<?php

namespace Tests\Feature\Phase12;

use App\Http\Controllers\Donations\GuestDonationEntryController;
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
        $this->get('/donate')
            ->assertOk()
            ->assertSeeText('খাত নির্বাচন করুন, তারপর দ্রুত ও নিরাপদে অনুদান দিন')
            ->assertSeeText('খাত ও পরিমাণ নির্ধারণ করুন')
            ->assertSeeText('মাদ্রাসা কমপ্লেক্স')
            ->assertSeeText('মসজিদ কমপ্লেক্স')
            ->assertSeeText('সাধারণ শিক্ষা তহবিল')
            ->assertSeeText('এখনই অনুদান করুন')
            ->assertSeeText('রেজিস্ট্রেশন')
            ->assertSeeInOrder(['মাদ্রাসা কমপ্লেক্স', 'মসজিদ কমপ্লেক্স', 'সাধারণ শিক্ষা তহবিল'], false)
            ->assertSee('name="category"', false)
            ->assertSee('name="custom_amount"', false)
            ->assertDontSee('name="name"', false)
            ->assertDontSee('name="email"', false)
            ->assertDontSee('name="phone"', false)
            ->assertDontSee('name="anonymous_display"', false);
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
        $response = $this->from('/donate')->post('/donate/start', [
            'category' => 'madrasa_complex',
            'amount' => '1500',
            'checkout_now' => '1',
        ]);

        $response->assertStatus(307);
        $response->assertRedirect('/donate/checkout');
        $response->assertSessionHas(GuestDonationEntryController::DRAFT_SESSION_KEY, function (array $draft): bool {
            return $draft['category_key'] === 'madrasa_complex'
                && $draft['category_label'] === 'মাদ্রাসা কমপ্লেক্স'
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
        $response = $this->from('/donate')->post('/donate/start', [
            'category' => 'madrasa_complex',
            'amount' => '1500',
        ]);

        $response->assertRedirect('/donate');
        $response->assertSessionHas('guest_donation_message');
        $response->assertSessionHas(GuestDonationEntryController::DRAFT_SESSION_KEY, function (array $draft): bool {
            return $draft['category_key'] === 'madrasa_complex'
                && $draft['category_label'] === 'মাদ্রাসা কমপ্লেক্স'
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
        $response = $this->from('/donate')->post('/donate/start', [
            'category' => 'mosque_complex',
            'amount' => '2500',
            'name' => 'Guest Donor',
            'email' => 'GUEST@EXAMPLE.COM',
            'phone' => '01700 000000',
            'anonymous_display' => '1',
        ]);

        $response->assertRedirect('/donate');
        $response->assertSessionHas(GuestDonationEntryController::DRAFT_SESSION_KEY, function (array $draft): bool {
            return $draft['category_key'] === 'mosque_complex'
                && $draft['category_label'] === 'মসজিদ কমপ্লেক্স'
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
                'category' => 'general_education_fund',
                'amount' => '3200',
                'email' => 'guest-flow@example.com',
            ]);

        $response->assertRedirect('/donate');
        $response->assertSessionHas(GuestDonationEntryController::DRAFT_SESSION_KEY, function (array $draft): bool {
            return $draft['category_key'] === 'general_education_fund'
                && $draft['category_label'] === 'সাধারণ শিক্ষা তহবিল'
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
}
