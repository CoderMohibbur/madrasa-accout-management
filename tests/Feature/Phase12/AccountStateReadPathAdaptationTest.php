<?php

namespace Tests\Feature\Phase12;

use App\Models\Donor;
use App\Models\Guardian;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Tests\TestCase;

class AccountStateReadPathAdaptationTest extends TestCase
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

    public function test_login_uses_explicit_approval_and_account_state_instead_of_email_verified_at_only(): void
    {
        $approvedUnverifiedUser = User::factory()->create([
            'email_verified_at' => null,
            'approval_status' => User::APPROVAL_APPROVED,
            'account_status' => User::ACCOUNT_STATUS_ACTIVE,
        ]);

        $response = $this->post('/login', [
            'email' => $approvedUnverifiedUser->email,
            'password' => 'password',
        ]);

        $response->assertRedirect(route('dashboard', absolute: false));
        $this->assertAuthenticatedAs($approvedUnverifiedUser);

        $this->get('/dashboard')->assertRedirect(route('verification.notice', absolute: false));

        auth()->logout();

        $pendingVerifiedUser = User::factory()->create([
            'approval_status' => User::APPROVAL_PENDING,
            'account_status' => User::ACCOUNT_STATUS_ACTIVE,
        ]);

        $this->post('/login', [
            'email' => $pendingVerifiedUser->email,
            'password' => 'password',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_inactive_suspended_and_deleted_accounts_fail_closed_on_portal_entry_points(): void
    {
        $suspendedUser = User::factory()->create([
            'approval_status' => User::APPROVAL_APPROVED,
            'account_status' => User::ACCOUNT_STATUS_SUSPENDED,
        ]);
        $suspendedUser->assignRole('management');

        $this->actingAs($suspendedUser)
            ->get('/dashboard')
            ->assertForbidden();

        $inactiveGuardianUser = User::factory()->create([
            'approval_status' => User::APPROVAL_APPROVED,
            'account_status' => User::ACCOUNT_STATUS_INACTIVE,
        ]);
        $inactiveGuardianUser->assignRole('guardian');

        Guardian::query()->create([
            'user_id' => $inactiveGuardianUser->id,
            'name' => 'Inactive Guardian',
            'email' => $inactiveGuardianUser->email,
            'portal_enabled' => true,
            'isActived' => true,
            'isDeleted' => false,
        ]);

        $this->actingAs($inactiveGuardianUser)
            ->get('/guardian')
            ->assertForbidden();

        $deletedDonorUser = User::factory()->create([
            'approval_status' => User::APPROVAL_APPROVED,
            'account_status' => User::ACCOUNT_STATUS_ACTIVE,
            'deleted_at' => now(),
        ]);
        $deletedDonorUser->assignRole('donor');

        Donor::query()->create([
            'user_id' => $deletedDonorUser->id,
            'name' => 'Deleted Donor',
            'email' => $deletedDonorUser->email,
            'portal_enabled' => true,
            'isActived' => true,
            'isDeleted' => false,
        ]);

        $this->actingAs($deletedDonorUser)
            ->get('/donor')
            ->assertForbidden();
    }

    public function test_dashboard_redirects_guardian_role_only_accounts_to_guardian_information_while_still_requiring_eligible_donor_profiles(): void
    {
        $guardianRoleOnly = User::factory()->create([
            'approval_status' => User::APPROVAL_APPROVED,
            'account_status' => User::ACCOUNT_STATUS_ACTIVE,
        ]);
        $guardianRoleOnly->assignRole('guardian');

        $this->actingAs($guardianRoleOnly)
            ->get('/dashboard')
            ->assertRedirect(route('guardian.info.dashboard', absolute: false));

        $donorUser = User::factory()->create([
            'approval_status' => User::APPROVAL_APPROVED,
            'account_status' => User::ACCOUNT_STATUS_ACTIVE,
        ]);
        $donorUser->assignRole('donor');

        Donor::query()->create([
            'user_id' => $donorUser->id,
            'name' => 'Eligible Donor',
            'email' => $donorUser->email,
            'portal_enabled' => true,
            'isActived' => true,
            'isDeleted' => false,
        ]);

        $this->actingAs($donorUser)
            ->get('/dashboard')
            ->assertRedirect('/donor');
    }
}
