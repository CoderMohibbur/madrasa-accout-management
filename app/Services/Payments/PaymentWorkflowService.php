<?php

namespace App\Services\Payments;

use App\Models\Payment;
use App\Models\Receipt;
use App\Models\StudentFeeInvoice;
use App\Models\Transactions;
use App\Models\TransactionsType;
use App\Models\User;
use App\Services\Finance\CanonicalPostingService;
use App\Services\Payments\Shurjopay\ShurjopayClient;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use RuntimeException;
use Throwable;

class PaymentWorkflowService
{
    public function __construct(
        private readonly StudentFeeInvoicePayableResolver $payableResolver,
        private readonly PaymentReferenceGenerator $referenceGenerator,
        private readonly PaymentEventLogger $eventLogger,
        private readonly PaymentAuditLogger $auditLogger,
        private readonly ShurjopayClient $shurjopayClient,
        private readonly CanonicalPostingService $canonicalPostingService,
    ) {
    }

    public function initiateShurjopay(User $user, StudentFeeInvoice $invoice): array
    {
        $resolvedPayable = $this->payableResolver->resolveForUser($user, $invoice);
        $existing = $this->findReusableShurjopayAttempt($invoice);

        if ($existing) {
            return [
                'payment' => $existing,
                'checkout_url' => data_get($existing->metadata, 'shurjopay.initiate_response.checkout_url'),
                'reused' => true,
            ];
        }

        $this->ensureNoActiveAttempt($invoice);

        $payment = DB::transaction(function () use ($resolvedPayable): Payment {
            $payment = Payment::query()->create([
                'user_id' => $resolvedPayable->user->getKey(),
                'payable_type' => StudentFeeInvoice::class,
                'payable_id' => $resolvedPayable->invoice->getKey(),
                'status' => Payment::STATUS_PENDING,
                'verification_status' => Payment::VERIFICATION_PENDING,
                'provider' => 'shurjopay',
                'provider_mode' => (string) config('payments.provider_mode', 'sandbox'),
                'currency' => $resolvedPayable->currency,
                'amount' => $resolvedPayable->amount,
                'idempotency_key' => $this->referenceGenerator->idempotencyKey('shurjopay'),
                'status_reason' => 'Preparing sandbox checkout request.',
                'metadata' => [
                    'resolved_payable' => $resolvedPayable->metadata,
                ],
            ]);

            $metadata = $payment->metadata ?? [];
            $metadata['merchant_order_id'] = $this->referenceGenerator->merchantOrderId($payment);
            $payment->forceFill(['metadata' => $metadata])->save();

            return $payment->fresh();
        });

        try {
            $gateway = $this->shurjopayClient->initiatePayment(
                $resolvedPayable,
                $payment,
                (string) data_get($payment->metadata, 'merchant_order_id')
            );
        } catch (Throwable $exception) {
            $before = $payment->only(['status', 'status_reason', 'provider_reference', 'failed_at']);

            $payment->forceFill([
                'status' => Payment::STATUS_FAILED,
                'verification_status' => Payment::VERIFICATION_FAILED,
                'status_reason' => $exception->getMessage(),
                'failed_at' => now(),
            ])->save();

            $this->eventLogger->record(
                $payment,
                'shurjopay',
                'initiate_error',
                [
                    'message' => $exception->getMessage(),
                ],
                'initiate',
                null,
                null,
                'failed'
            );

            $this->auditLogger->record(
                $user,
                $payment,
                'payment.initiation_failed',
                'Sandbox shurjoPay initiation failed.',
                $before,
                $payment->only(['status', 'status_reason', 'provider_reference', 'failed_at']),
                [
                    'provider' => 'shurjopay',
                    'mode' => config('payments.provider_mode'),
                ]
            );

            throw ValidationException::withMessages([
                'payment' => [$exception->getMessage()],
            ]);
        }

        $before = $payment->only(['status', 'status_reason', 'provider_reference', 'initiated_at']);
        $metadata = $payment->metadata ?? [];
        $metadata['shurjopay'] = [
            'auth_response' => $gateway['auth'],
            'initiate_request' => $gateway['request'],
            'initiate_response' => $gateway['response'],
        ];

        $payment->forceFill([
            'status' => Payment::STATUS_REDIRECT_PENDING,
            'verification_status' => Payment::VERIFICATION_PENDING,
            'status_reason' => 'Waiting for verified shurjoPay callback or return.',
            'provider_reference' => $gateway['provider_order_id'],
            'initiated_at' => now(),
            'metadata' => $metadata,
        ])->save();

        $this->eventLogger->record(
            $payment,
            'shurjopay',
            'initiate_response',
            $gateway,
            'initiate',
            $gateway['provider_order_id'],
            $gateway['http_status'],
            'accepted',
            true
        );

        $this->auditLogger->record(
            $user,
            $payment,
            'payment.initiated',
            'Sandbox shurjoPay payment attempt created.',
            $before,
            $payment->only(['status', 'status_reason', 'provider_reference', 'initiated_at']),
            [
                'provider' => 'shurjopay',
                'mode' => config('payments.provider_mode'),
                'invoice_id' => $invoice->getKey(),
                'merchant_order_id' => data_get($payment->metadata, 'merchant_order_id'),
            ]
        );

        return [
            'payment' => $payment->fresh(['payable.student', 'receipt']),
            'checkout_url' => $gateway['checkout_url'],
            'reused' => false,
        ];
    }

