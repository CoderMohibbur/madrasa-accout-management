<?php

namespace Tests\Feature\Phase12;

use App\Contracts\Auth\GoogleOAuthBroker;
use App\Data\Auth\GoogleOAuthUser;
use App\Models\Donor;
use App\Models\ExternalIdentity;
use App\Models\Guardian;
use App\Models\Role;
use App\Models\Student;
use App\Models\StudentFeeInvoice;
use App\Models\User;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MultiRoleHomeAndSwitchingTest extends TestCase
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
        Role::query()->create(['name' => 'management', 'display_name' => 'Management']);
        Role::query()->create(['name' => 'guardian', 'display_name' => 'Guardian']);
        Role::query()->create(['name' => 'donor', 'display_name' => 'Donor']);
    }

    public function test_unverified_multi_role_users_land_on_a_neutral_chooser_and_only_get_guardian_information_switching(): void
    {
        $user = User::factory()->unverified()->create([
            'email' => 'multi-info@example.com',
            'approval_status' => User::APPROVAL_NOT_REQUIRED,
            'account_status' => User::ACCOUNT_STATUS_ACTIVE,
        ]);
        $user->assignRole(User::ROLE_REGISTERED_USER);

        Donor::query()->create([
            'user_id' => $user->id,
            'name' => 'Multi Donor',
            'email' => $user->email,
            'portal_enabled' => false,
            'isActived' => true,
            'isDeleted' => false,
        ]);

        Guardian::query()->create([
            'user_id' => $user->id,
            'name' => 'Multi Guardian',
            'email' => $user->email,
            'portal_enabled' => true,
            'isActived' => true,
            'isDeleted' => false,
        ]);

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ])->assertRedirect(route('dashboard', absolute: false));

        $this->actingAs($user)
            ->get('/dashboard')
            ->assertOk()
            ->assertSeeText('Choose an eligible context')
            ->assertSeeText('Donor Home')
            ->assertSeeText('Guardian Information')
            ->assertSee(route('donor.dashboard', absolute: false), false)
            ->assertSee(route('guardian.info.dashboard', absolute: false), false)
            ->assertDontSeeText('Guardian Portal');

        $this->actingAs($user)
            ->get('/donor')
            ->assertOk()
            ->assertSeeText('Switch context')
            ->assertSee(route('dashboard', absolute: false), false)
            ->assertSee(route('guardian.info.dashboard', absolute: false), false)
            ->assertDontSeeText('Guardian Portal');

        $this->actingAs($user)
            ->get('/guardian')
            ->assertForbidden();
    }

    public function test_verified_multi_role_users_get_the_chooser_instead_of_a_raw_guardian_first_redirect_and_can_switch_between_portals(): void
    {
        $user = User::factory()->create([
            'approval_status' => User::APPROVAL_APPROVED,
            'account_status' => User::ACCOUNT_STATUS_ACTIVE,
        ]);
        $user->assignRole(User::ROLE_REGISTERED_USER);

        Donor::query()->create([
            'user_id' => $user->id,
            'name' => 'Portal Donor',
            'email' => $user->email,
            'portal_enabled' => true,
            'isActived' => true,
            'isDeleted' => false,
        ]);

        $guardian = Guardian::query()->create([
            'user_id' => $user->id,
            'name' => 'Portal Guardian',
            'email' => $user->email,
            'portal_enabled' => true,
            'isActived' => true,
            'isDeleted' => false,
        ]);

        $student = Student::query()->create([
            'full_name' => 'Protected Multi Student',
            'roll' => 21,
            'isActived' => true,
            'isDeleted' => false,
        ]);

        $guardian->students()->attach($student->id, [
            'relationship_label' => 'Mother',
            'is_primary' => true,
        ]);

        $invoice = StudentFeeInvoice::query()->create([
            'student_id' => $student->id,
            'guardian_id' => $guardian->id,
            'invoice_number' => 'INV-MULTI-001',
            'status' => 'open',
            'issued_at' => now()->subDays(2)->toDateString(),
            'due_at' => now()->addDays(5)->toDateString(),
            'subtotal_amount' => 1200,
            'total_amount' => 1200,
            'paid_amount' => 0,
            'balance_amount' => 1200,
            'currency' => 'BDT',
        ]);

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ])->assertRedirect(route('dashboard', absolute: false));

        $this->actingAs($user)
            ->get('/dashboard')
            ->assertOk()
            ->assertSeeText('Donor Portal')
            ->assertSeeText('Guardian Portal')
            ->assertDontSeeText($student->full_name)
            ->assertDontSeeText($invoice->invoice_number);

        $this->actingAs($user)
            ->get('/guardian')
            ->assertOk()
            ->assertSeeText('Switch context')
            ->assertSee(route('dashboard', absolute: false), false)
            ->assertSee(route('donor.dashboard', absolute: false), false);

        $this->actingAs($user)
            ->get('/donor')
            ->assertOk()
            ->assertSeeText('Switch context')
            ->assertSee(route('dashboard', absolute: false), false)
            ->assertSee(route('guardian.dashboard', absolute: false), false);
    }

    public function test_google_sign_in_reuses_the_multi_role_foundation_and_redirects_to_the_shared_chooser(): void
    {
        $user = User::factory()->unverified()->create([
            'email' => 'google-multi@example.com',
            'approval_status' => User::APPROVAL_NOT_REQUIRED,
            'account_status' => User::ACCOUNT_STATUS_ACTIVE,
        ]);
        $user->assignRole(User::ROLE_REGISTERED_USER);

        Donor::query()->create([
            'user_id' => $user->id,
            'name' => 'Google Multi Donor',
            'email' => $user->email,
            'portal_enabled' => false,
            'isActived' => true,
            'isDeleted' => false,
        ]);

        Guardian::query()->create([
            'user_id' => $user->id,
            'name' => 'Google Multi Guardian',
            'email' => $user->email,
            'portal_enabled' => true,
            'isActived' => true,
            'isDeleted' => false,
        ]);

        $this->bindGoogleBroker($this->googleUser([
            'subject' => 'google-subject-multi',
            'email' => $user->email,
        ]));

        $this->get(route('google.redirect'))
            ->assertRedirect('https://accounts.google.test/oauth');

        $response = $this->get(route('google.callback'));

        $response->assertRedirect(route('dashboard', absolute: false));
        $this->assertAuthenticatedAs($user->fresh());
        $this->assertDatabaseHas('external_identities', [
            'user_id' => $user->id,
            'provider' => ExternalIdentity::PROVIDER_GOOGLE,
            'provider_subject' => 'google-subject-multi',
        ]);

        $this->get('/dashboard')
            ->assertOk()
            ->assertSeeText('Donor Home')
            ->assertSeeText('Guardian Information');
    }

    private function bindGoogleBroker(GoogleOAuthUser $googleUser): void
    {
        $this->app->instance(GoogleOAuthBroker::class, new MultiRoleFakeGoogleOAuthBroker($googleUser));
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

class MultiRoleFakeGoogleOAuthBroker implements GoogleOAuthBroker
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
