<?php

namespace App\Services\Donations;

use App\Models\DonationCategory;
use App\Models\DonationIntent;
use App\Models\DonationRecord;
use App\Models\Payment;
use App\Models\Receipt;
use App\Models\User;
use App\Services\Payments\PaymentAuditLogger;
use App\Services\Payments\PaymentEventLogger;
use App\Services\Payments\PaymentFlowResult;
use App\Services\Payments\PaymentReferenceGenerator;
use App\Services\Payments\Shurjopay\ShurjopayClient;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use RuntimeException;
use Throwable;

class DonationCheckoutService
{
    public const CURRENT_INTENT_SESSION_KEY = 'donations.current_intent_public_reference';
    public const ACCESS_KEYS_SESSION_KEY = 'donations.access_keys';

    public function __construct(
        private readonly DonationReferenceGenerator $donationReferenceGenerator,
        private readonly PaymentReferenceGenerator $paymentReferenceGenerator,
        private readonly PaymentEventLogger $paymentEventLogger,
        private readonly PaymentAuditLogger $paymentAuditLogger,
        private readonly ShurjopayClient $shurjopayClient,
    ) {
    }

    public function beginCheckout(Request $request, array $draft): array
    {
        $user = $request->user();
        $donorMode = $user ? DonationIntent::DONOR_MODE_IDENTIFIED : DonationIntent::DONOR_MODE_GUEST;
        $intent = $this->findReusableIntent($request, $donorMode, $user);
        $plainAccessKey = $this->sessionAccessKey($request, $intent?->public_reference);

        if (! $intent) {
            $plainAccessKey = $plainAccessKey ?: $this->donationReferenceGenerator->accessKey();
            $intent = $this->createIntent($request, $draft, $donorMode, $user, $plainAccessKey);
        }

        $this->storeAccessKey($request, $intent->public_reference, $plainAccessKey);
        $request->session()->put(self::CURRENT_INTENT_SESSION_KEY, $intent->public_reference);
        $this->ensureNoActiveAttempt($intent);

        $payment = Payment::query()->create([
            'user_id' => $user?->getKey(),
            'payable_type' => DonationIntent::class,
            'payable_id' => $intent->getKey(),
            'status' => Payment::STATUS_PENDING,
            'verification_status' => Payment::VERIFICATION_PENDING,
            'provider' => 'shurjopay',
            'provider_mode' => (string) config('payments.provider_mode', 'sandbox'),
            'currency' => $intent->currency,
            'amount' => $intent->amount,
            'idempotency_key' => $this->paymentReferenceGenerator->idempotencyKey('shurjopay'),
            'status_reason' => 'Preparing donor checkout request.',
            'metadata' => [
                'donation_intent' => [
                    'public_reference' => $intent->public_reference,
                    'donor_mode' => $intent->donor_mode,
                    'display_mode' => $intent->display_mode,
                ],
            ],
        ]);

        $merchantOrderId = $this->paymentReferenceGenerator->merchantOrderId($payment, 'DON');
        $payment->forceFill([
            'metadata' => $this->mergeMetadata($payment->metadata, [
                'merchant_order_id' => $merchantOrderId,
            ]),
        ])->save();

        try {
            $gateway = $this->shurjopayClient->initiateCheckout(
                $this->buildCheckoutPayload($request, $intent, $payment, $merchantOrderId)
            );
        } catch (Throwable $exception) {
            $before = $payment->only(['status', 'verification_status', 'status_reason', 'provider_reference', 'failed_at']);

            $payment->forceFill([
                'status' => Payment::STATUS_FAILED,
                'verification_status' => Payment::VERIFICATION_FAILED,
                'status_reason' => $exception->getMessage(),
                'failed_at' => now(),
            ])->save();

            $this->paymentEventLogger->record(
                $payment,
                'shurjopay',
                'donation_initiate_error',
                [
                    'public_reference' => $intent->public_reference,
                    'message' => $exception->getMessage(),
                ],
                'initiate',
                null,
                null,
                'failed'
            );

            $this->paymentAuditLogger->record(
                $user,
                $payment,
                'donation_payment.initiation_failed',
                'Donor checkout initiation failed.',
                $before,
                $payment->only(['status', 'verification_status', 'status_reason', 'provider_reference', 'failed_at']),
                [
                    'public_reference' => $intent->public_reference,
                    'provider' => 'shurjopay',
                ]
            );

            throw ValidationException::withMessages([
                'payment' => [$exception->getMessage()],
            ]);
        }

        $before = $payment->only(['status', 'verification_status', 'status_reason', 'provider_reference', 'initiated_at']);

        $payment->forceFill([
            'status' => Payment::STATUS_REDIRECT_PENDING,
            'verification_status' => Payment::VERIFICATION_PENDING,
            'status_reason' => 'Waiting for verified donor checkout confirmation from shurjoPay.',
            'provider_reference' => $gateway['provider_order_id'],
            'initiated_at' => now(),
            'metadata' => $this->mergeMetadata($payment->metadata, [
                'shurjopay' => [
                    'auth_response' => $gateway['auth'],
                    'initiate_request' => $gateway['request'],
                    'initiate_response' => $gateway['response'],
                ],
            ]),
        ])->save();

        $this->paymentEventLogger->record(
            $payment,
            'shurjopay',
            'donation_initiate_response',
            $gateway,
            'initiate',
            $gateway['provider_order_id'],
            $gateway['http_status'],
            'accepted',
            true
        );

        $this->paymentAuditLogger->record(
            $user,
            $payment,
            'donation_payment.initiated',
            'Donor checkout payment attempt was created.',
            $before,
            $payment->only(['status', 'verification_status', 'status_reason', 'provider_reference', 'initiated_at']),
            [
                'public_reference' => $intent->public_reference,
                'provider' => 'shurjopay',
                'merchant_order_id' => $merchantOrderId,
            ]
        );

        return [
            'intent' => $intent->fresh(['donationRecord', 'latestPayment']),
            'payment' => $payment->fresh($this->paymentRelations()),
            'checkout_url' => $gateway['checkout_url'],
            'access_key' => $plainAccessKey,
        ];
    }

