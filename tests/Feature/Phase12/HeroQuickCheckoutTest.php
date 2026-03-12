<?php

namespace Tests\Feature\Phase12;

use App\Http\Controllers\Donations\GuestDonationEntryController;
use App\Models\DonationCategory;
use App\Models\DonationIntent;
use App\Models\Payment;
use App\Models\Role;
use App\Models\User;
use App\Services\Donations\DonationCheckoutService;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request as HttpRequest;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class HeroQuickCheckoutTest extends TestCase
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

    public function test_hero_quick_checkout_validates_amount_phone_and_fund(): void
    {
        $this->from('/donate')->post('/donate/quick-checkout', [
            'fund' => 'not-an-approved-head',
            'amount' => '0',
            'phone' => '',
        ])->assertRedirect('/donate')
            ->assertSessionHasErrors(['fund', 'amount', 'phone']);

        $this->assertDatabaseCount('donation_intents', 0);
        $this->assertDatabaseCount('payments', 0);
        $this->assertDatabaseCount('users', 0);
        $this->assertDatabaseCount('donors', 0);
    }

    public function test_hero_quick_checkout_redirects_to_shurjopay_and_preserves_fund_metadata_without_creating_accounts(): void
    {
        $category = $this->categoryByKey('madrasa_complex');

        $this->fakeCheckout(
            checkoutUrl: 'https://sandbox.shurjopayment.com/pay/hero-quick-001',
            providerOrderId: 'SP-HERO-001',
        );

        $response = $this->withSession([
            DonationCheckoutService::CURRENT_INTENT_SESSION_KEY => 'DON-STALE-001',
            DonationCheckoutService::ACCESS_KEYS_SESSION_KEY => [
                'DON-STALE-001' => 'stale-access-key',
            ],
        ])->post('/donate/quick-checkout', [
            'fund' => $category->key,
            'amount' => '2750',
            'phone' => '01700 000000',
        ]);

        $response->assertRedirect('https://sandbox.shurjopayment.com/pay/hero-quick-001');

        $intent = DonationIntent::query()->firstOrFail();
        $payment = Payment::query()->firstOrFail();

        $this->assertSame(DonationIntent::DONOR_MODE_GUEST, $intent->donor_mode);
        $this->assertNull($intent->user_id);
        $this->assertSame($category->getKey(), $intent->donation_category_id);
        $this->assertSame('+8801700000000', $intent->phone_snapshot);
        $this->assertSame($category->key, data_get($intent->metadata, 'category.key'));
        $this->assertSame($category->displayLabel(), data_get($intent->metadata, 'category.label'));
        $this->assertSame($category->key, data_get($intent->metadata, 'fund.key'));
        $this->assertSame($category->displayLabel(), data_get($intent->metadata, 'fund.label'));
        $this->assertSame($category->key, data_get($payment->metadata, 'category.key'));
        $this->assertSame($category->displayLabel(), data_get($payment->metadata, 'category.label'));
        $this->assertSame($category->key, data_get($payment->metadata, 'fund.key'));
        $this->assertSame($category->displayLabel(), data_get($payment->metadata, 'fund.label'));

        $response->assertSessionHas(GuestDonationEntryController::DRAFT_SESSION_KEY, function (array $draft) use ($category): bool {
            return $draft['category_id'] === $category->getKey()
                && $draft['category_key'] === $category->key
                && $draft['fund_key'] === $category->key
                && $draft['phone'] === '+8801700000000'
                && $draft['amount'] === 2750.0
                && $draft['identity_mode'] === 'guest'
                && $draft['entry_context'] === 'public_session';
        });
        $response->assertSessionHas(DonationCheckoutService::CURRENT_INTENT_SESSION_KEY, $intent->public_reference);
        $response->assertSessionMissing(DonationCheckoutService::ACCESS_KEYS_SESSION_KEY.'.DON-STALE-001');
        $this->assertNotEmpty(session(DonationCheckoutService::ACCESS_KEYS_SESSION_KEY.'.'.$intent->public_reference));

        $this->assertDatabaseCount('users', 0);
        $this->assertDatabaseCount('donors', 0);

        Http::assertSent(function (HttpRequest $request): bool {
            return str_contains($request->url(), '/api/secret-pay')
                && $request['customer_phone'] === '+8801700000000'
                && $request['shipping_phone_number'] === '+8801700000000';
        });
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

    private function categoryByKey(string $key): DonationCategory
    {
        return DonationCategory::query()
            ->where('key', $key)
            ->firstOrFail();
    }
}