    public function handleShurjopaySuccessReturn(User $user, array $input): PaymentFlowResult
    {
        return $this->handleShurjopayVerificationFlow($user, $input, 'return_success', false);
    }

    public function handleShurjopayFailureReturn(User $user, array $input): PaymentFlowResult
    {
        return $this->handleShurjopayVerificationFlow($user, $input, 'return_fail', true);
    }

    public function handleShurjopayCancellation(User $user, array $input): PaymentFlowResult
    {
        $providerOrderId = trim((string) ($input['order_id'] ?? ''));

        if ($providerOrderId === '') {
            return new PaymentFlowResult(
                status: 'cancelled',
                payment: null,
                message: 'The payment was cancelled before shurjoPay returned an order id.'
            );
        }

        $payment = Payment::query()
            ->where('provider', 'shurjopay')
            ->where('provider_reference', $providerOrderId)
            ->with(['payable.student', 'receipt', 'reviewer'])
            ->first();

        if (! $payment) {
            return new PaymentFlowResult(
                status: 'manual_review',
                payment: null,
                message: 'Cancellation reached Laravel, but no local sandbox payment matched the shurjoPay order id.'
            );
        }

        $this->authorizeView($user, $payment);

        $this->eventLogger->record(
            $payment,
            'shurjopay',
            'browser_cancel',
            [
                'query' => $input,
            ],
            'return',
            $providerOrderId,
            null,
            'received'
        );

        if ($payment->isTerminal()) {
            return new PaymentFlowResult(
                status: $payment->status,
                payment: $payment,
                message: 'The payment was already in a terminal state before the browser cancellation return arrived.'
            );
        }

        $before = $payment->only(['status', 'verification_status', 'status_reason', 'cancelled_at']);
        $metadata = $payment->metadata ?? [];
        $metadata['browser_cancelled_at'] = now()->toIso8601String();

        $payment->forceFill([
            'status' => Payment::STATUS_CANCELLED,
            'verification_status' => Payment::VERIFICATION_PENDING,
            'status_reason' => 'Customer cancelled at the shurjoPay checkout screen. Awaiting any later provider confirmation.',
            'cancelled_at' => now(),
            'metadata' => $metadata,
        ])->save();

        $this->auditLogger->record(
            $user,
            $payment,
            'payment.cancelled',
            'Browser cancellation was recorded for the sandbox shurjoPay payment.',
            $before,
            $payment->only(['status', 'verification_status', 'status_reason', 'cancelled_at']),
            [
                'provider' => 'shurjopay',
                'order_id' => $providerOrderId,
            ]
        );

        return new PaymentFlowResult(
            status: 'cancelled',
            payment: $payment->fresh(['payable.student', 'receipt', 'reviewer']),
            message: 'The payment was cancelled in the browser. No receipt or posting was created.'
        );
    }

    public function handleShurjopayIpn(array $input): PaymentFlowResult
    {
        return $this->handleShurjopayVerificationFlow(null, $input, 'ipn', false);
    }

