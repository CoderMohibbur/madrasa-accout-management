<?php

namespace Tests\Feature\Phase12;

use App\Http\Controllers\Donations\GuestDonationEntryController;
use App\Models\DonationCategory;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GuestDonationEntryTest extends TestCase
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
    }

    public function test_public_users_can_view_the_guest_donation_entry_page(): void
    {
        $labels = DonationCategory::query()
            ->active()
            ->ordered()
            ->limit(3)
            ->get()
            ->map(fn (DonationCategory $category): string => $category->displayLabel())
            ->all();

        $this->get('/donate')
            ->assertOk()
            ->assertSeeInOrder($labels, false)
            ->assertSee('name="category"', false)
            ->assertSee('name="custom_amount"', false)
            ->assertSee('name="name"', false)
            ->assertSee('name="email"', false)
            ->assertSee('name="phone"', false)
            ->assertSee('name="anonymous_display"', false);
    }

    public function test_only_active_categories_appear_on_the_guest_donation_page(): void
    {
        $inactiveCategory = $this->categoryByKey('general_education_fund');

        $inactiveCategory->forceFill([
            'is_active' => false,
            'sort_order' => 999,
        ])->save();

        $activeLabels = DonationCategory::query()
            ->active()
            ->ordered()
            ->get()
            ->map(fn (DonationCategory $category): string => $category->displayLabel())
            ->all();

        $this->get('/donate')
            ->assertOk()
            ->assertSeeInOrder($activeLabels, false)
            ->assertDontSeeText($inactiveCategory->displayLabel());
    }

    public function test_guest_donation_start_requires_category_selection(): void
    {
        $this->from('/donate')->post('/donate/start', [
            'amount' => '1500',
        ])->assertRedirect('/donate')
            ->assertSessionHasErrors('category');
    }

    public function test_guest_donation_start_can_forward_straight_to_checkout_when_requested(): void
    {
        $category = $this->categoryByKey('madrasa_complex');

        $response = $this->from('/donate')->post('/donate/start', [
            'category' => $category->key,
            'amount' => '1500',
            'checkout_now' => '1',
        ]);

        $response->assertStatus(307);
        $response->assertRedirect('/donate/checkout');
        $response->assertSessionHas(GuestDonationEntryController::DRAFT_SESSION_KEY, function (array $draft) use ($category): bool {
            return $draft['category_id'] === $category->getKey()
                && $draft['category_key'] === $category->key
                && $draft['category_label'] === $category->displayLabel()
                && $draft['amount'] === 1500.0
                && $draft['name'] === null
                && $draft['email'] === null
                && $draft['phone'] === null
                && $draft['identity_mode'] === 'guest'
                && $draft['entry_context'] === 'public_session';
        });

        $this->assertDatabaseCount('users', 0);
        $this->assertDatabaseCount('donors', 0);
    }

    public function test_guest_donation_start_requires_category_and_amount_and_stays_guest_only(): void
    {
        $category = $this->categoryByKey('madrasa_complex');

        $response = $this->from('/donate')->post('/donate/start', [
            'category' => $category->key,
            'amount' => '1500',
        ]);

        $response->assertRedirect('/donate');
        $response->assertSessionHas('guest_donation_message');
        $response->assertSessionHas(GuestDonationEntryController::DRAFT_SESSION_KEY, function (array $draft) use ($category): bool {
            return $draft['category_id'] === $category->getKey()
                && $draft['category_key'] === $category->key
                && $draft['category_label'] === $category->displayLabel()
                && $draft['amount'] === 1500.0
                && $draft['name'] === null
                && $draft['email'] === null
                && $draft['phone'] === null
                && $draft['identity_mode'] === 'guest'
                && $draft['entry_context'] === 'public_session';
        });

        $this->assertDatabaseCount('users', 0);
        $this->assertDatabaseCount('donors', 0);
    }

    public function test_optional_guest_contact_fields_are_normalized_without_creating_accounts_or_donor_profiles(): void
    {
        $category = $this->categoryByKey('mosque_complex');

        $response = $this->from('/donate')->post('/donate/start', [
            'category' => $category->key,
            'amount' => '2500',
            'name' => 'Guest Donor',
            'email' => 'GUEST@EXAMPLE.COM',
            'phone' => '01700 000000',
            'anonymous_display' => '1',
        ]);

        $response->assertRedirect('/donate');
        $response->assertSessionHas(GuestDonationEntryController::DRAFT_SESSION_KEY, function (array $draft) use ($category): bool {
            return $draft['category_id'] === $category->getKey()
                && $draft['category_key'] === $category->key
                && $draft['category_label'] === $category->displayLabel()
                && $draft['amount'] === 2500.0
                && $draft['name'] === 'Guest Donor'
                && $draft['email'] === 'guest@example.com'
                && $draft['phone'] === '+8801700000000'
                && $draft['anonymous_display'] === true
                && $draft['identity_mode'] === 'guest';
        });

        $this->assertDatabaseCount('users', 0);
        $this->assertDatabaseCount('donors', 0);
    }

    public function test_authenticated_accounts_can_use_guest_entry_without_affecting_verification_state(): void
    {
        $category = $this->categoryByKey('general_education_fund');

        $user = User::factory()->create([
            'approval_status' => User::APPROVAL_NOT_REQUIRED,
            'account_status' => User::ACCOUNT_STATUS_ACTIVE,
            'email_verified_at' => now(),
            'phone' => '+8801710000000',
            'phone_verified_at' => now(),
        ]);
        $user->assignRole(User::ROLE_REGISTERED_USER);

        $response = $this->actingAs($user)
            ->from('/donate')
            ->post('/donate/start', [
                'category' => $category->key,
                'amount' => '3200',
                'email' => 'guest-flow@example.com',
            ]);

        $response->assertRedirect('/donate');
        $response->assertSessionHas(GuestDonationEntryController::DRAFT_SESSION_KEY, function (array $draft) use ($category): bool {
            return $draft['category_id'] === $category->getKey()
                && $draft['category_key'] === $category->key
                && $draft['category_label'] === $category->displayLabel()
                && $draft['amount'] === 3200.0
                && $draft['email'] === 'guest-flow@example.com'
                && $draft['entry_context'] === 'authenticated_session';
        });

        $user->refresh();

        $this->assertNotNull($user->email_verified_at);
        $this->assertNotNull($user->phone_verified_at);
        $this->assertSame('+8801710000000', $user->phone);
        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseCount('donors', 0);
    }

    public function test_guest_donation_start_rejects_inactive_categories(): void
    {
        $this->categoryByKey('mosque_complex')
            ->forceFill(['is_active' => false])
            ->save();

        $this->from('/donate')->post('/donate/start', [
            'category' => 'mosque_complex',
            'amount' => '2500',
        ])->assertRedirect('/donate')
            ->assertSessionHasErrors('category');
    }

    private function categoryByKey(string $key): DonationCategory
    {
        return DonationCategory::query()
            ->where('key', $key)
            ->firstOrFail();
    }
}
