<?php

namespace Tests\Feature\Phase12;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OpenRegistrationFoundationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(ValidateCsrfToken::class);

        Role::query()->create(['name' => 'management', 'display_name' => 'Management']);
        Role::query()->create(['name' => 'guardian', 'display_name' => 'Guardian']);
        Role::query()->create(['name' => 'donor', 'display_name' => 'Donor']);
    }

    public function test_general_registration_creates_a_base_account_and_neutral_onboarding_state(): void
    {
        $response = $this->post('/register', [
            'name' => 'Public User',
            'email' => 'public@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'intent' => 'public',
        ]);

        $user = User::query()->where('email', 'public@example.com')->firstOrFail();

        $response->assertRedirect(route('registration.onboarding', absolute: false));
        $this->assertAuthenticatedAs($user);
        $this->assertSame(User::APPROVAL_NOT_REQUIRED, $user->approval_status);
        $this->assertSame(User::ACCOUNT_STATUS_ACTIVE, $user->account_status);
        $this->assertTrue($user->hasRole(User::ROLE_REGISTERED_USER));
        $this->assertFalse($user->hasRole('donor'));
        $this->assertFalse($user->hasRole('guardian'));
        $this->assertNull($user->donorProfile()->first());
        $this->assertNull($user->guardianProfile()->first());

        $this->get('/registration/onboarding')
            ->assertOk()
            ->assertSeeText('General account created');

        $this->get('/dashboard')->assertRedirect(route('verification.notice', absolute: false));
        $this->get('/students')->assertForbidden();
    }

    public function test_donor_registration_creates_a_non_portal_inactive_donor_profile_without_donor_role(): void
    {
        $response = $this->post('/register', [
            'name' => 'Donor User',
            'email' => 'donor@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'intent' => 'donor',
        ]);

        $user = User::query()->where('email', 'donor@example.com')->firstOrFail();
        $donor = $user->donorProfile()->first();

        $response->assertRedirect(route('registration.onboarding', absolute: false));
        $this->assertAuthenticatedAs($user);
        $this->assertNotNull($donor);
        $this->assertSame('Donor User', $donor->name);
        $this->assertSame('donor@example.com', $donor->email);
        $this->assertFalse($donor->portal_enabled);
        $this->assertFalse($donor->isActived);
        $this->assertFalse($donor->isDeleted);
        $this->assertFalse($user->hasRole('donor'));
        $this->assertTrue($user->hasRole(User::ROLE_REGISTERED_USER));

        $this->get('/registration/onboarding')
            ->assertOk()
            ->assertSeeText('Donor foundation created');
    }

    public function test_guardian_registration_creates_an_unlinked_non_protected_guardian_profile_without_guardian_role(): void
    {
        $response = $this->post('/register', [
            'name' => 'Guardian User',
            'email' => 'guardian@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'intent' => 'guardian',
        ]);

        $user = User::query()->where('email', 'guardian@example.com')->firstOrFail();
        $guardian = $user->guardianProfile()->first();

        $response->assertRedirect(route('registration.onboarding', absolute: false));
        $this->assertAuthenticatedAs($user);
        $this->assertNotNull($guardian);
        $this->assertSame('Guardian User', $guardian->name);
        $this->assertSame('guardian@example.com', $guardian->email);
        $this->assertFalse($guardian->portal_enabled);
        $this->assertTrue($guardian->isActived);
        $this->assertFalse($guardian->isDeleted);
        $this->assertFalse($user->hasRole('guardian'));
        $this->assertTrue($user->hasRole(User::ROLE_REGISTERED_USER));

        $this->get('/registration/onboarding')
            ->assertOk()
            ->assertSeeText('Guardian foundation created');
    }

    public function test_donor_and_guardian_registration_entry_pages_preselect_their_intents(): void
    {
        $this->get('/register/donor')
            ->assertOk()
            ->assertSee('value="donor"', false)
            ->assertSeeText('Donor foundation');

        $this->get('/register/guardian')
            ->assertOk()
            ->assertSee('value="guardian"', false)
            ->assertSeeText('Guardian foundation');
    }
}