    public function submitManualBankRequest(User $user, StudentFeeInvoice $invoice, array $evidence): Payment
    {
        if (! config('payments.manual_bank.enabled', true)) {
            throw ValidationException::withMessages([
                'payment' => ['Manual bank submission is currently disabled.'],
            ]);
        }

        $resolvedPayable = $this->payableResolver->resolveForUser($user, $invoice);
        $this->ensureNoActiveAttempt($invoice);

        $existing = Payment::query()
            ->where('provider', 'manual_bank')
            ->where('provider_reference', $evidence['bank_reference'])
            ->where('user_id', $user->getKey())
            ->where('payable_type', StudentFeeInvoice::class)
            ->where('payable_id', $invoice->getKey())
            ->first();

        if ($existing && $existing->status === Payment::STATUS_PAID) {
            throw ValidationException::withMessages([
                'bank_reference' => ['This bank reference is already attached to a paid record.'],
            ]);
        }

        $payment = $existing ?: Payment::query()->create([
            'user_id' => $user->getKey(),
            'payable_type' => StudentFeeInvoice::class,
            'payable_id' => $invoice->getKey(),
            'provider' => 'manual_bank',
            'provider_mode' => (string) config('payments.provider_mode', 'sandbox'),
            'currency' => $resolvedPayable->currency,
            'amount' => $resolvedPayable->amount,
            'idempotency_key' => $this->referenceGenerator->idempotencyKey('manual-bank'),
        ]);

        $before = $payment->only(['status', 'verification_status', 'status_reason', 'provider_reference']);
        $metadata = $payment->metadata ?? [];
        $metadata['manual_bank'] = [
            'payer_name' => $evidence['payer_name'],
            'payment_channel' => $evidence['payment_channel'] ?? config('payments.manual_bank.display_name'),
            'transferred_at' => $evidence['transferred_at'],
            'evidence_url' => $evidence['evidence_url'] ?? null,
            'note' => $evidence['note'] ?? null,
            'submitted_by_user_id' => $user->getKey(),
        ];
        $metadata['resolved_payable'] = $resolvedPayable->metadata;

        $payment->forceFill([
            'status' => Payment::STATUS_AWAITING_MANUAL_PAYMENT,
            'verification_status' => Payment::VERIFICATION_PENDING,
            'status_reason' => 'Awaiting management review of the submitted bank transfer evidence.',
            'provider_reference' => $evidence['bank_reference'],
            'initiated_at' => now(),
            'metadata' => $metadata,
            'failed_at' => null,
            'cancelled_at' => null,
        ])->save();

        $this->eventLogger->record(
            $payment,
            'manual_bank',
            'manual_bank_submitted',
            [
                'evidence' => Arr::except($metadata['manual_bank'], ['note']),
            ],
            'manual_bank',
            $payment->provider_reference,
            null,
            'received'
        );

        $this->auditLogger->record(
            $user,
            $payment,
            'payment.manual_bank_submitted',
            'Manual bank evidence was submitted for invoice payment review.',
            $before,
            $payment->only(['status', 'verification_status', 'status_reason', 'provider_reference']),
            [
                'invoice_id' => $invoice->getKey(),
                'bank_reference' => $payment->provider_reference,
            ]
        );

        return $payment->fresh(['payable.student', 'receipt', 'reviewer']);
    }

    public function approveManualBank(User $manager, Payment $payment, array $reviewData): Payment
    {
        $this->authorizeManagement($manager);

        if ($payment->provider !== 'manual_bank') {
            throw new RuntimeException('Only manual bank payments can be approved from this queue.');
        }

        if ($payment->status === Payment::STATUS_PAID) {
            return $payment->fresh(['payable.student', 'receipt', 'reviewer']);
        }

        $metadata = $payment->metadata ?? [];
        $metadata['manual_bank_review'] = [
            'decision' => 'approved',
            'decision_note' => $reviewData['decision_note'] ?? null,
            'matched_bank_reference' => $reviewData['matched_bank_reference'] ?? $payment->provider_reference,
            'reviewed_by_user_id' => $manager->getKey(),
            'reviewed_at' => now()->toIso8601String(),
        ];

        $payment->forceFill([
            'reviewed_by_user_id' => $manager->getKey(),
            'reviewed_at' => now(),
            'metadata' => $metadata,
        ])->save();

        $this->eventLogger->record(
            $payment,
            'manual_bank',
            'manual_bank_approved',
            [
                'review' => $metadata['manual_bank_review'],
            ],
            'management_review',
            $payment->provider_reference,
            null,
            'verified',
            true
        );

        return $this->finalizeAsPaid(
            $payment,
            actor: $manager,
            verificationStatus: Payment::VERIFICATION_VERIFIED,
            statusReason: 'Manual bank transfer was approved by management.',
            metadataPatch: [
                'manual_bank_approved' => true,
            ],
            verificationData: [
                'matched_bank_reference' => $reviewData['matched_bank_reference'] ?? $payment->provider_reference,
            ]
        );
    }

