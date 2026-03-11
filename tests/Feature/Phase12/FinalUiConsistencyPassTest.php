<?php

namespace Tests\Feature\Phase12;

use App\Models\Donor;
use App\Models\Guardian;
use App\Models\Role;
use App\Models\Student;
use App\Models\StudentFeeInvoice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FinalUiConsistencyPassTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::query()->create([
            'name' => User::ROLE_REGISTERED_USER,
            'display_name' => 'Registered User',
        ]);
        Role::query()->create(['name' => 'management', 'display_name' => 'Management']);
        Role::query()->create(['name' => 'guardian', 'display_name' => 'Guardian']);
        Role::query()->create(['name' => 'donor', 'display_name' => 'Donor']);

        config([
            'portal.admission.external_url' => 'https://attawheedic.com/admission/',
            'payments.manual_bank.enabled' => true,
            'payments.provider_mode' => 'sandbox',
        ]);
    }

    public function test_auth_and_profile_surfaces_render_the_shared_final_ui_patterns(): void
    {
        $user = User::factory()->create([
            'approval_status' => User::APPROVAL_NOT_REQUIRED,
            'account_status' => User::ACCOUNT_STATUS_ACTIVE,
        ]);
        $user->assignRole(User::ROLE_REGISTERED_USER);

        $this->get(route('login'))
            ->assertOk()
            ->assertSeeText('Google sign-in')
            ->assertSee('ui-checkbox', false)
            ->assertSee('ui-link', false);

        $this->get(route('password.request'))
            ->assertOk()
            ->assertSeeText('Send reset link')
            ->assertSee('ui-card__header', false);

        $this->get(route('password.reset', ['token' => 'reset-token', 'email' => 'reset@example.com']))
            ->assertOk()
            ->assertSeeText('Choose a new password')
            ->assertSee('ui-card__header', false);

        $this->actingAs($user)
            ->get(route('profile.edit'))
            ->assertOk()
            ->assertSeeText('Profile settings')
            ->assertSeeText('Google Sign-In')
            ->assertSee('ui-card__header', false);
    }

    public function test_donor_and_guardian_portal_views_render_shared_stat_and_table_shells(): void
    {
        $donorUser = User::factory()->create([
            'approval_status' => User::APPROVAL_NOT_REQUIRED,
            'account_status' => User::ACCOUNT_STATUS_ACTIVE,
        ]);
        $donorUser->assignRole(User::ROLE_REGISTERED_USER);

        Donor::query()->create([
            'user_id' => $donorUser->id,
            'name' => 'Portal Donor',
            'email' => $donorUser->email,
            'portal_enabled' => true,
            'isActived' => true,
            'isDeleted' => false,
        ]);

        $this->actingAs($donorUser)
            ->get(route('donor.dashboard'))
            ->assertOk()
            ->assertSee('ui-stat-card', false)
            ->assertSeeText('Recent donations');

        $this->actingAs($donorUser)
            ->get(route('donor.donations.index'))
            ->assertOk()
            ->assertSee('ui-table-shell', false);

        $this->actingAs($donorUser)
            ->get(route('donor.receipts.index'))
            ->assertOk()
            ->assertSee('ui-table-shell', false);

        ['guardianUser' => $guardianUser, 'invoice' => $invoice] = $this->makeProtectedGuardianFixture();

        $this->actingAs($guardianUser)
            ->get(route('guardian.dashboard'))
            ->assertOk()
            ->assertSee('ui-stat-card', false)
            ->assertSeeText('Linked students');

        $this->actingAs($guardianUser)
            ->get(route('guardian.invoices.index'))
            ->assertOk()
            ->assertSee('ui-table-shell', false);

        $this->actingAs($guardianUser)
            ->get(route('guardian.history.index'))
            ->assertOk()
            ->assertSee('ui-table-shell', false);

        $this->actingAs($guardianUser)
            ->get(route('guardian.invoices.show', $invoice))
            ->assertOk()
            ->assertSee('ui-table-shell', false)
            ->assertSeeText('Sandbox payment options');
    }

    public function test_public_and_guardian_informational_admission_surfaces_keep_the_shared_handoff_copy(): void
    {
        $guardianUser = User::factory()->unverified()->create([
            'approval_status' => User::APPROVAL_NOT_REQUIRED,
            'account_status' => User::ACCOUNT_STATUS_ACTIVE,
        ]);
        $guardianUser->assignRole(User::ROLE_REGISTERED_USER);

        Guardian::query()->create([
            'user_id' => $guardianUser->id,
            'name' => 'Guardian Info',
            'email' => $guardianUser->email,
            'portal_enabled' => false,
            'isActived' => true,
            'isDeleted' => false,
        ]);

        $this->get(route('admission'))
            ->assertOk()
            ->assertSeeText('External application handoff')
            ->assertSeeText('Open external application')
            ->assertSee('href="https://attawheedic.com/admission/"', false);

        $this->actingAs($guardianUser)
            ->get(route('guardian.info.admission'))
            ->assertOk()
            ->assertSeeText('External application handoff')
            ->assertSeeText('Open external application')
            ->assertSee('href="https://attawheedic.com/admission/"', false);
    }

    /**
     * @return array{guardianUser: User, guardian: Guardian, student: Student, invoice: StudentFeeInvoice}
     */
    private function makeProtectedGuardianFixture(): array
    {
        $guardianUser = User::factory()->create([
            'approval_status' => User::APPROVAL_APPROVED,
            'account_status' => User::ACCOUNT_STATUS_ACTIVE,
        ]);

        $guardian = Guardian::query()->create([
            'user_id' => $guardianUser->id,
            'name' => 'Guardian One',
            'email' => $guardianUser->email,
            'mobile' => '01700000000',
            'portal_enabled' => true,
            'isActived' => true,
            'isDeleted' => false,
        ]);

        $student = Student::query()->create([
            'full_name' => 'Protected Student',
            'roll' => 1,
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
            'invoice_number' => 'INV-FINAL-001',
            'status' => 'open',
            'issued_at' => now()->subDays(3)->toDateString(),
            'due_at' => now()->addDays(4)->toDateString(),
            'subtotal_amount' => 1200,
            'total_amount' => 1200,
            'paid_amount' => 0,
            'balance_amount' => 1200,
            'currency' => 'BDT',
        ]);

        return compact('guardianUser', 'guardian', 'student', 'invoice');
    }
}