    public function handleShurjopaySuccessReturn(?User $user, array $input): PaymentFlowResult
    {
        return $this->handleShurjopayVerificationFlow($user, $input, 'return_success', false);
    }

    public function handleShurjopayFailureReturn(?User $user, array $input): PaymentFlowResult
    {
        return $this->handleShurjopayVerificationFlow($user, $input, 'return_fail', true);
    }

    public function handleShurjopayCancellation(?User $user, array $input): PaymentFlowResult
    {
        $providerOrderId = trim((string) ($input['order_id'] ?? ''));

        if ($providerOrderId === '') {
            return new PaymentFlowResult(
                status: 'cancelled',
                payment: null,
                message: 'The donor checkout was cancelled before shurjoPay returned an order id.'
            );
        }

        $payment = $this->findPaymentByProviderOrder($providerOrderId);

        if (! $payment) {
            return new PaymentFlowResult(
                status: 'manual_review',
                payment: null,
                message: 'Cancellation reached Laravel, but no donor checkout matched the shurjoPay order id.'
            );
        }

        $this->paymentEventLogger->record(
            $payment,
            'shurjopay',
            'donation_browser_cancel',
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
                message: 'The donor payment attempt was already in a terminal state before browser cancellation returned.'
            );
        }

        $before = $payment->only(['status', 'verification_status', 'status_reason', 'cancelled_at']);

        $payment->forceFill([
            'status' => Payment::STATUS_CANCELLED,
            'verification_status' => Payment::VERIFICATION_PENDING,
            'status_reason' => 'Customer cancelled at the shurjoPay checkout screen. Waiting for any later provider confirmation.',
            'cancelled_at' => now(),
            'metadata' => $this->mergeMetadata($payment->metadata, [
                'browser_cancelled_at' => now()->toIso8601String(),
            ]),
        ])->save();

        $this->paymentAuditLogger->record(
            $user,
            $payment,
            'donation_payment.cancelled',
            'Browser cancellation was recorded for the donor checkout.',
            $before,
            $payment->only(['status', 'verification_status', 'status_reason', 'cancelled_at']),
            [
                'public_reference' => $this->publicReferenceForPayment($payment),
                'provider_order_id' => $providerOrderId,
            ]
        );