    public function rejectManualBank(User $manager, Payment $payment, array $reviewData): Payment
    {
        $this->authorizeManagement($manager);

        if ($payment->provider !== 'manual_bank') {
            throw new RuntimeException('Only manual bank payments can be rejected from this queue.');
        }

        $before = $payment->only(['status', 'verification_status', 'status_reason', 'failed_at', 'reviewed_at']);
        $metadata = $payment->metadata ?? [];
        $metadata['manual_bank_review'] = [
            'decision' => 'rejected',
            'decision_note' => $reviewData['decision_note'] ?? null,
            'matched_bank_reference' => $reviewData['matched_bank_reference'] ?? $payment->provider_reference,
            'reviewed_by_user_id' => $manager->getKey(),
            'reviewed_at' => now()->toIso8601String(),
        ];

        $payment->forceFill([
            'status' => Payment::STATUS_FAILED,
            'verification_status' => Payment::VERIFICATION_FAILED,
            'status_reason' => $reviewData['decision_note'] ?: 'Manual bank evidence was rejected by management.',
            'failed_at' => now(),
            'reviewed_by_user_id' => $manager->getKey(),
            'reviewed_at' => now(),
            'metadata' => $metadata,
        ])->save();

        $this->eventLogger->record(
            $payment,
            'manual_bank',
            'manual_bank_rejected',
            [
                'review' => $metadata['manual_bank_review'],
            ],
            'management_review',
            $payment->provider_reference,
            null,
            'failed',
            true
        );

        $this->auditLogger->record(
            $manager,
            $payment,
            'payment.manual_bank_rejected',
            'Manual bank payment was rejected by management.',
            $before,
            $payment->only(['status', 'verification_status', 'status_reason', 'failed_at', 'reviewed_at']),
            [
                'bank_reference' => $payment->provider_reference,
            ]
        );

        return $payment->fresh(['payable.student', 'receipt', 'reviewer']);
    }

