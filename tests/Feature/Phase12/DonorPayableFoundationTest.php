<?php

namespace Tests\Feature\Phase12;

use App\Models\DonationIntent;
use App\Models\DonationRecord;
use App\Models\Donor;
use App\Models\Payment;
use App\Models\Receipt;
use App\Models\Role;
use App\Models\User;
use App\Services\Donations\DonationCheckoutService;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class DonorPayableFoundationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(ValidateCsrfToken::class);

        Role::query()->create([
            'name' => User::ROLE_REGISTERED_USER,
            'display_name' => 'Registered User',
        ]);
        Role::query()->create([
            'name' => 'donor',
            'display_name' => 'Donor',
        ]);

        config([
            'payments.provider_mode' => 'sandbox',
            'payments.shurjopay.sandbox.base_url' => 'https://sandbox.shurjopayment.com',
            'payments.shurjopay.sandbox.username' => 'sp_sandbox',
            'payments.shurjopay.sandbox.password' => 'sandbox-password',
            'payments.shurjopay.order_prefix' => 'DON',
            'payments.default_currency' => 'BDT',
            'payments.posting.student_fee.enabled' => false,
        ]);
    }

    public function test_guest_checkout_creates_a_donation_intent_and_redirect_pending_payment(): void
    {
        Http::fake([
            'https://sandbox.shurjopayment.com/api/get_token' => Http::response([
                'token' => 'sandbox-token',
                'store_id' => 1,
                'token_type' => 'Bearer',
                'sp_code' => 1000,
                'message' => 'Ok',
            ]),
            'https://sandbox.shurjopayment.com/api/secret-pay' => Http::response([
                'checkout_url' => 'https://sandbox.shurjopayment.com/pay/donation-001',
                'sp_order_id' => 'SP-DON-001',
                'customer_order_id' => 'DON-DON-1',
            ]),
        ]);

        $this->post('/donate/start', [
            'category' => 'madrasa_complex',
            'amount' => '1500',
            'name' => 'Guest Donor',
            'email' => 'Guest@Example.com',
        ])->assertRedirect('/donate');

        $this->post('/donate/checkout')
            ->assertRedirect('https://sandbox.shurjopayment.com/pay/donation-001');

        $intent = DonationIntent::query()->first();
        $payment = Payment::query()->first();

        $this->assertNotNull($intent);
        $this->assertNotNull($payment);
        $this->assertSame(DonationIntent::DONOR_MODE_GUEST, $intent->donor_mode);
        $this->assertNull($intent->user_id);
        $this->assertSame('guest@example.com', $intent->email_snapshot);
        $this->assertSame(DonationIntent::STATUS_OPEN, $intent->status);
        $this->assertNotEmpty($intent->public_reference);
        $this->assertNotEmpty($intent->guest_access_token_hash);
        $this->assertSame('madrasa_complex', data_get($intent->metadata, 'category.key'));
        $this->assertSame('মাদ্রাসা কমপ্লেক্স', data_get($intent->metadata, 'category.label'));
        $this->assertSame(DonationIntent::class, $payment->payable_type);
        $this->assertNull($payment->user_id);
        $this->assertSame(Payment::STATUS_REDIRECT_PENDING, $payment->status);
        $this->assertSame('SP-DON-001', $payment->provider_reference);
        $this->assertSame($intent->id, $payment->payable_id);
        $this->assertSame($intent->public_reference, data_get($payment->metadata, 'donation_intent.public_reference'));
        $this->assertSame($intent->public_reference, session(DonationCheckoutService::CURRENT_INTENT_SESSION_KEY));
        $this->assertNotEmpty(session(DonationCheckoutService::ACCESS_KEYS_SESSION_KEY.'.'.$intent->public_reference));
        $this->assertDatabaseCount('donors', 0);
        $this->assertDatabaseCount('donation_records', 0);
    }

    public function test_guest_success_return_finalizes_the_donation_and_keeps_access_transaction_specific(): void
    {
        Http::fake([
            'https://sandbox.shurjopayment.com/api/get_token' => Http::response([
                'token' => 'sandbox-token',
                'store_id' => 1,
                'token_type' => 'Bearer',
                'sp_code' => 1000,
                'message' => 'Ok',
            ]),
            'https://sandbox.shurjopayment.com/api/secret-pay' => Http::response([
                'checkout_url' => 'https://sandbox.shurjopayment.com/pay/donation-002',
                'sp_order_id' => 'SP-DON-002',
                'customer_order_id' => 'placeholder',
            ]),
        ]);

        $this->post('/donate/start', [
            'category' => 'mosque_complex',
            'amount' => '2500',
            'name' => 'Guest Donor',
        ])->assertRedirect('/donate');

        $this->post('/donate/checkout')
            ->assertRedirect('https://sandbox.shurjopayment.com/pay/donation-002');

        $payment = Payment::query()->firstOrFail();
        $intent = DonationIntent::query()->firstOrFail();
        $accessKey = session(DonationCheckoutService::ACCESS_KEYS_SESSION_KEY.'.'.$intent->public_reference);

        Http::fake([
            'https://sandbox.shurjopayment.com/api/get_token' => Http::response([
                'token' => 'sandbox-token',
                'store_id' => 1,
                'token_type' => 'Bearer',
                'sp_code' => 1000,
                'message' => 'Ok',
            ]),
            'https://sandbox.shurjopayment.com/api/verification' => Http::response([
                [
                    'order_id' => 'SP-DON-002',
                    'customer_order_id' => (string) data_get($payment->metadata, 'merchant_order_id'),
                    'amount' => (float) $payment->amount,
                    'currency' => $payment->currency,
                    'bank_status' => 'Success',
                    'sp_code' => 1000,
                    'value1' => (string) $payment->id,
                ],
            ]),
        ]);

        $this->get('/donate/return/success?order_id=SP-DON-002')
            ->assertRedirect(route('donations.payments.show', ['publicReference' => $intent->public_reference], false));

        $payment->refresh();
        $intent->refresh();
        $record = DonationRecord::query()->first();
        $receipt = Receipt::query()->first();

        $this->assertSame(Payment::STATUS_PAID, $payment->status, $payment->status_reason);
        $this->assertSame(Payment::VERIFICATION_VERIFIED, $payment->verification_status);
        $this->assertSame(DonationIntent::STATUS_SUCCEEDED, $intent->status);
        $this->assertSame('mosque_complex', data_get($intent->metadata, 'category.key'));
        $this->assertNotNull($record);
        $this->assertNotNull($receipt);
        $this->assertNull($receipt->issued_to_user_id);
        $this->assertSame($intent->id, $record->donation_intent_id);
        $this->assertSame($payment->id, $record->winning_payment_id);
        $this->assertSame(DonationRecord::POSTING_SKIPPED, $record->posting_status);
        $this->assertDatabaseCount('transactions', 0);
        $this->assertDatabaseCount('users', 0);
        $this->assertDatabaseCount('donors', 0);

        $this->get(route('donations.payments.show', [
            'publicReference' => $intent->public_reference,
            'access_key' => $accessKey,
        ], false))
            ->assertOk()
            ->assertSeeText('Donation Verified')
            ->assertSeeText($intent->public_reference)
            ->assertSeeText($receipt->receipt_number)
            ->assertSeeText('No account was created automatically.');
    }

    public function test_authenticated_users_can_complete_identified_checkout_without_donor_portal_gating_or_profile_creation(): void
    {
        $user = User::factory()->create([
            'approval_status' => User::APPROVAL_NOT_REQUIRED,
            'account_status' => User::ACCOUNT_STATUS_ACTIVE,
            'phone' => '+8801711111111',
        ]);
        $user->assignRole(User::ROLE_REGISTERED_USER);

        Http::fake([
            'https://sandbox.shurjopayment.com/api/get_token' => Http::response([
                'token' => 'sandbox-token',
                'store_id' => 1,
                'token_type' => 'Bearer',
                'sp_code' => 1000,
                'message' => 'Ok',
            ]),
            'https://sandbox.shurjopayment.com/api/secret-pay' => Http::response([
                'checkout_url' => 'https://sandbox.shurjopayment.com/pay/donation-003',
                'sp_order_id' => 'SP-DON-003',
                'customer_order_id' => 'placeholder',
            ]),
        ]);

        $this->actingAs($user)
            ->post('/donate/start', [
                'category' => 'general_education_fund',
                'amount' => '3000',
            ])
            ->assertRedirect('/donate');

        $this->actingAs($user)
            ->post('/donate/checkout')
            ->assertRedirect('https://sandbox.shurjopayment.com/pay/donation-003');

        $payment = Payment::query()->firstOrFail();

        Http::fake([
            'https://sandbox.shurjopayment.com/api/get_token' => Http::response([
                'token' => 'sandbox-token',
                'store_id' => 1,
                'token_type' => 'Bearer',
                'sp_code' => 1000,
                'message' => 'Ok',
            ]),
            'https://sandbox.shurjopayment.com/api/verification' => Http::response([
                [
                    'order_id' => 'SP-DON-003',
                    'customer_order_id' => (string) data_get($payment->metadata, 'merchant_order_id'),
                    'amount' => (float) $payment->amount,
                    'currency' => $payment->currency,
                    'bank_status' => 'Success',
                    'sp_code' => 1000,
                    'value1' => (string) $payment->id,
                ],
            ]),
        ]);

        $this->actingAs($user)
            ->get('/donate/return/success?order_id=SP-DON-003')
            ->assertRedirect();

        $intent = DonationIntent::query()->firstOrFail();
        $payment->refresh();
        $this->assertSame(Payment::STATUS_PAID, $payment->status, $payment->status_reason);
        $record = DonationRecord::query()->firstOrFail();
        $receipt = Receipt::query()->firstOrFail();

        $this->assertSame(DonationIntent::DONOR_MODE_IDENTIFIED, $intent->donor_mode);
        $this->assertSame($user->id, $intent->user_id);
        $this->assertNull($intent->donor_id);
        $this->assertSame('general_education_fund', data_get($intent->metadata, 'category.key'));
        $this->assertSame($user->id, $payment->user_id);
        $this->assertSame($user->id, $record->user_id);
        $this->assertSame($user->id, $receipt->issued_to_user_id);
        $this->assertDatabaseCount('donors', 0);

        $this->actingAs($user)
            ->get(route('donations.payments.show', ['publicReference' => $intent->public_reference], false))
            ->assertOk()
            ->assertSeeText('Identified donation')
            ->assertSeeText('account-linked donation was settled successfully');
    }

    public function test_mismatched_verification_routes_the_donation_to_manual_review_without_creating_a_record_or_receipt(): void
    {
        Http::fake([
            'https://sandbox.shurjopayment.com/api/get_token' => Http::response([
                'token' => 'sandbox-token',
                'store_id' => 1,
                'token_type' => 'Bearer',
                'sp_code' => 1000,
                'message' => 'Ok',
            ]),
            'https://sandbox.shurjopayment.com/api/secret-pay' => Http::response([
                'checkout_url' => 'https://sandbox.shurjopayment.com/pay/donation-004',
                'sp_order_id' => 'SP-DON-004',
                'customer_order_id' => 'DON-DON-1',
            ]),
            'https://sandbox.shurjopayment.com/api/verification' => Http::response([
                [
                    'order_id' => 'SP-DON-004',
                    'customer_order_id' => 'DON-DON-999',
                    'amount' => 9999,
                    'currency' => 'BDT',
                    'bank_status' => 'Success',
                    'sp_code' => 1000,
                    'value1' => '1',
                ],
            ]),
        ]);

        $this->post('/donate/start', [
            'category' => 'student_support_and_guardian_care',
            'amount' => '1800',
        ])->assertRedirect('/donate');

        $this->post('/donate/checkout')
            ->assertRedirect('https://sandbox.shurjopayment.com/pay/donation-004');

        $intent = DonationIntent::query()->firstOrFail();

        $this->get('/donate/return/success?order_id=SP-DON-004')
            ->assertRedirect(route('donations.payments.show', ['publicReference' => $intent->public_reference], false));

        $payment = Payment::query()->firstOrFail();
        $intent->refresh();

        $this->assertSame(Payment::STATUS_MANUAL_REVIEW, $payment->status);
        $this->assertSame(Payment::VERIFICATION_MANUAL_REVIEW, $payment->verification_status);
        $this->assertSame(DonationIntent::STATUS_MANUAL_REVIEW, $intent->status);
        $this->assertDatabaseCount('donation_records', 0);
        $this->assertDatabaseCount('receipts', 0);
        $this->assertDatabaseCount('transactions', 0);
    }

    public function test_retry_reuses_the_same_open_intent_and_creates_a_new_payment_attempt(): void
    {
        Http::fake([
            'https://sandbox.shurjopayment.com/api/get_token' => Http::response([
                'token' => 'sandbox-token',
                'store_id' => 1,
                'token_type' => 'Bearer',
                'sp_code' => 1000,
                'message' => 'Ok',
            ]),
            'https://sandbox.shurjopayment.com/api/secret-pay' => Http::sequence()
                ->push([
                    'checkout_url' => 'https://sandbox.shurjopayment.com/pay/donation-005-a',
                    'sp_order_id' => 'SP-DON-005-A',
                    'customer_order_id' => 'DON-DON-1',
                ])
                ->push([
                    'checkout_url' => 'https://sandbox.shurjopayment.com/pay/donation-005-b',
                    'sp_order_id' => 'SP-DON-005-B',
                    'customer_order_id' => 'DON-DON-2',
                ]),
        ]);

        $this->post('/donate/start', [
            'category' => 'dawah_and_welfare_programs',
            'amount' => '2200',
        ])->assertRedirect('/donate');

        $this->post('/donate/checkout')
            ->assertRedirect('https://sandbox.shurjopayment.com/pay/donation-005-a');

        $firstIntent = DonationIntent::query()->firstOrFail();
        $firstPayment = Payment::query()->firstOrFail();

        $firstPayment->forceFill([
            'status' => Payment::STATUS_FAILED,
            'verification_status' => Payment::VERIFICATION_FAILED,
            'status_reason' => 'First attempt failed before settlement.',
            'failed_at' => now(),
        ])->save();

        $this->post('/donate/checkout')
            ->assertRedirect('https://sandbox.shurjopayment.com/pay/donation-005-b');

        $firstIntent->refresh();
        $payments = Payment::query()->orderBy('id')->get();

        $this->assertCount(2, $payments);
        $this->assertSame($firstIntent->id, $payments[0]->payable_id);
        $this->assertSame($firstIntent->id, $payments[1]->payable_id);
        $this->assertSame('SP-DON-005-B', $payments[1]->provider_reference);
        $this->assertSame(DonationIntent::STATUS_OPEN, $firstIntent->status);
        $this->assertDatabaseCount('donation_records', 0);
    }
}