        return new PaymentFlowResult(
            status: 'cancelled',
            payment: $payment->fresh($this->paymentRelations()),
            message: 'The donor checkout was cancelled in the browser. No receipt or donation record was created.'
        );
    }

    public function handleShurjopayIpn(array $input): PaymentFlowResult
    {
        return $this->handleShurjopayVerificationFlow(null, $input, 'ipn', false);
    }

    public function statusViewData(Request $request, string $publicReference): array
    {
        $intent = DonationIntent::query()
            ->where('public_reference', $publicReference)
            ->with([
                'donationCategory',
                'donationRecord.winningPayment.receipt',
                'donationRecord.donationCategory',
                'latestPayment.receipt',
                'user',
                'donor',
            ])
            ->firstOrFail();

        $queryAccessKey = trim((string) $request->query('access_key', ''));
        $sessionAccessKey = $this->sessionAccessKey($request, $publicReference);
        $plainAccessKey = $queryAccessKey !== '' ? $queryAccessKey : $sessionAccessKey;

        if (! $this->canViewIntent($request->user(), $intent, $plainAccessKey)) {
            throw new AuthorizationException('You are not allowed to view this donation status.');
        }

        if ($queryAccessKey !== '' && $this->donationReferenceGenerator->accessKeyMatches($intent->guest_access_token_hash, $queryAccessKey)) {
            $this->storeAccessKey($request, $publicReference, $queryAccessKey);
            $sessionAccessKey = $queryAccessKey;
        }

        $payment = $intent->donationRecord?->winningPayment ?: $intent->latestPayment;
        $status = $payment?->status ?? 'pending';
        $statusVariant = (string) session('donation_status_variant', match ($status) {
            Payment::STATUS_PAID => 'success',
            Payment::STATUS_FAILED, Payment::STATUS_CANCELLED => 'warning',
            Payment::STATUS_MANUAL_REVIEW => 'warning',
            default => 'info',
        });

        return [
            'intent' => $intent,
            'payment' => $payment,
            'donationRecord' => $intent->donationRecord,
            'accessKeyForDisplay' => $sessionAccessKey,
            'statusMessage' => (string) session('donation_status_message', $this->defaultStatusMessage($intent, $payment)),
            'statusVariant' => $statusVariant,
            'statusLink' => $sessionAccessKey
                ? route('donations.payments.show', ['publicReference' => $publicReference, 'access_key' => $sessionAccessKey])
                : route('donations.payments.show', ['publicReference' => $publicReference]),
        ];
    }

    private function createIntent(Request $request, array $draft, string $donorMode, ?User $user, string $plainAccessKey): DonationIntent
    {
        $contactSnapshot = $this->contactSnapshot($draft, $user);
        $donorId = $user?->donorProfile()->value('id');
        $donationCategory = $this->resolveDonationCategoryFromDraft($draft);
        $categorySnapshot = $this->draftCategorySnapshot($draft, $donationCategory);

        $intent = DonationIntent::query()->create([
            'user_id' => $user?->getKey(),
            'donor_id' => $donorId,
            'donation_category_id' => $donationCategory?->getKey(),
            'donor_mode' => $donorMode,
            'display_mode' => ! empty($draft['anonymous_display'])
                ? DonationIntent::DISPLAY_MODE_ANONYMOUS
                : DonationIntent::DISPLAY_MODE_IDENTIFIED,
            'amount' => round((float) ($draft['amount'] ?? 0), 2),
            'currency' => (string) config('payments.default_currency', 'BDT'),
            'status' => DonationIntent::STATUS_OPEN,
            'public_reference' => $this->donationReferenceGenerator->publicReference(),
            'guest_access_token_hash' => $this->donationReferenceGenerator->accessKeyHash($plainAccessKey),
            'name_snapshot' => $contactSnapshot['name'],
            'email_snapshot' => $contactSnapshot['email'],
            'phone_snapshot' => $contactSnapshot['phone'],
            'metadata' => [
                'category' => $categorySnapshot,
                'entry_context' => $draft['entry_context'] ?? null,
                'captured_at' => $draft['captured_at'] ?? null,
                'checkout_started_at' => now()->toIso8601String(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'anonymous_display' => (bool) ($draft['anonymous_display'] ?? false),
            ],
        ]);

        $this->paymentAuditLogger->record(
            $user,
            $intent,
            'donation_intent.created',
            'A donor checkout intent was created.',
            [],
            $intent->only([
                'user_id',
                'donor_id',
                'donor_mode',
                'display_mode',
                'amount',
                'currency',
                'status',
                'public_reference',
            ]),
            [
                'category_id' => $donationCategory?->getKey(),
                'category_key' => $draft['category_key'] ?? null,
                'entry_context' => $draft['entry_context'] ?? null,
            ]
        );

        return $intent;
    }

    private function buildCheckoutPayload(Request $request, DonationIntent $intent, Payment $payment, string $merchantOrderId): array
    {
        $customerDefaults = config('payments.customer_defaults');
        $customerName = trim((string) ($intent->name_snapshot ?: ($intent->user?->name ?: 'Guest Donor')));
        $customerEmail = trim((string) ($intent->email_snapshot ?: ($intent->user?->email ?: '')));
        $customerPhone = trim((string) ($intent->phone_snapshot ?: ($intent->user?->phone ?: '')));

        if ($customerEmail === '') {
            $host = parse_url((string) config('app.url'), PHP_URL_HOST) ?: 'example.com';
            $reference = Str::lower(Str::limit(preg_replace('/[^a-zA-Z0-9]/', '', $intent->public_reference) ?: 'donor', 8, ''));

            $customerEmail = "donor+{$reference}@{$host}";
        }

        if ($customerPhone === '') {
            $customerPhone = '01700000000';
        }

        return [
            'prefix' => config('payments.shurjopay.order_prefix'),
            'return_url' => route('donations.shurjopay.return.success'),
            'fail_url' => route('donations.shurjopay.return.fail'),
            'cancel_url' => route('donations.shurjopay.return.cancel'),
            'amount' => (float) $intent->amount,
            'order_id' => $merchantOrderId,
            'currency' => $intent->currency,
            'customer_name' => $customerName,
            'customer_address' => (string) $customerDefaults['address'],
            'customer_email' => $customerEmail,
            'customer_phone' => $customerPhone,
            'customer_city' => (string) $customerDefaults['city'],
            'customer_post_code' => (string) $customerDefaults['post_code'],
            'customer_state' => (string) $customerDefaults['state'],
            'customer_country' => (string) $customerDefaults['country'],
            'shipping_address' => (string) $customerDefaults['address'],
            'shipping_city' => (string) $customerDefaults['city'],
            'shipping_country' => (string) $customerDefaults['country'],
            'received_person_name' => $customerName,
            'shipping_phone_number' => $customerPhone,
            'client_ip' => $request->ip() ?: '127.0.0.1',
            'value1' => (string) $payment->getKey(),
            'value2' => $intent->public_reference,
            'value3' => $intent->donor_mode,
            'value4' => $payment->idempotency_key,
        ];
    }

    private function handleShurjopayVerificationFlow(?User $user, array $input, string $requestSource, bool $preferFailurePath): PaymentFlowResult
    {
        $providerOrderId = trim((string) ($input['order_id'] ?? ''));

        if ($providerOrderId === '') {
            return new PaymentFlowResult(
                status: $preferFailurePath ? 'failed' : 'manual_review',
                payment: null,
                message: 'shurjoPay did not supply an order id to donor checkout verification.'
            );
        }

        try {
            $verification = $this->verifyShurjopayOrder($providerOrderId);
            $record = $verification['record'];
            $payment = $this->locatePaymentFromVerification($providerOrderId, $record);
        } catch (Throwable $exception) {
            return $this->handleVerificationException($user, $input, $requestSource, $providerOrderId, $exception);
        }

        if ($payment) {
            $this->paymentEventLogger->record(
                $payment,
                'shurjopay',
                $requestSource === 'ipn' ? 'donation_ipn_received' : 'donation_browser_return',
                [
                    'source' => $requestSource,
                    'query' => $input,
                ],
                $requestSource === 'ipn' ? 'ipn' : 'return',
                $providerOrderId,
                null,
                'received'
            );

            $this->paymentEventLogger->record(
                $payment,
                'shurjopay',
                'donation_verification_result',
                $verification,
                'verify',
                $providerOrderId,
                $verification['http_status'],
                'processed',
                true
            );
        }

        if (! $payment) {
            return new PaymentFlowResult(
                status: 'manual_review',
                payment: null,
                message: 'Verification succeeded, but Laravel could not safely match the shurjoPay order id to a donor payment attempt.',
                context: ['provider_order_id' => $providerOrderId]
            );
        }

        $classification = $this->classifyVerificationRecord($record);

        if ($classification === 'success') {
            if (! $this->verificationMatchesLocalPayment($payment, $record)) {
                return $this->markManualReview(
                    $payment,
                    $user,
                    'shurjoPay verification data did not match the local donor amount or merchant reference.',
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
                    'A successful shurjoPay verification arrived after browser cancellation. Manual reconciliation is required.',
                    [
                        'provider_order_id' => $providerOrderId,
                        'verification_record' => $record,
                    ]
                );
            }

            try {
                $payment = $this->finalizeAsPaid(
                    $payment,
                    $user,
                    $record
                );
            } catch (Throwable $exception) {
                return $this->markManualReview(
                    $payment,
                    $user,
                    $exception->getMessage(),
                    [
                        'provider_order_id' => $providerOrderId,
                        'verification_record' => $record,
                    ]
                );
            }

            return new PaymentFlowResult(
                status: 'paid',
                payment: $payment,
                message: 'The donor payment was verified and finalized.'
            );
        }

        if ($classification === 'failed') {
            $before = $payment->only(['status', 'verification_status', 'status_reason', 'failed_at']);

            $payment->forceFill([
                'status' => $preferFailurePath ? Payment::STATUS_FAILED : Payment::STATUS_PENDING_VERIFICATION,
                'verification_status' => $preferFailurePath ? Payment::VERIFICATION_FAILED : Payment::VERIFICATION_PENDING,
                'status_reason' => $preferFailurePath
                    ? 'shurjoPay verification reported a non-successful donor payment outcome.'
                    : 'shurjoPay has not yet confirmed a successful donor payment outcome.',
                'failed_at' => $preferFailurePath ? now() : null,
                'metadata' => $this->mergeMetadata($payment->metadata, [
                    'last_verification' => $record,
                ]),
            ])->save();

            $this->paymentAuditLogger->record(
                $user,
                $payment,
                $preferFailurePath ? 'donation_payment.failed' : 'donation_payment.pending_verification',
                $preferFailurePath
                    ? 'The donor payment was marked failed after verification.'
                    : 'The donor payment remains pending verification.',
                $before,
                $payment->only(['status', 'verification_status', 'status_reason', 'failed_at']),
                [
                    'public_reference' => $this->publicReferenceForPayment($payment),
                    'provider_order_id' => $providerOrderId,
                ]
            );

            return new PaymentFlowResult(
                status: $preferFailurePath ? 'failed' : 'pending',
                payment: $payment->fresh($this->paymentRelations()),
                message: $preferFailurePath
                    ? 'The donor payment was not verified as successful by shurjoPay.'
                    : 'The donor payment is still waiting for a successful shurjoPay verification result.'
            );
        }

        $before = $payment->only(['status', 'verification_status', 'status_reason']);

        $payment->forceFill([
            'status' => Payment::STATUS_PENDING_VERIFICATION,
            'verification_status' => Payment::VERIFICATION_PENDING,
            'status_reason' => 'shurjoPay verification returned an ambiguous donor payment response. Manual review may be required.',
            'metadata' => $this->mergeMetadata($payment->metadata, [
                'last_verification' => $record,
            ]),
        ])->save();

        $this->paymentAuditLogger->record(
            $user,
            $payment,
            'donation_payment.pending_verification',
            'The donor payment remains pending because shurjoPay returned an ambiguous verification response.',
            $before,
            $payment->only(['status', 'verification_status', 'status_reason']),
            [
                'public_reference' => $this->publicReferenceForPayment($payment),
                'provider_order_id' => $providerOrderId,
            ]
        );

        return new PaymentFlowResult(
            status: 'pending',
            payment: $payment->fresh($this->paymentRelations()),
            message: 'The donor payment has not been authoritatively verified yet. No receipt or donation record was created.'
        );
    }

    private function finalizeAsPaid(Payment $payment, ?User $actor, array $verificationData): Payment
    {
        return DB::transaction(function () use ($payment, $actor, $verificationData): Payment {
            /** @var Payment $lockedPayment */
            $lockedPayment = Payment::query()
                ->whereKey($payment->getKey())
                ->with($this->paymentRelations())
                ->lockForUpdate()
                ->firstOrFail();

            if ($lockedPayment->status === Payment::STATUS_PAID) {
                return $lockedPayment;
            }

            $intent = $lockedPayment->payable;

            if (! $intent instanceof DonationIntent) {
                throw new RuntimeException('Only donation-intent payments are supported in donor checkout finalization.');
            }

            if ($intent->donationRecord && (int) $intent->donationRecord->winning_payment_id !== (int) $lockedPayment->getKey()) {
                throw new RuntimeException('This donation intent is already settled by another payment attempt. Manual review is required.');
            }

            $beforePayment = $lockedPayment->only([
                'status',
                'verification_status',
                'status_reason',
                'paid_at',
                'verified_at',
                'posted_transaction_id',
            ]);
            $beforeIntent = $intent->only(['status', 'settled_at']);

            $lockedPayment->forceFill([
                'status' => Payment::STATUS_PAID,
                'verification_status' => Payment::VERIFICATION_VERIFIED,
                'status_reason' => 'Verified by the shurjoPay server-side verification API.',
                'paid_at' => now(),
                'verified_at' => now(),
                'failed_at' => null,
                'cancelled_at' => null,
                'metadata' => $this->mergeMetadata($lockedPayment->metadata, [
                    'verification_data' => $verificationData,
                    'posting' => [
                        'status' => DonationRecord::POSTING_SKIPPED,
                        'reason' => 'Donor settlement remains separate from legacy accounting posting in prompt-34.',
                    ],
                ]),
            ])->save();

            $receipt = $lockedPayment->receipt ?: Receipt::query()->create([
                'payment_id' => $lockedPayment->getKey(),
                'issued_to_user_id' => $lockedPayment->user_id,
                'receipt_number' => $this->paymentReferenceGenerator->receiptNumber($lockedPayment),
                'currency' => $lockedPayment->currency,
                'amount' => $lockedPayment->amount,
                'issued_at' => now(),
                'metadata' => [
                    'provider' => $lockedPayment->provider,
                    'provider_mode' => $lockedPayment->provider_mode,
                    'public_reference' => $intent->public_reference,
                ],
            ]);

            $donationRecord = $intent->donationRecord ?: DonationRecord::query()->create([
                'donation_intent_id' => $intent->getKey(),
                'winning_payment_id' => $lockedPayment->getKey(),
                'user_id' => $intent->user_id,
                'donor_id' => $intent->donor_id,
                'donation_category_id' => $intent->donation_category_id,
                'donor_mode' => $intent->donor_mode,
                'display_mode' => $intent->display_mode,
                'amount' => $intent->amount,
                'currency' => $intent->currency,
                'donated_at' => now(),
                'posting_status' => DonationRecord::POSTING_SKIPPED,
                'name_snapshot' => $intent->name_snapshot,
                'email_snapshot' => $intent->email_snapshot,
                'phone_snapshot' => $intent->phone_snapshot,
                'metadata' => [
                    'receipt_id' => $receipt->getKey(),
                    'public_reference' => $intent->public_reference,
                    'category' => array_filter([
                        'key' => $intent->resolvedDonationCategoryKey(),
                        'label' => $intent->resolvedDonationCategoryLabel(),
                    ], static fn ($value): bool => is_string($value) && $value !== ''),
                ],
            ]);

            if ($intent->donationRecord instanceof DonationRecord) {
                $recordUpdates = [];
                $recordMetadata = is_array($donationRecord->metadata) ? $donationRecord->metadata : [];

                if ($donationRecord->donation_category_id === null && $intent->donation_category_id) {
                    $recordUpdates['donation_category_id'] = $intent->donation_category_id;
                }

                if (! data_get($recordMetadata, 'category.key') && ! data_get($recordMetadata, 'category.label')) {
                    $recordMetadata['category'] = array_filter([
                        'key' => $intent->resolvedDonationCategoryKey(),
                        'label' => $intent->resolvedDonationCategoryLabel(),
                    ], static fn ($value): bool => is_string($value) && $value !== '');
                    $recordUpdates['metadata'] = $recordMetadata;
                }

                if ($recordUpdates !== []) {
                    $donationRecord->forceFill($recordUpdates)->save();
                }
            }

            $intent->forceFill([
                'status' => DonationIntent::STATUS_SUCCEEDED,
                'settled_at' => now(),
                'metadata' => $this->mergeMetadata($intent->metadata, [
                    'winning_payment_id' => $lockedPayment->getKey(),
                    'receipt_id' => $receipt->getKey(),
                ]),
            ])->save();

            $this->paymentAuditLogger->record(
                $actor,
                $lockedPayment,
                'donation_payment.paid',
                'Donor payment was finalized as paid.',
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
                    'public_reference' => $intent->public_reference,
                    'receipt_id' => $receipt->getKey(),
                ]
            );

            $this->paymentAuditLogger->record(
                $actor,
                $intent,
                'donation_intent.settled',
                'Donation intent was settled after verified payment.',
                $beforeIntent,
                $intent->only(['status', 'settled_at']),
                [
                    'payment_id' => $lockedPayment->getKey(),
                    'receipt_id' => $receipt->getKey(),
                ]
            );

            $this->paymentAuditLogger->record(
                $actor,
                $donationRecord,
                'donation_record.created',
                'A settled donation record was created.',
                [],
                $donationRecord->only(['donor_mode', 'display_mode', 'amount', 'currency', 'donated_at', 'posting_status']),
                [
                    'payment_id' => $lockedPayment->getKey(),
                    'receipt_id' => $receipt->getKey(),
                    'public_reference' => $intent->public_reference,
                ]
            );

            $this->paymentAuditLogger->record(
                $actor,
                $receipt,
                'receipt.issued',
                'A dedicated receipt row was issued for the settled donation.',
                [],
                $receipt->only(['receipt_number', 'amount', 'issued_at']),
                [
                    'payment_id' => $lockedPayment->getKey(),
                    'public_reference' => $intent->public_reference,
                ]
            );

            return $lockedPayment->fresh($this->paymentRelations());
        });
    }

    private function markManualReview(Payment $payment, ?User $actor, string $reason, array $context = []): PaymentFlowResult
    {
        $before = $payment->only(['status', 'verification_status', 'status_reason']);
        $intent = $payment->payable;

        $payment->forceFill([
            'status' => Payment::STATUS_MANUAL_REVIEW,
            'verification_status' => Payment::VERIFICATION_MANUAL_REVIEW,
            'status_reason' => $reason,
            'metadata' => $this->mergeMetadata($payment->metadata, [
                'manual_review_context' => $context,
            ]),
        ])->save();

        if ($intent instanceof DonationIntent) {
            $intent->forceFill([
                'status' => DonationIntent::STATUS_MANUAL_REVIEW,
                'metadata' => $this->mergeMetadata($intent->metadata, [
                    'manual_review_context' => $context,
                ]),
            ])->save();
        }

        $this->paymentAuditLogger->record(
            $actor,
            $payment,
            'donation_payment.manual_review',
            'Donor payment was routed to manual review.',
            $before,
            $payment->only(['status', 'verification_status', 'status_reason']),
            $context
        );

        return new PaymentFlowResult(
            status: 'manual_review',
            payment: $payment->fresh($this->paymentRelations()),
            message: $reason,
            context: $context
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
        $payment = $this->findPaymentByProviderOrder($providerOrderId);

        $this->paymentEventLogger->record(
            $payment,
            'shurjopay',
            'donation_verification_error',
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
                message: 'shurjoPay verification could not be completed and Laravel could not match the order id to a donor payment.',
                context: [
                    'provider_order_id' => $providerOrderId,
                    'verification_error' => $exception->getMessage(),
                ]
            );
        }

        return $this->markManualReview(
            $payment,
            $user,
            'shurjoPay verification could not be completed. Manual review is required before treating this donation as settled.',
            [
                'provider_order_id' => $providerOrderId,
                'verification_error' => $exception->getMessage(),
            ]
        );
    }

    private function locatePaymentFromVerification(string $providerOrderId, array $record): ?Payment
    {
        $payment = $this->findPaymentByProviderOrder($providerOrderId);

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
            ->where('payable_type', DonationIntent::class)
            ->with($this->paymentRelations())
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

    private function findPaymentByProviderOrder(string $providerOrderId): ?Payment
    {
        return Payment::query()
            ->where('provider', 'shurjopay')
            ->where('provider_reference', $providerOrderId)
            ->where('payable_type', DonationIntent::class)
            ->with($this->paymentRelations())
            ->first();
    }

    private function classifyVerificationRecord(array $record): string
    {
        $bankStatus = strtolower(trim((string) ($record['bank_status'] ?? '')));
        $spMessage = strtolower(trim((string) ($record['sp_message'] ?? $record['message'] ?? '')));
        $spCode = (string) ($record['sp_code'] ?? '');

        if ($bankStatus === 'success' || ($spCode === '1000' && ! in_array($bankStatus, ['failed', 'cancelled', 'canceled'], true))) {
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
        $localPaymentId = (int) ($record['value1'] ?? 0);

        if (abs($verifiedAmount - (float) $payment->amount) > 0.01) {
            return false;
        }

        if ($verifiedCurrency !== '' && $verifiedCurrency !== (string) $payment->currency) {
            return false;
        }

        if ($localPaymentId > 0 && $localPaymentId !== (int) $payment->getKey()) {
            return false;
        }

        if ($merchantOrderId !== '' && $customerOrderId !== '' && $merchantOrderId !== $customerOrderId && $localPaymentId <= 0) {
            return false;
        }

        return true;
    }

    private function canViewIntent(?User $user, DonationIntent $intent, ?string $accessKey): bool
    {
        if ($user?->hasRole('management')) {
            return true;
        }

        if ($user && (int) $intent->user_id === (int) $user->getKey()) {
            return true;
        }

        return $this->donationReferenceGenerator->accessKeyMatches($intent->guest_access_token_hash, $accessKey);
    }

    private function findReusableIntent(Request $request, string $donorMode, ?User $user): ?DonationIntent
    {
        $publicReference = (string) $request->session()->get(self::CURRENT_INTENT_SESSION_KEY, '');

        if ($publicReference === '') {
            return null;
        }

        $intent = DonationIntent::query()
            ->where('public_reference', $publicReference)
            ->with(['donationRecord', 'latestPayment'])
            ->first();

        if (! $intent || $intent->status !== DonationIntent::STATUS_OPEN || $intent->donor_mode !== $donorMode || $intent->donationRecord) {
            return null;
        }

        if ($user) {
            return (int) $intent->user_id === (int) $user->getKey() ? $intent : null;
        }

        if ($intent->user_id !== null) {
            return null;
        }

        return $this->donationReferenceGenerator->accessKeyMatches(
            $intent->guest_access_token_hash,
            $this->sessionAccessKey($request, $intent->public_reference)
        ) ? $intent : null;
    }

    private function ensureNoActiveAttempt(DonationIntent $intent): void
    {
        $activeAttempt = Payment::query()
            ->where('payable_type', DonationIntent::class)
            ->where('payable_id', $intent->getKey())
            ->whereIn('status', Payment::ACTIVE_STATUSES)
            ->latest('id')
            ->first();

        if ($activeAttempt) {
            throw ValidationException::withMessages([
                'payment' => ['A donor payment attempt is already active for this donation. Check the status page before starting another checkout.'],
            ]);
        }
    }

    private function contactSnapshot(array $draft, ?User $user): array
    {
        $name = trim((string) ($draft['name'] ?? ''));
        $email = trim((string) ($draft['email'] ?? ''));
        $phone = trim((string) ($draft['phone'] ?? ''));

        return [
            'name' => $name !== '' ? $name : ($user?->name ?: null),
            'email' => $email !== '' ? $email : ($user?->email ?: null),
            'phone' => $phone !== '' ? $phone : ($user?->phone ?: null),
        ];
    }

    private function sessionAccessKey(Request $request, ?string $publicReference): ?string
    {
        if (! $publicReference) {
            return null;
        }

        $accessKeys = $request->session()->get(self::ACCESS_KEYS_SESSION_KEY, []);

        return is_array($accessKeys) ? ($accessKeys[$publicReference] ?? null) : null;
    }

    private function storeAccessKey(Request $request, string $publicReference, ?string $plainAccessKey): void
    {
        if (! $plainAccessKey) {
            return;
        }

        $accessKeys = $request->session()->get(self::ACCESS_KEYS_SESSION_KEY, []);

        if (! is_array($accessKeys)) {
            $accessKeys = [];
        }

        $accessKeys[$publicReference] = $plainAccessKey;

        $request->session()->put(self::ACCESS_KEYS_SESSION_KEY, $accessKeys);
    }

    private function paymentRelations(): array
    {
        return [
            'payable.donationRecord.winningPayment.receipt',
            'payable.donationRecord.donationCategory',
            'payable.donationCategory',
            'payable.latestPayment.receipt',
            'payable.user',
            'payable.donor',
            'receipt',
            'user',
        ];
    }

    private function defaultStatusMessage(DonationIntent $intent, ?Payment $payment): string
    {
        if ($payment?->status === Payment::STATUS_PAID) {
            return $intent->donor_mode === DonationIntent::DONOR_MODE_GUEST
                ? 'Your donation was settled successfully. No account was created automatically.'
                : 'Your account-linked donation was settled successfully. Donor portal access remains a separate later step.';
        }

        return $payment?->status_reason
            ?: 'This page only shows the specific donation referenced here. It does not open donor portal history or broader account access.';
    }

    private function publicReferenceForPayment(Payment $payment): ?string
    {
        $intent = $payment->payable;

        return $intent instanceof DonationIntent ? $intent->public_reference : null;
    }

    private function mergeMetadata(?array $existing, array $patch): array
    {
        return array_replace_recursive($existing ?? [], $patch);
    }

    private function resolveDonationCategoryFromDraft(array $draft): ?DonationCategory
    {
        $categoryId = (int) ($draft['category_id'] ?? 0);

        if ($categoryId > 0) {
            $category = DonationCategory::query()->find($categoryId);

            if ($category) {
                return $category;
            }
        }

        $categoryKey = trim((string) ($draft['category_key'] ?? ''));

        if ($categoryKey === '') {
            return null;
        }

        return DonationCategory::query()
            ->where('key', $categoryKey)
            ->first();
    }

    private function draftCategorySnapshot(array $draft, ?DonationCategory $category): array
    {
        return array_filter([
            'key' => $draft['category_key'] ?? $category?->key,
            'label' => $draft['category_label'] ?? $category?->displayLabel(),
        ], static fn ($value): bool => is_string($value) && $value !== '');
    }
}