    private function handleShurjopayVerificationFlow(?User $user, array $input, string $requestSource, bool $preferFailurePath): PaymentFlowResult
    {
        $providerOrderId = trim((string) ($input['order_id'] ?? ''));

        if ($providerOrderId === '') {
            return new PaymentFlowResult(
                status: $preferFailurePath ? 'failed' : 'manual_review',
                payment: null,
                message: 'shurjoPay did not supply an order id to Laravel.'
            );
        }

        try {
            $verification = $this->verifyShurjopayOrder($providerOrderId);
            $record = $verification['record'];
            $payment = $this->locatePaymentFromVerification($providerOrderId, $record);
        } catch (Throwable $exception) {
            return $this->handleVerificationException($user, $input, $requestSource, $providerOrderId, $exception);
        }

        $this->eventLogger->record(
            $payment,
            'shurjopay',
            $requestSource === 'ipn' ? 'ipn_received' : 'browser_return',
            [
                'source' => $requestSource,
                'query' => $input,
            ],
            $requestSource === 'ipn' ? 'ipn' : 'return',
            $providerOrderId,
            null,
            'received'
        );

        $this->eventLogger->record(
            $payment,
            'shurjopay',
            'verification_result',
            $verification,
            'verify',
            $providerOrderId,
            $verification['http_status'],
            'processed',
            true
        );

        if (! $payment) {
            return new PaymentFlowResult(
                status: 'manual_review',
                payment: null,
                message: 'Verification succeeded, but Laravel could not safely match the shurjoPay order id to a local payment row.',
                context: ['provider_order_id' => $providerOrderId]
            );
        }

        if ($user) {
            $this->authorizeView($user, $payment);
        }

        $classification = $this->classifyVerificationRecord($record);

        if ($classification === 'success') {
            if (! $this->verificationMatchesLocalPayment($payment, $record)) {
                return $this->markManualReview(
                    $payment,
                    $user,
                    'shurjoPay verification data did not match the local invoice amount or merchant order reference.',
                    [
                        'provider_order_id' => $providerOrderId,
                        'verification_record' => $record,
                    ]
                );
            }

            if ($payment->status === Payment::STATUS_CANCELLED) {
                return $this->markManualReview(
                    $payment,
                    $user,
                    'A successful shurjoPay verification arrived after the browser cancellation flow. Manual reconciliation is required.',
                    [
                        'provider_order_id' => $providerOrderId,
                        'verification_record' => $record,
                    ]
                );
            }

            $payment = $this->finalizeAsPaid(
                $payment,
                actor: $user,
                verificationStatus: Payment::VERIFICATION_VERIFIED,
                statusReason: 'Verified by the shurjoPay server-side verification API.',
                metadataPatch: [
                    'shurjopay_verified' => true,
                ],
                verificationData: $record
            );

            return new PaymentFlowResult(
                status: 'paid',
                payment: $payment,
                message: 'The sandbox shurjoPay payment was verified and finalized.'
            );
        }

        if ($classification === 'failed') {
            $before = $payment->only(['status', 'verification_status', 'status_reason', 'failed_at']);
            $payment->forceFill([
                'status' => $preferFailurePath ? Payment::STATUS_FAILED : Payment::STATUS_PENDING_VERIFICATION,
                'verification_status' => $preferFailurePath ? Payment::VERIFICATION_FAILED : Payment::VERIFICATION_PENDING,
                'status_reason' => $preferFailurePath
                    ? 'shurjoPay verification reported a non-successful payment outcome.'
                    : 'shurjoPay has not yet confirmed a successful payment outcome.',
                'failed_at' => $preferFailurePath ? now() : null,
                'metadata' => $this->mergeMetadata($payment->metadata, [
                    'last_verification' => $record,
                ]),
            ])->save();

            $this->auditLogger->record(
                $user,
                $payment,
                $preferFailurePath ? 'payment.failed' : 'payment.pending_verification',
                $preferFailurePath
                    ? 'The sandbox shurjoPay payment was marked failed after verification.'
                    : 'The sandbox shurjoPay payment remains pending verification.',
                $before,
                $payment->only(['status', 'verification_status', 'status_reason', 'failed_at']),
                [
                    'provider_order_id' => $providerOrderId,
                ]
            );

            return new PaymentFlowResult(
                status: $preferFailurePath ? 'failed' : 'pending',
                payment: $payment->fresh(['payable.student', 'receipt', 'reviewer']),
                message: $preferFailurePath
                    ? 'The payment was not verified as successful by shurjoPay.'
                    : 'The payment is still waiting for a successful shurjoPay verification result.'
            );
        }

        $before = $payment->only(['status', 'verification_status', 'status_reason']);
        $payment->forceFill([
            'status' => Payment::STATUS_PENDING_VERIFICATION,
            'verification_status' => Payment::VERIFICATION_PENDING,
            'status_reason' => 'shurjoPay verification returned an ambiguous response. Manual review may be required.',
            'metadata' => $this->mergeMetadata($payment->metadata, [
                'last_verification' => $record,
            ]),
        ])->save();

        $this->auditLogger->record(
            $user,
            $payment,
            'payment.pending_verification',
            'The payment remains pending because shurjoPay returned an ambiguous verification response.',
            $before,
            $payment->only(['status', 'verification_status', 'status_reason']),
            [
                'provider_order_id' => $providerOrderId,
            ]
        );

        return new PaymentFlowResult(
            status: 'pending',
            payment: $payment->fresh(['payable.student', 'receipt', 'reviewer']),
            message: 'The payment has not been authoritatively verified yet. No receipt or ledger posting was created.'
        );
    }

    private function verifyShurjopayOrder(string $providerOrderId): array
    {
        $verification = $this->shurjopayClient->verifyPayment($providerOrderId);
        $record = $verification['response'];

        if (isset($record[0]) && is_array($record[0])) {
            $record = $record[0];
        }

        return [
            'http_status' => $verification['http_status'],
            'auth' => $verification['auth'],
            'record' => is_array($record) ? $record : [],
            'response' => $verification['response'],
        ];
    }

    private function handleVerificationException(
        ?User $user,
        array $input,
        string $requestSource,
        string $providerOrderId,
        Throwable $exception,
    ): PaymentFlowResult {
        $payment = Payment::query()
            ->where('provider', 'shurjopay')
            ->where('provider_reference', $providerOrderId)
            ->with(['payable.student', 'receipt', 'reviewer'])
            ->first();

        if ($user && $payment) {
            $this->authorizeView($user, $payment);
        }

        $this->eventLogger->record(
            $payment,
            'shurjopay',
            'verification_error',
            [
                'source' => $requestSource,
                'query' => $input,
                'message' => $exception->getMessage(),
            ],
            'verify',
            $providerOrderId,
            null,
            'failed'
        );

        if (! $payment) {
            return new PaymentFlowResult(
                status: 'manual_review',
                payment: null,
                message: 'shurjoPay verification could not be completed and Laravel could not match the order id to a local payment.',
                context: [
                    'provider_order_id' => $providerOrderId,
                    'verification_error' => $exception->getMessage(),
                ]
            );
        }

        return $this->markManualReview(
            $payment,
            $user,
            'shurjoPay verification could not be completed. Manual review is required before treating this payment as settled.',
            [
                'provider_order_id' => $providerOrderId,
                'verification_error' => $exception->getMessage(),
            ]
        );
    }

