<?php

namespace Tests\Feature\Phase5;

use App\Models\Guardian;
use App\Models\Payment;
use App\Models\Role;
use App\Models\Student;
use App\Models\StudentFeeInvoice;
use App\Models\User;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PaymentIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(VerifyCsrfToken::class);
        $this->withoutMiddleware(ValidateCsrfToken::class);

        Role::query()->create(['name' => 'management', 'display_name' => 'Management']);
        Role::query()->create(['name' => 'guardian', 'display_name' => 'Guardian']);
        Role::query()->create(['name' => 'donor', 'display_name' => 'Donor']);

        config([
            'payments.provider_mode' => 'sandbox',
            'payments.shurjopay.sandbox.base_url' => 'https://sandbox.shurjopayment.com',
            'payments.shurjopay.sandbox.username' => 'sp_sandbox',
            'payments.shurjopay.sandbox.password' => 'sandbox-password',
            'payments.shurjopay.order_prefix' => 'TIC',
            'payments.manual_bank.enabled' => true,
            'payments.posting.student_fee.enabled' => false,
        ]);
    }

    public function test_guardian_can_initiate_a_sandbox_shurjopay_payment_for_a_linked_invoice(): void
    {
        ['guardianUser' => $guardianUser, 'invoice' => $invoice] = $this->makeGuardianInvoiceFixture();

        Http::fake([
            'https://sandbox.shurjopayment.com/api/get_token' => Http::response([
                'token' => 'sandbox-token',
                'store_id' => 1,
                'token_type' => 'Bearer',
                'sp_code' => 1000,
                'message' => 'Ok',
            ]),
            'https://sandbox.shurjopayment.com/api/secret-pay' => Http::response([
                'checkout_url' => 'https://sandbox.shurjopayment.com/pay/abc123',
                'sp_order_id' => 'SP-SANDBOX-001',
                'customer_order_id' => 'TIC-INV-1',
            ]),
        ]);

        $this->actingAs($guardianUser)
            ->post('/payments/shurjopay/initiate', [
                'invoice_id' => $invoice->id,
            ])
            ->assertRedirect('https://sandbox.shurjopayment.com/pay/abc123');

        $payment = Payment::query()->where('provider', 'shurjopay')->first();

        $this->assertNotNull($payment);
        $this->assertSame(Payment::STATUS_REDIRECT_PENDING, $payment->status);
        $this->assertSame('SP-SANDBOX-001', $payment->provider_reference);
        $this->assertSame('sandbox', $payment->provider_mode);
        $this->assertSame('TIC-INV-'.$payment->id, data_get($payment->metadata, 'merchant_order_id'));
        Http::assertSent(function ($request): bool {
            if ($request->url() !== 'https://sandbox.shurjopayment.com/api/secret-pay') {
                return false;
            }

            $payload = $request->data();

            return ($payload['prefix'] ?? null) === 'TIC'
                && ($payload['return_url'] ?? null) === route('payments.shurjopay.return.success')
                && ($payload['fail_url'] ?? null) === route('payments.shurjopay.return.fail')
                && ($payload['cancel_url'] ?? null) === route('payments.shurjopay.return.cancel');
        });
        $this->assertDatabaseHas('payment_gateway_events', [
            'payment_id' => $payment->id,
            'provider' => 'shurjopay',
            'event_name' => 'initiate_response',
            'provider_order_id' => 'SP-SANDBOX-001',
        ]);
    }

    public function test_success_return_routes_verification_transport_errors_to_manual_review(): void
    {
        ['guardianUser' => $guardianUser, 'invoice' => $invoice] = $this->makeGuardianInvoiceFixture();

        $payment = Payment::query()->create([
            'user_id' => $guardianUser->id,
            'payable_type' => StudentFeeInvoice::class,
            'payable_id' => $invoice->id,
            'status' => Payment::STATUS_REDIRECT_PENDING,
            'verification_status' => Payment::VERIFICATION_PENDING,
            'provider' => 'shurjopay',
            'provider_mode' => 'sandbox',
            'currency' => 'BDT',
            'amount' => 800,
            'idempotency_key' => 'SPAY-TEST-ERROR-001',
            'provider_reference' => 'SP-SANDBOX-ERROR-001',
            'metadata' => [
                'merchant_order_id' => 'TIC-INV-1003',
            ],
            'initiated_at' => now()->subMinute(),
        ]);

        Http::fake([
            'https://sandbox.shurjopayment.com/api/get_token' => Http::response([
                'token' => 'sandbox-token',
                'store_id' => 1,
                'token_type' => 'Bearer',
            ]),
            'https://sandbox.shurjopayment.com/api/verification' => Http::response([
                'message' => 'temporary failure',
            ], 500),
        ]);

        $this->actingAs($guardianUser)
            ->get('/payments/shurjopay/return/success?order_id=SP-SANDBOX-ERROR-001')
            ->assertOk()
            ->assertSeeText('Manual Review Needed');

        $payment->refresh();
        $invoice->refresh();

        $this->assertSame(Payment::STATUS_MANUAL_REVIEW, $payment->status);
        $this->assertSame(Payment::VERIFICATION_MANUAL_REVIEW, $payment->verification_status);
        $this->assertSame(0.0, (float) $invoice->paid_amount);
        $this->assertSame(800.0, (float) $invoice->balance_amount);
        $this->assertDatabaseCount('receipts', 0);
        $this->assertDatabaseHas('payment_gateway_events', [
            'payment_id' => $payment->id,
            'provider' => 'shurjopay',
            'event_name' => 'verification_error',
            'provider_order_id' => 'SP-SANDBOX-ERROR-001',
        ]);
    }

    public function test_success_return_verifies_and_finalizes_the_payment(): void
    {
        ['guardianUser' => $guardianUser, 'invoice' => $invoice] = $this->makeGuardianInvoiceFixture();

        $payment = Payment::query()->create([
            'user_id' => $guardianUser->id,
            'payable_type' => StudentFeeInvoice::class,
            'payable_id' => $invoice->id,
            'status' => Payment::STATUS_REDIRECT_PENDING,
            'verification_status' => Payment::VERIFICATION_PENDING,
            'provider' => 'shurjopay',
            'provider_mode' => 'sandbox',
            'currency' => 'BDT',
            'amount' => 800,
            'idempotency_key' => 'SPAY-TEST-001',
            'provider_reference' => 'SP-SANDBOX-PAID-001',
            'metadata' => [
                'merchant_order_id' => 'TIC-INV-1001',
            ],
            'initiated_at' => now()->subMinute(),
        ]);

        Http::fake([
            'https://sandbox.shurjopayment.com/api/get_token' => Http::response([
                'token' => 'sandbox-token',
                'store_id' => 1,
                'token_type' => 'Bearer',
            ]),
            'https://sandbox.shurjopayment.com/api/verification' => Http::response([
                [
                    'order_id' => 'SP-SANDBOX-PAID-001',
                    'customer_order_id' => 'TIC-INV-1001',
                    'amount' => 800,
                    'currency' => 'BDT',
                    'bank_status' => 'Success',
                    'sp_code' => 1000,
                    'value1' => (string) $payment->id,
                ],
            ]),
        ]);

        $this->actingAs($guardianUser)
            ->get('/payments/shurjopay/return/success?order_id=SP-SANDBOX-PAID-001')
            ->assertOk()
            ->assertSeeText('Payment Verified');

        $payment->refresh();
        $invoice->refresh();

        $this->assertSame(Payment::STATUS_PAID, $payment->status);
        $this->assertSame(Payment::VERIFICATION_VERIFIED, $payment->verification_status);
        $this->assertNotNull($payment->paid_at);
        $this->assertSame('paid', $invoice->status);
        $this->assertSame(800.0, (float) $invoice->paid_amount);
        $this->assertSame(0.0, (float) $invoice->balance_amount);
        $this->assertDatabaseCount('receipts', 1);
        $this->assertDatabaseHas('payment_gateway_events', [
            'payment_id' => $payment->id,
            'provider' => 'shurjopay',
            'event_name' => 'verification_result',
            'provider_order_id' => 'SP-SANDBOX-PAID-001',
        ]);
    }

    public function test_ipn_routes_mismatched_verification_to_manual_review(): void
    {
        ['invoice' => $invoice, 'guardianUser' => $guardianUser] = $this->makeGuardianInvoiceFixture();

        $payment = Payment::query()->create([
            'user_id' => $guardianUser->id,
            'payable_type' => StudentFeeInvoice::class,
            'payable_id' => $invoice->id,
            'status' => Payment::STATUS_REDIRECT_PENDING,
            'verification_status' => Payment::VERIFICATION_PENDING,
            'provider' => 'shurjopay',
            'provider_mode' => 'sandbox',
            'currency' => 'BDT',
            'amount' => 800,
            'idempotency_key' => 'SPAY-TEST-002',
            'provider_reference' => 'SP-SANDBOX-MISMATCH-001',
            'metadata' => [
                'merchant_order_id' => 'TIC-INV-1002',
            ],
            'initiated_at' => now()->subMinute(),
        ]);

        Http::fake([
            'https://sandbox.shurjopayment.com/api/get_token' => Http::response([
                'token' => 'sandbox-token',
                'store_id' => 1,
                'token_type' => 'Bearer',
            ]),
            'https://sandbox.shurjopayment.com/api/verification' => Http::response([
                [
                    'order_id' => 'SP-SANDBOX-MISMATCH-001',
                    'customer_order_id' => 'TIC-INV-9999',
                    'amount' => 700,
                    'currency' => 'BDT',
                    'bank_status' => 'Success',
                    'sp_code' => 1000,
                    'value1' => (string) $payment->id,
                ],
            ]),
        ]);

        $this->post('/payments/shurjopay/ipn', [
            'order_id' => 'SP-SANDBOX-MISMATCH-001',
        ])->assertOk()
            ->assertJson([
                'status' => 'manual_review',
                'payment_id' => $payment->id,
            ]);

        $payment->refresh();

        $this->assertSame(Payment::STATUS_MANUAL_REVIEW, $payment->status);
        $this->assertSame(Payment::VERIFICATION_MANUAL_REVIEW, $payment->verification_status);
        $this->assertDatabaseCount('receipts', 0);
    }

    public function test_manual_bank_submission_and_management_approval_finalize_the_payment(): void
    {
        ['invoice' => $invoice, 'guardianUser' => $guardianUser] = $this->makeGuardianInvoiceFixture();
        $manager = User::factory()->create();
        $manager->assignRole('management');

        $this->actingAs($guardianUser)
            ->post('/payments/manual-bank/requests', [
                'invoice_id' => $invoice->id,
                'payer_name' => 'Guardian One',
                'bank_reference' => 'BANK-REF-1001',
                'payment_channel' => 'Bank Transfer',
                'transferred_at' => now()->toDateTimeString(),
                'note' => 'Sandbox bank transfer',
            ])
            ->assertRedirect();

        $payment = Payment::query()->where('provider', 'manual_bank')->first();

        $this->assertNotNull($payment);
        $this->assertSame(Payment::STATUS_AWAITING_MANUAL_PAYMENT, $payment->status);
        $this->assertSame('BANK-REF-1001', $payment->provider_reference);

        $this->actingAs($manager)
            ->post("/management/payments/manual-bank/{$payment->id}/approve", [
                'matched_bank_reference' => 'BANK-REF-1001',
                'decision_note' => 'Matched to sandbox statement.',
            ])
            ->assertRedirect('/management/payments/manual-bank');

        $payment->refresh();
        $invoice->refresh();

        $this->assertSame(Payment::STATUS_PAID, $payment->status);
        $this->assertSame(Payment::VERIFICATION_VERIFIED, $payment->verification_status);
        $this->assertSame($manager->id, $payment->reviewed_by_user_id);
        $this->assertSame('paid', $invoice->status);
        $this->assertDatabaseHas('receipts', [
            'payment_id' => $payment->id,
        ]);
        $this->assertDatabaseHas('payment_gateway_events', [
            'payment_id' => $payment->id,
            'provider' => 'manual_bank',
            'event_name' => 'manual_bank_approved',
        ]);
    }

    public function test_manual_bank_resubmission_reuses_the_pending_request_instead_of_blocking_it(): void
    {
        ['invoice' => $invoice, 'guardianUser' => $guardianUser] = $this->makeGuardianInvoiceFixture();

        $this->actingAs($guardianUser)
            ->post('/payments/manual-bank/requests', [
                'invoice_id' => $invoice->id,
                'payer_name' => 'Guardian One',
                'bank_reference' => 'BANK-REF-2001',
                'payment_channel' => 'Bank Transfer',
                'transferred_at' => now()->subMinute()->toDateTimeString(),
                'note' => 'Initial sandbox bank transfer',
            ])
            ->assertRedirect();

        $payment = Payment::query()->where('provider', 'manual_bank')->firstOrFail();
        $originalIdempotencyKey = $payment->idempotency_key;

        $this->actingAs($guardianUser)
            ->post('/payments/manual-bank/requests', [
                'invoice_id' => $invoice->id,
                'payer_name' => 'Guardian One Updated',
                'bank_reference' => 'BANK-REF-2001',
                'payment_channel' => 'Bank Transfer',
                'transferred_at' => now()->toDateTimeString(),
                'note' => 'Updated sandbox evidence',
            ])
            ->assertRedirect(route('payments.manual-bank.show', $payment, false));

        $payment->refresh();

        $this->assertSame(Payment::STATUS_AWAITING_MANUAL_PAYMENT, $payment->status);
        $this->assertSame($originalIdempotencyKey, $payment->idempotency_key);
        $this->assertSame('Guardian One Updated', data_get($payment->metadata, 'manual_bank.payer_name'));
        $this->assertSame('Updated sandbox evidence', data_get($payment->metadata, 'manual_bank.note'));
        $this->assertNull($payment->reviewed_by_user_id);
        $this->assertNull($payment->reviewed_at);
        $this->assertSame(1, Payment::query()->where('provider', 'manual_bank')->count());
        $this->assertDatabaseHas('payment_gateway_events', [
            'payment_id' => $payment->id,
            'provider' => 'manual_bank',
            'event_name' => 'manual_bank_resubmitted',
        ]);
    }

    private function makeGuardianInvoiceFixture(): array
    {
        $guardianUser = User::factory()->create();
        $guardianUser->assignRole('guardian');

        $guardian = Guardian::query()->create([
            'user_id' => $guardianUser->id,
            'name' => 'Guardian One',
            'email' => $guardianUser->email,
            'mobile' => '01700000000',
            'address' => 'Dhaka, Bangladesh',
            'portal_enabled' => true,
            'isActived' => true,
            'isDeleted' => false,
        ]);

        $student = Student::query()->create([
            'full_name' => 'Sandbox Student',
            'roll' => 11,
            'isActived' => true,
            'isDeleted' => false,
        ]);

        $guardian->students()->attach($student->id, [
            'relationship_label' => 'Father',
            'is_primary' => true,
        ]);

        $invoice = StudentFeeInvoice::query()->create([
            'student_id' => $student->id,
            'guardian_id' => $guardian->id,
            'invoice_number' => 'INV-PHASE5-001',
            'status' => 'open',
            'issued_at' => now()->subDays(2)->toDateString(),
            'due_at' => now()->addDays(4)->toDateString(),
            'subtotal_amount' => 800,
            'total_amount' => 800,
            'paid_amount' => 0,
            'balance_amount' => 800,
            'currency' => 'BDT',
        ]);

        return compact('guardianUser', 'guardian', 'student', 'invoice');
    }
}

