<?php

namespace Tests\Feature\Phase12;

use App\Contracts\Auth\GoogleOAuthBroker;
use App\Data\Auth\GoogleOAuthUser;
use App\Models\ExternalIdentity;
use App\Models\Guardian;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GoogleSignInFoundationTest extends TestCase
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

    public function test_first_time_public_google_sign_in_creates_one_registered_shared_account(): void
    {
        $this->bindGoogleBroker($this->googleUser());

        $this->get(route('google.redirect', ['intent' => 'public']))
            ->assertRedirect('https://accounts.google.test/oauth');

        $response = $this->get(route('google.callback'));

        $user = User::query()->where('email', 'public@example.com')->firstOrFail();

        $response->assertRedirect(route('dashboard', absolute: false));
        $this->assertAuthenticatedAs($user);
        $this->assertTrue($user->hasRole(User::ROLE_REGISTERED_USER));
        $this->assertNotNull($user->email_verified_at);
        $this->assertNull($user->donorProfile()->first());
        $this->assertNull($user->guardianProfile()->first());
        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseHas('external_identities', [
            'user_id' => $user->id,
            'provider' => ExternalIdentity::PROVIDER_GOOGLE,
            'provider_subject' => 'google-subject-1',
            'provider_email' => 'public@example.com',
            'provider_email_verified' => true,
        ]);

        $this->get('/dashboard')->assertRedirect(route('registration.onboarding', absolute: false));
    }

    public function test_first_time_donor_google_sign_in_creates_only_the_non_portal_donor_foundation(): void
    {
        $this->bindGoogleBroker($this->googleUser([
            'subject' => 'google-subject-donor',
            'email' => 'donor@example.com',
            'name' => 'Donor Google User',
        ]));

        $this->get(route('google.redirect', ['intent' => 'donor']))
            ->assertRedirect('https://accounts.google.test/oauth');

        $response = $this->get(route('google.callback'));

        $user = User::query()->where('email', 'donor@example.com')->firstOrFail();
        $donor = $user->donorProfile()->first();

        $response->assertRedirect(route('donor.dashboard', absolute: false));
        $this->assertAuthenticatedAs($user);
        $this->assertNotNull($donor);
        $this->assertFalse($donor->portal_enabled);
        $this->assertFalse($donor->isActived);
        $this->assertFalse($donor->isDeleted);
        $this->assertFalse($user->hasRole('donor'));
    }

    public function test_google_auto_link_keeps_unlinked_guardians_out_of_the_protected_portal(): void
    {
        $user = User::factory()->unverified()->create([
            'email' => 'guardian@example.com',
            'approval_status' => User::APPROVAL_NOT_REQUIRED,
            'account_status' => User::ACCOUNT_STATUS_ACTIVE,
        ]);

        $user->assignRole(User::ROLE_REGISTERED_USER);

        Guardian::query()->create([
            'user_id' => $user->id,
            'name' => 'Guardian User',
            'email' => $user->email,
            'portal_enabled' => true,
            'isActived' => true,
            'isDeleted' => false,
        ]);

        $this->bindGoogleBroker($this->googleUser([
            'subject' => 'google-subject-guardian',
            'email' => 'guardian@example.com',
        ]));

        $this->get(route('google.redirect'))
            ->assertRedirect('https://accounts.google.test/oauth');

        $response = $this->get(route('google.callback'));

        $response->assertRedirect(route('guardian.info.dashboard', absolute: false));
        $this->assertAuthenticatedAs($user->fresh());
        $this->assertNotNull($user->fresh()->email_verified_at);
        $this->assertDatabaseHas('external_identities', [
            'user_id' => $user->id,
            'provider_subject' => 'google-subject-guardian',
        ]);

        $this->get('/guardian')->assertForbidden();
        $this->get('/guardian/info')->assertOk();
    }

    public function test_google_sign_in_fails_closed_without_a_usable_verified_email(): void
    {
        $this->bindGoogleBroker($this->googleUser([
            'subject' => 'google-subject-no-verify',
            'email' => 'pending@example.com',
            'emailVerified' => false,
        ]));

        $this->get(route('google.redirect'))
            ->assertRedirect('https://accounts.google.test/oauth');

        $response = $this->get(route('google.callback'));

        $response->assertRedirect(route('login', absolute: false));
        $this->assertGuest();
        $this->assertDatabaseCount('users', 0);
        $this->assertDatabaseCount('external_identities', 0);
    }

    public function test_authenticated_google_link_can_attach_to_the_current_account_without_creating_a_duplicate_user(): void
    {
        $user = User::factory()->unverified()->create([
            'email' => 'linked@example.com',
            'approval_status' => User::APPROVAL_NOT_REQUIRED,
            'account_status' => User::ACCOUNT_STATUS_ACTIVE,
        ]);

        $user->assignRole(User::ROLE_REGISTERED_USER);

        $this->bindGoogleBroker($this->googleUser([
            'subject' => 'google-subject-link',
            'email' => 'linked@example.com',
        ]));

        $this->actingAs($user)
            ->post(route('google.link'))
            ->assertRedirect('https://accounts.google.test/oauth');

        $response = $this->actingAs($user)->get(route('google.callback'));

        $response->assertRedirect(route('profile.edit', absolute: false));
        $this->assertAuthenticatedAs($user->fresh());
        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseHas('external_identities', [
            'user_id' => $user->id,
            'provider_subject' => 'google-subject-link',
        ]);
        $this->assertNotNull($user->fresh()->email_verified_at);
    }

    public function test_authenticated_google_link_refuses_to_reassign_an_identity_from_another_account(): void
    {
        $linkedUser = User::factory()->create([
            'approval_status' => User::APPROVAL_NOT_REQUIRED,
            'account_status' => User::ACCOUNT_STATUS_ACTIVE,
        ]);

        $linkedUser->assignRole(User::ROLE_REGISTERED_USER);
        $linkedUser->externalIdentities()->create([
            'provider' => ExternalIdentity::PROVIDER_GOOGLE,
            'provider_subject' => 'google-subject-conflict',
            'provider_email' => 'linked-owner@example.com',
            'provider_email_verified' => true,
            'linked_at' => now(),
            'last_used_at' => now(),
        ]);

        $currentUser = User::factory()->unverified()->create([
            'email' => 'current@example.com',
            'approval_status' => User::APPROVAL_NOT_REQUIRED,
            'account_status' => User::ACCOUNT_STATUS_ACTIVE,
        ]);

        $currentUser->assignRole(User::ROLE_REGISTERED_USER);

        $this->bindGoogleBroker($this->googleUser([
            'subject' => 'google-subject-conflict',
            'email' => 'current@example.com',
        ]));

        $this->actingAs($currentUser)
            ->post(route('google.link'))
            ->assertRedirect('https://accounts.google.test/oauth');

        $response = $this->actingAs($currentUser)->get(route('google.callback'));

        $response->assertRedirect(route('profile.edit', absolute: false));
        $this->assertAuthenticatedAs($currentUser);
        $this->assertNull($currentUser->fresh()->googleIdentity()->first());
        $this->assertDatabaseHas('external_identities', [
            'user_id' => $linkedUser->id,
            'provider_subject' => 'google-subject-conflict',
        ]);
    }

    private function bindGoogleBroker(GoogleOAuthUser $googleUser): void
    {
        $this->app->instance(GoogleOAuthBroker::class, new FakeGoogleOAuthBroker($googleUser));
    }

    private function googleUser(array $overrides = []): GoogleOAuthUser
    {
        $defaults = [
            'subject' => 'google-subject-1',
            'email' => 'public@example.com',
            'emailVerified' => true,
            'name' => 'Google Public User',
            'avatar' => null,
        ];

        return new GoogleOAuthUser(...array_merge($defaults, $overrides));
    }
}

class FakeGoogleOAuthBroker implements GoogleOAuthBroker
{
    public function __construct(
        private readonly GoogleOAuthUser $googleUser,
    ) {
    }

    public function redirect(): \Illuminate\Http\RedirectResponse
    {
        return redirect()->away('https://accounts.google.test/oauth');
    }

    public function user(): GoogleOAuthUser
    {
        return $this->googleUser;
    }
}