    private function locatePaymentFromVerification(string $providerOrderId, array $record): ?Payment
    {
        $payment = Payment::query()
            ->where('provider', 'shurjopay')
            ->where('provider_reference', $providerOrderId)
            ->with(['payable.student', 'receipt', 'reviewer'])
            ->first();

        if ($payment) {
            return $payment;
        }

        $localPaymentId = (int) ($record['value1'] ?? 0);

        if ($localPaymentId <= 0) {
            return null;
        }

        $candidate = Payment::query()
            ->whereKey($localPaymentId)
            ->where('provider', 'shurjopay')
            ->with(['payable.student', 'receipt', 'reviewer'])
            ->first();

        if (! $candidate) {
            return null;
        }

        $merchantOrderId = data_get($candidate->metadata, 'merchant_order_id');
        $customerOrderId = $record['customer_order_id'] ?? $record['order_id'] ?? null;

        if ($merchantOrderId && $customerOrderId && $merchantOrderId !== $customerOrderId) {
            return null;
        }

        return $candidate;
    }

    private function classifyVerificationRecord(array $record): string
    {
        $bankStatus = strtolower(trim((string) ($record['bank_status'] ?? '')));
        $spMessage = strtolower(trim((string) ($record['sp_message'] ?? $record['message'] ?? '')));
        $spCode = (string) ($record['sp_code'] ?? '');

        if ($bankStatus === 'success' || ($spCode === '1000' && $bankStatus !== 'failed' && $bankStatus !== 'cancelled' && $bankStatus !== 'canceled')) {
            return 'success';
        }

        if (in_array($bankStatus, ['failed', 'cancelled', 'canceled', 'declined'], true)) {
            return 'failed';
        }

        if (str_contains($spMessage, 'fail') || str_contains($spMessage, 'cancel') || str_contains($spMessage, 'declin')) {
            return 'failed';
        }

        return 'pending';
    }

    private function verificationMatchesLocalPayment(Payment $payment, array $record): bool
    {
        $verifiedAmount = (float) ($record['amount'] ?? $payment->amount);
        $verifiedCurrency = (string) ($record['currency'] ?? $payment->currency);
        $customerOrderId = (string) ($record['customer_order_id'] ?? $record['order_id'] ?? '');
        $merchantOrderId = (string) data_get($payment->metadata, 'merchant_order_id', '');

        if (abs($verifiedAmount - (float) $payment->amount) > 0.01) {
            return false;
        }

        if ($verifiedCurrency !== '' && $verifiedCurrency !== (string) $payment->currency) {
            return false;
        }

        if ($merchantOrderId !== '' && $customerOrderId !== '' && $merchantOrderId !== $customerOrderId) {
            return false;
        }

        return true;
    }

