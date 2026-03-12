<?php

namespace Tests\Feature\Phase12;

use App\Models\DonationCategory;
use App\Models\DonationIntent;
use App\Models\DonationRecord;
use App\Models\Payment;
use App\Models\Receipt;
use App\Models\Role;
use App\Models\User;
use App\Services\Donations\DonationCheckoutService;
use Database\Seeders\DonationCategorySeeder;
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
        $category = $this->categoryByKey('madrasa_complex');

        $this->fakeCheckout(
            checkoutUrl: 'https://sandbox.shurjopayment.com/pay/donation-001',
            providerOrderId: 'SP-DON-001',
            customerOrderId: 'DON-DON-1',
        );

        $this->post('/donate/start', [
            'category' => $category->key,
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
        $this->assertSame($category->getKey(), $intent->donation_category_id);
        $this->assertSame('guest@example.com', $intent->email_snapshot);
        $this->assertSame(DonationIntent::STATUS_OPEN, $intent->status);
        $this->assertNotEmpty($intent->public_reference);
        $this->assertNotEmpty($intent->guest_access_token_hash);
        $this->assertSame($category->key, data_get($intent->metadata, 'category.key'));
        $this->assertSame($category->displayLabel(), data_get($intent->metadata, 'category.label'));
        $this->assertSame(DonationIntent::class, $payment->payable_type);
        $this->assertNull($payment->user_id);
        $this->assertSame(Payment::STATUS_REDIRECT_PENDING, $payment->status);
        $this->assertSame('SP-DON-001', $payment->provider_reference);
        $this->assertSame($intent->id, $payment->payable_id);
        $this->assertSame($intent->public_reference, data_get($payment->metadata, 'donation_intent.public_reference'));
        $this->assertSame($category->key, data_get($payment->metadata, 'category.key'));
        $this->assertSame($category->displayLabel(), data_get($payment->metadata, 'fund.label'));
        $this->assertSame($intent->public_reference, session(DonationCheckoutService::CURRENT_INTENT_SESSION_KEY));
        $this->assertNotEmpty(session(DonationCheckoutService::ACCESS_KEYS_SESSION_KEY.'.'.$intent->public_reference));
        $this->assertDatabaseCount('donation_records', 0);
    }

    public function test_guest_success_return_finalizes_the_donation_and_keeps_access_transaction_specific(): void
    {
        $category = $this->categoryByKey('mosque_complex');

        $this->fakeCheckout(
            checkoutUrl: 'https://sandbox.shurjopayment.com/pay/donation-002',
            providerOrderId: 'SP-DON-002',
        );

        $this->post('/donate/start', [
            'category' => $category->key,
            'amount' => '2500',
            'name' => 'Guest Donor',
        ])->assertRedirect('/donate');

        $this->post('/donate/checkout')
            ->assertRedirect('https://sandbox.shurjopayment.com/pay/donation-002');

        $payment = Payment::query()->firstOrFail();
        $intent = DonationIntent::query()->firstOrFail();
        $accessKey = session(DonationCheckoutService::ACCESS_KEYS_SESSION_KEY.'.'.$intent->public_reference);

        $this->fakeVerification($payment, 'SP-DON-002');

        $this->get('/donate/return/success?order_id=SP-DON-002')
            ->assertRedirect(route('donations.payments.show', ['publicReference' => $intent->public_reference], false));

        $payment->refresh();
        $intent->refresh();
        $record = DonationRecord::query()->first();
        $receipt = Receipt::query()->first();

        $this->assertSame(Payment::STATUS_PAID, $payment->status, $payment->status_reason);
        $this->assertSame(Payment::VERIFICATION_VERIFIED, $payment->verification_status);
        $this->assertSame(DonationIntent::STATUS_SUCCEEDED, $intent->status);
        $this->assertSame($category->getKey(), $intent->donation_category_id);
        $this->assertSame($category->key, data_get($intent->metadata, 'category.key'));
        $this->assertSame($category->displayLabel(), data_get($intent->metadata, 'category.label'));
        $this->assertNotNull($record);
        $this->assertNotNull($receipt);
        $this->assertNull($receipt->issued_to_user_id);
        $this->assertSame($intent->id, $record->donation_intent_id);
        $this->assertSame($payment->id, $record->winning_payment_id);
        $this->assertSame($category->getKey(), $record->donation_category_id);
        $this->assertSame($category->key, data_get($record->metadata, 'category.key'));
        $this->assertSame($category->displayLabel(), data_get($record->metadata, 'category.label'));
        $this->assertSame(DonationRecord::POSTING_SKIPPED, $record->posting_status);
        $this->assertDatabaseCount('transactions', 0);
        $this->assertDatabaseCount('users', 0);
        $this->assertDatabaseCount('donors', 0);

        $this->get(route('donations.payments.show', [
            'publicReference' => $intent->public_reference,
            'access_key' => $accessKey,
        ], false))
            ->assertOk()
            ->assertSeeText('অনুদান ভেরিফাইড')
            ->assertSeeText($category->displayLabel())
            ->assertSeeText($intent->public_reference)
            ->assertSeeText($receipt->receipt_number)
            ->assertSeeText('No account was created automatically.');
    }

    public function test_authenticated_users_can_complete_identified_checkout_without_donor_portal_gating_or_profile_creation(): void
    {
        $category = $this->categoryByKey('general_education_fund');

        $user = User::factory()->create([
            'approval_status' => User::APPROVAL_NOT_REQUIRED,
            'account_status' => User::ACCOUNT_STATUS_ACTIVE,
            'phone' => '+8801711111111',
        ]);
        $user->assignRole(User::ROLE_REGISTERED_USER);

        $this->fakeCheckout(
            checkoutUrl: 'https://sandbox.shurjopayment.com/pay/donation-003',
            providerOrderId: 'SP-DON-003',
        );

        $this->actingAs($user)
            ->post('/donate/start', [
                'category' => $category->key,
                'amount' => '3000',
            ])
            ->assertRedirect('/donate');

        $this->actingAs($user)
            ->post('/donate/checkout')
            ->assertRedirect('https://sandbox.shurjopayment.com/pay/donation-003');

        $payment = Payment::query()->firstOrFail();

        $this->fakeVerification($payment, 'SP-DON-003');

        $this->actingAs($user)
            ->get('/donate/return/success?order_id=SP-DON-003')
            ->assertRedirect();

        $intent = DonationIntent::query()->firstOrFail();
        $payment->refresh();
        $record = DonationRecord::query()->firstOrFail();
        $receipt = Receipt::query()->firstOrFail();

        $this->assertSame(Payment::STATUS_PAID, $payment->status, $payment->status_reason);
        $this->assertSame(DonationIntent::DONOR_MODE_IDENTIFIED, $intent->donor_mode);
        $this->assertSame($user->id, $intent->user_id);
        $this->assertNull($intent->donor_id);
        $this->assertSame($category->getKey(), $intent->donation_category_id);
        $this->assertSame($category->key, data_get($intent->metadata, 'category.key'));
        $this->assertSame($user->id, $payment->user_id);
        $this->assertSame($user->id, $record->user_id);
        $this->assertSame($category->getKey(), $record->donation_category_id);
        $this->assertSame($user->id, $receipt->issued_to_user_id);
        $this->assertDatabaseCount('donors', 0);

        $this->actingAs($user)
            ->get(route('donations.payments.show', ['publicReference' => $intent->public_reference], false))
            ->assertOk()
            ->assertSeeText('অ্যাকাউন্ট-সংযুক্ত অনুদান')
            ->assertSeeText($category->displayLabel())
            ->assertSeeText('account-linked donation was settled successfully');
    }

    public function test_historical_metadata_only_intent_status_page_still_shows_category_label(): void
    {
        $category = $this->categoryByKey('publication_library_and_infrastructure');

        $user = User::factory()->create([
            'approval_status' => User::APPROVAL_NOT_REQUIRED,
            'account_status' => User::ACCOUNT_STATUS_ACTIVE,
        ]);
        $user->assignRole(User::ROLE_REGISTERED_USER);

        $intent = DonationIntent::query()->create([
            'user_id' => $user->getKey(),
            'donor_mode' => DonationIntent::DONOR_MODE_IDENTIFIED,
            'display_mode' => DonationIntent::DISPLAY_MODE_IDENTIFIED,
            'amount' => 1900,
            'currency' => 'BDT',
            'status' => DonationIntent::STATUS_SUCCEEDED,
            'public_reference' => 'DON-HIST-001',
            'metadata' => [
                'category' => [
                    'key' => $category->key,
                    'label' => $category->displayLabel(),
                ],
            ],
            'settled_at' => now(),
        ]);

        Payment::query()->create([
            'user_id' => $user->getKey(),
            'payable_type' => DonationIntent::class,
            'payable_id' => $intent->getKey(),
            'status' => Payment::STATUS_PAID,
            'verification_status' => Payment::VERIFICATION_VERIFIED,
            'provider' => 'shurjopay',
            'provider_mode' => 'sandbox',
            'currency' => 'BDT',
            'amount' => 1900,
            'idempotency_key' => 'status-metadata-only',
            'provider_reference' => 'SP-HIST-001',
            'status_reason' => 'Historical verified payment.',
            'paid_at' => now(),
            'verified_at' => now(),
        ]);

        $this->actingAs($user)
            ->get(route('donations.payments.show', ['publicReference' => $intent->public_reference], false))
            ->assertOk()
            ->assertSeeText($category->displayLabel());
    }

    public function test_donation_category_seeder_backfills_known_historical_metadata_keys(): void
    {
        $category = $this->categoryByKey('student_support_and_guardian_care');

        $intent = DonationIntent::query()->create([
            'donor_mode' => DonationIntent::DONOR_MODE_GUEST,
            'display_mode' => DonationIntent::DISPLAY_MODE_IDENTIFIED,
            'amount' => 1800,
            'currency' => 'BDT',
            'status' => DonationIntent::STATUS_SUCCEEDED,
            'public_reference' => 'DON-BACKFILL-001',
            'guest_access_token_hash' => 'hash',
            'metadata' => [
                'category' => [
                    'key' => $category->key,
                    'label' => $category->displayLabel(),
                ],
            ],
            'settled_at' => now(),
        ]);

        $payment = Payment::query()->create([
            'payable_type' => DonationIntent::class,
            'payable_id' => $intent->getKey(),
            'status' => Payment::STATUS_PAID,
            'verification_status' => Payment::VERIFICATION_VERIFIED,
            'provider' => 'shurjopay',
            'provider_mode' => 'sandbox',
            'currency' => 'BDT',
            'amount' => 1800,
            'idempotency_key' => 'backfill-payment',
            'provider_reference' => 'SP-BACKFILL-001',
            'status_reason' => 'Historical verified payment.',
            'paid_at' => now(),
            'verified_at' => now(),
        ]);

        $record = DonationRecord::query()->create([
            'donation_intent_id' => $intent->getKey(),
            'winning_payment_id' => $payment->getKey(),
            'donor_mode' => DonationIntent::DONOR_MODE_GUEST,
            'display_mode' => DonationIntent::DISPLAY_MODE_IDENTIFIED,
            'amount' => 1800,
            'currency' => 'BDT',
            'donated_at' => now(),
            'posting_status' => DonationRecord::POSTING_SKIPPED,
            'metadata' => [
                'receipt_id' => null,
                'public_reference' => $intent->public_reference,
            ],
        ]);

        $intent->forceFill(['donation_category_id' => null])->save();
        $record->forceFill(['donation_category_id' => null])->save();

        $this->seed(DonationCategorySeeder::class);

        $intent->refresh();
        $record->refresh();

        $this->assertSame($category->getKey(), $intent->donation_category_id);
        $this->assertSame($category->getKey(), $record->donation_category_id);
        $this->assertSame($category->key, data_get($record->metadata, 'category.key'));
        $this->assertSame($category->displayLabel(), data_get($record->metadata, 'category.label'));
    }

    public function test_mismatched_verification_routes_the_donation_to_manual_review_without_creating_a_record_or_receipt(): void
    {
        $this->fakeCheckout(
            checkoutUrl: 'https://sandbox.shurjopayment.com/pay/donation-004',
            providerOrderId: 'SP-DON-004',
            customerOrderId: 'DON-DON-1',
        );

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

    private function fakeCheckout(
        string $checkoutUrl,
        string $providerOrderId,
        string $customerOrderId = 'placeholder',
    ): void {
        Http::fake([
            'https://sandbox.shurjopayment.com/api/get_token' => Http::response([
                'token' => 'sandbox-token',
                'store_id' => 1,
                'token_type' => 'Bearer',
                'sp_code' => 1000,
                'message' => 'Ok',
            ]),
            'https://sandbox.shurjopayment.com/api/secret-pay' => Http::response([
                'checkout_url' => $checkoutUrl,
                'sp_order_id' => $providerOrderId,
                'customer_order_id' => $customerOrderId,
            ]),
        ]);
    }

    private function fakeVerification(Payment $payment, string $providerOrderId, array $overrides = []): void
    {
        Http::fake([
            'https://sandbox.shurjopayment.com/api/get_token' => Http::response([
                'token' => 'sandbox-token',
                'store_id' => 1,
                'token_type' => 'Bearer',
                'sp_code' => 1000,
                'message' => 'Ok',
            ]),
            'https://sandbox.shurjopayment.com/api/verification' => Http::response([
                array_merge([
                    'order_id' => $providerOrderId,
                    'customer_order_id' => (string) data_get($payment->metadata, 'merchant_order_id'),
                    'amount' => (float) $payment->amount,
                    'currency' => $payment->currency,
                    'bank_status' => 'Success',
                    'sp_code' => 1000,
                    'value1' => (string) $payment->id,
                ], $overrides),
            ]),
        ]);
    }

    private function categoryByKey(string $key): DonationCategory
    {
        return DonationCategory::query()
            ->where('key', $key)
            ->firstOrFail();
    }
}
