<?php

namespace Tests\Feature\Phase12;

use App\Models\Guardian;
use App\Models\Role;
use App\Models\Student;
use App\Models\StudentFeeInvoice;
use App\Models\User;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GuardianProtectedPortalGatingTest extends TestCase
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

        config([
            'payments.manual_bank.enabled' => true,
            'payments.provider_mode' => 'sandbox',
        ]);
    }

    public function test_profile_only_linked_verified_guardians_can_use_protected_routes_and_manual_bank_entry(): void
    {
        ['guardianUser' => $guardianUser, 'invoice' => $invoice] = $this->makeProtectedGuardianFixture();

        $this->actingAs($guardianUser)
            ->get('/dashboard')
            ->assertRedirect('/guardian');

        $this->actingAs($guardianUser)
            ->get('/guardian')
            ->assertOk()
            ->assertSeeText('Protected Student')
            ->assertSeeText($invoice->invoice_number);

        $this->actingAs($guardianUser)
            ->post('/payments/manual-bank/requests', [
                'invoice_id' => $invoice->id,
                'payer_name' => 'Guardian One',
                'bank_reference' => 'GPROT-BANK-001',
                'payment_channel' => 'Bank Transfer',
                'transferred_at' => now()->toDateTimeString(),
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('payments', [
            'user_id' => $guardianUser->id,
            'payable_type' => StudentFeeInvoice::class,
            'payable_id' => $invoice->id,
            'provider' => 'manual_bank',
            'provider_reference' => 'GPROT-BANK-001',
        ]);
    }

    public function test_role_only_unlinked_and_unverified_guardian_contexts_fail_closed_on_protected_routes(): void
    {
        $roleOnly = User::factory()->create([
            'approval_status' => User::APPROVAL_APPROVED,
            'account_status' => User::ACCOUNT_STATUS_ACTIVE,
        ]);
        $roleOnly->assignRole('guardian');

        $this->actingAs($roleOnly)
            ->get('/guardian')
            ->assertForbidden();

        $unlinked = User::factory()->create([
            'approval_status' => User::APPROVAL_APPROVED,
            'account_status' => User::ACCOUNT_STATUS_ACTIVE,
        ]);

        Guardian::query()->create([
            'user_id' => $unlinked->id,
            'name' => 'Unlinked Guardian',
            'email' => $unlinked->email,
            'portal_enabled' => true,
            'isActived' => true,
            'isDeleted' => false,
        ]);

        $this->actingAs($unlinked)
            ->get('/dashboard')
            ->assertRedirect(route('guardian.info.dashboard', absolute: false));

        $this->actingAs($unlinked)
            ->get('/guardian')
            ->assertForbidden();

        $unverifiedLinked = User::factory()->unverified()->create([
            'approval_status' => User::APPROVAL_NOT_REQUIRED,
            'account_status' => User::ACCOUNT_STATUS_ACTIVE,
        ]);

        $guardian = Guardian::query()->create([
            'user_id' => $unverifiedLinked->id,
            'name' => 'Unverified Linked Guardian',
            'email' => $unverifiedLinked->email,
            'portal_enabled' => true,
            'isActived' => true,
            'isDeleted' => false,
        ]);

        $student = Student::query()->create([
            'full_name' => 'Linked Student',
            'roll' => 11,
            'isActived' => true,
            'isDeleted' => false,
        ]);

        $guardian->students()->attach($student->id, [
            'relationship_label' => 'Mother',
            'is_primary' => true,
        ]);

        $this->actingAs($unverifiedLinked)
            ->get('/guardian')
            ->assertForbidden();

        $this->actingAs($unverifiedLinked)
            ->get('/guardian/info')
            ->assertOk()
            ->assertSeeText('protected routes wait on email verification');
    }

    public function test_payment_entry_stays_fail_closed_for_other_protected_guardians(): void
    {
        ['invoice' => $invoice] = $this->makeProtectedGuardianFixture();
        ['guardianUser' => $otherGuardianUser] = $this->makeProtectedGuardianFixture([
            'invoice_number' => 'INV-GPROT-002',
            'student_name' => 'Other Student',
            'roll' => 22,
            'bank_reference' => 'GPROT-BANK-OTHER',
        ]);

        $this->actingAs($otherGuardianUser)
            ->post('/payments/manual-bank/requests', [
                'invoice_id' => $invoice->id,
                'payer_name' => 'Intruding Guardian',
                'bank_reference' => 'GPROT-BANK-999',
                'payment_channel' => 'Bank Transfer',
                'transferred_at' => now()->toDateTimeString(),
            ])
            ->assertForbidden();

        $this->assertDatabaseMissing('payments', [
            'provider' => 'manual_bank',
            'provider_reference' => 'GPROT-BANK-999',
        ]);
    }

    /**
     * @param  array{invoice_number?: string, student_name?: string, roll?: int}  $overrides
     * @return array{guardianUser: User, guardian: Guardian, student: Student, invoice: StudentFeeInvoice}
     */
    private function makeProtectedGuardianFixture(array $overrides = []): array
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
            'full_name' => $overrides['student_name'] ?? 'Protected Student',
            'roll' => $overrides['roll'] ?? 1,
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
            'invoice_number' => $overrides['invoice_number'] ?? 'INV-GPROT-001',
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