    private function finalizeAsPaid(
        Payment $payment,
        ?User $actor,
        string $verificationStatus,
        string $statusReason,
        array $metadataPatch = [],
        array $verificationData = [],
    ): Payment {
        return DB::transaction(function () use ($payment, $actor, $verificationStatus, $statusReason, $metadataPatch, $verificationData): Payment {
            /** @var Payment $lockedPayment */
            $lockedPayment = Payment::query()
                ->whereKey($payment->getKey())
                ->with(['payable.student', 'receipt', 'reviewer'])
                ->lockForUpdate()
                ->firstOrFail();

            if ($lockedPayment->status === Payment::STATUS_PAID) {
                return $lockedPayment;
            }

            $invoice = $lockedPayment->payable;

            if (! $invoice instanceof StudentFeeInvoice) {
                throw new RuntimeException('Only student fee invoice payments are supported in this phase.');
            }

            if ((float) $invoice->balance_amount <= 0) {
                throw new RuntimeException('The linked invoice already has no remaining balance.');
            }

            $beforePayment = $lockedPayment->only([
                'status',
                'verification_status',
                'status_reason',
                'paid_at',
                'verified_at',
                'posted_transaction_id',
            ]);
            $beforeInvoice = $invoice->only(['status', 'paid_amount', 'balance_amount']);

            $newPaidAmount = min((float) $invoice->total_amount, (float) $invoice->paid_amount + (float) $lockedPayment->amount);
            $newBalance = max(0, (float) $invoice->total_amount - $newPaidAmount);

            $invoice->forceFill([
                'paid_amount' => $newPaidAmount,
                'balance_amount' => $newBalance,
                'status' => $newBalance <= 0 ? 'paid' : 'partial',
            ])->save();

            $metadata = $this->mergeMetadata($lockedPayment->metadata, [
                'verification_data' => $verificationData,
                'finalization' => array_merge($metadataPatch, [
                    'verified_at' => now()->toIso8601String(),
                ]),
            ]);

            $lockedPayment->forceFill([
                'status' => Payment::STATUS_PAID,
                'verification_status' => $verificationStatus,
                'status_reason' => $statusReason,
                'paid_at' => now(),
                'verified_at' => now(),
                'failed_at' => null,
                'cancelled_at' => null,
                'metadata' => $metadata,
            ])->save();

            $receipt = $lockedPayment->receipt ?: Receipt::query()->create([
                'payment_id' => $lockedPayment->getKey(),
                'issued_to_user_id' => $lockedPayment->user_id,
                'receipt_number' => $this->referenceGenerator->receiptNumber($lockedPayment),
                'currency' => $lockedPayment->currency,
                'amount' => $lockedPayment->amount,
                'issued_at' => now(),
                'metadata' => [
                    'provider' => $lockedPayment->provider,
                    'provider_mode' => $lockedPayment->provider_mode,
                ],
            ]);

            $this->auditLogger->record(
                $actor,
                $lockedPayment,
                'payment.paid',
                'Payment was finalized as paid.',
                $beforePayment,
                $lockedPayment->only([
                    'status',
                    'verification_status',
                    'status_reason',
                    'paid_at',
                    'verified_at',
                    'posted_transaction_id',
                ]),
                [
                    'receipt_id' => $receipt->getKey(),
                    'provider' => $lockedPayment->provider,
                ]
            );

            $this->auditLogger->record(
                $actor,
                $invoice,
                'invoice.payment_applied',
                'Invoice balance was updated after payment finalization.',
                $beforeInvoice,
                $invoice->only(['status', 'paid_amount', 'balance_amount']),
                [
                    'payment_id' => $lockedPayment->getKey(),
                    'receipt_id' => $receipt->getKey(),
                ]
            );

            $this->auditLogger->record(
                $actor,
                $receipt,
                'receipt.issued',
                'A dedicated receipt row was issued for the finalized payment.',
                [],
                $receipt->only(['receipt_number', 'amount', 'issued_at']),
                [
                    'payment_id' => $lockedPayment->getKey(),
                ]
            );

            $transaction = $this->maybePostStudentFeeTransaction($lockedPayment, $invoice, $receipt, $actor);

            if ($transaction) {
                $lockedPayment->forceFill([
                    'posted_transaction_id' => $transaction->getKey(),
                    'metadata' => $this->mergeMetadata($lockedPayment->metadata, [
                        'posting' => [
                            'status' => 'posted',
                            'transaction_id' => $transaction->getKey(),
                        ],
                    ]),
                ])->save();
            }

            if (! $transaction) {
                $lockedPayment->forceFill([
                    'metadata' => $this->mergeMetadata($lockedPayment->metadata, [
                        'posting' => [
                            'status' => 'skipped',
                            'reason' => 'Canonical posting is disabled for sandbox Phase 5 until an operator account mapping is approved.',
                        ],
                    ]),
                ])->save();
            }

            return $lockedPayment->fresh(['payable.student', 'receipt', 'reviewer']);
        });
    }

    private function maybePostStudentFeeTransaction(
        Payment $payment,
        StudentFeeInvoice $invoice,
        Receipt $receipt,
        ?User $actor,
    ): ?Transactions {
        if (! config('payments.posting.student_fee.enabled', false)) {
            return null;
        }

        $typeKey = (string) config('payments.posting.student_fee.transaction_type_key', 'student_fee');
        $typeId = TransactionsType::idByKey($typeKey);
        $payload = $this->canonicalPostingService->build(
            typeKey: $typeKey,
            amount: (float) $payment->amount,
            sourceType: 'student_fee_invoice',
            sourceId: $invoice->getKey(),
            reference: $receipt->receipt_number,
            metadata: [
                'payment_id' => $payment->getKey(),
                'receipt_id' => $receipt->getKey(),
            ]
        );

        return Transactions::query()->create([
            'student_id' => $invoice->student_id,
            'transactions_type_id' => $typeId,
            'student_book_number' => $invoice->invoice_number,
            'recipt_no' => $receipt->receipt_number,
            'total_fees' => $payment->amount,
            'debit' => $payload->debit,
            'credit' => $payload->credit,
            'transactions_date' => now()->toDateString(),
            'account_id' => config('payments.posting.student_fee.account_id'),
            'created_by_id' => $actor?->getKey() ?: $payment->user_id,
            'class_id' => $invoice->student?->class_id,
            'section_id' => $invoice->student?->section_id,
            'academic_year_id' => $invoice->student?->academic_year_id,
            'roll' => $invoice->student?->roll,
            'fess_type_id' => $invoice->student?->fees_type_id,
            'c_i_1' => $invoice->student?->roll,
            'c_s_1' => 'Student Fee Payment',
            'note' => 'Phase 5 payment finalization',
            'isActived' => true,
            'isDeleted' => false,
        ]);
    }

    private function markManualReview(
        Payment $payment,
        ?User $actor,
        string $reason,
        array $context = [],
    ): PaymentFlowResult {
        $before = $payment->only(['status', 'verification_status', 'status_reason']);

        $payment->forceFill([
            'status' => Payment::STATUS_MANUAL_REVIEW,
            'verification_status' => Payment::VERIFICATION_MANUAL_REVIEW,
            'status_reason' => $reason,
            'metadata' => $this->mergeMetadata($payment->metadata, [
                'manual_review_context' => $context,
            ]),
        ])->save();

        $this->auditLogger->record(
            $actor,
            $payment,
            'payment.manual_review',
            'Payment was routed to manual review.',
            $before,
            $payment->only(['status', 'verification_status', 'status_reason']),
            $context
        );

        return new PaymentFlowResult(
            status: 'manual_review',
            payment: $payment->fresh(['payable.student', 'receipt', 'reviewer']),
            message: $reason,
            context: $context
        );
    }

    private function findReusableShurjopayAttempt(StudentFeeInvoice $invoice): ?Payment
    {
        $payment = Payment::query()
            ->where('provider', 'shurjopay')
            ->where('payable_type', StudentFeeInvoice::class)
            ->where('payable_id', $invoice->getKey())
            ->whereIn('status', [
                Payment::STATUS_PENDING,
                Payment::STATUS_REDIRECT_PENDING,
                Payment::STATUS_PENDING_VERIFICATION,
            ])
            ->latest('id')
            ->first();

        if (! $payment) {
            return null;
        }

        if (data_get($payment->metadata, 'shurjopay.initiate_response.checkout_url')) {
            return $payment->fresh(['payable.student', 'receipt', 'reviewer']);
        }

        return null;
    }

    private function ensureNoActiveAttempt(StudentFeeInvoice $invoice): void
    {
        $activeAttempt = Payment::query()
            ->where('payable_type', StudentFeeInvoice::class)
            ->where('payable_id', $invoice->getKey())
            ->whereIn('status', Payment::ACTIVE_STATUSES)
            ->latest('id')
            ->first();

        if ($activeAttempt) {
            throw ValidationException::withMessages([
                'invoice_id' => ['An active payment attempt already exists for this invoice. Review or finish that attempt before creating another one.'],
            ]);
        }
    }

    private function authorizeView(User $user, Payment $payment): void
    {
        if ($user->hasRole('management')) {
            return;
        }

        if ((int) $payment->user_id === (int) $user->getKey()) {
            return;
        }

        $invoice = $payment->payable;

        if ($invoice instanceof StudentFeeInvoice && (int) $invoice->guardian?->user_id === (int) $user->getKey()) {
            return;
        }

        throw new AuthorizationException('You are not allowed to view this payment.');
    }

    private function authorizeManagement(User $user): void
    {
        if (! $user->hasRole('management')) {
            throw new AuthorizationException('Management access is required.');
        }
    }

    private function mergeMetadata(?array $existing, array $patch): array
    {
        return array_replace_recursive($existing ?? [], $patch);
    }
}
