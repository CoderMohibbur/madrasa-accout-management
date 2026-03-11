<?php

namespace Tests\Feature\Phase12;

use App\Models\Donor;
use App\Models\Guardian;
use App\Models\Payment;
use App\Models\Role;
use App\Models\Student;
use App\Models\StudentFeeInvoice;
use App\Models\User;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route as RouteFacade;
use Tests\TestCase;

class RouteMiddlewarePolicyFinalizationTest extends TestCase
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

    public function test_route_buckets_now_use_explicit_context_middleware_and_remove_verified_from_payment_detail_routes(): void
    {
        $this->assertSame(
            ['web', 'auth', 'donor.access'],
            RouteFacade::getRoutes()->getByName('donor.dashboard')->gatherMiddleware()
        );
        $this->assertSame(
            ['web', 'auth', 'guardian.info.access'],
            RouteFacade::getRoutes()->getByName('guardian.info.dashboard')->gatherMiddleware()
        );
        $this->assertSame(
            ['web', 'auth', 'guardian.protected'],
            RouteFacade::getRoutes()->getByName('guardian.dashboard')->gatherMiddleware()
        );
        $this->assertSame(
            ['web', 'auth', 'portal.home'],
            RouteFacade::getRoutes()->getByName('dashboard')->gatherMiddleware()
        );

        $paymentShowMiddleware = RouteFacade::getRoutes()->getByName('payments.manual-bank.show')->gatherMiddleware();

        $this->assertContains('auth', $paymentShowMiddleware);
        $this->assertContains('can:view,payment', $paymentShowMiddleware);
        $this->assertNotContains('verified', $paymentShowMiddleware);

        $returnMiddleware = RouteFacade::getRoutes()->getByName('payments.shurjopay.return.success')->gatherMiddleware();

        $this->assertContains('auth', $returnMiddleware);
        $this->assertNotContains('verified', $returnMiddleware);

        $managementMiddleware = RouteFacade::getRoutes()->getByName('transactions.center')->gatherMiddleware();

        $this->assertContains('auth', $managementMiddleware);
        $this->assertContains('verified', $managementMiddleware);
        $this->assertContains('management.surface', $managementMiddleware);
    }

    public function test_donor_profile_accounts_without_a_raw_donor_role_cannot_fall_through_to_legacy_management_routes(): void
    {
        $user = User::factory()->create([
            'approval_status' => User::APPROVAL_APPROVED,
            'account_status' => User::ACCOUNT_STATUS_ACTIVE,
        ]);

        Donor::query()->create([
            'user_id' => $user->id,
            'name' => 'Profile Donor',
            'email' => $user->email,
            'portal_enabled' => true,
            'isActived' => true,
            'isDeleted' => false,
        ]);

        $this->actingAs($user)
            ->get('/dashboard')
            ->assertRedirect(route('donor.dashboard', absolute: false));

        $this->actingAs($user)
            ->get('/transaction-center')
            ->assertForbidden();
    }

    public function test_invoice_policies_require_protected_guardian_eligibility_for_view_and_pay(): void
    {
        ['owner' => $owner, 'invoice' => $invoice, 'student' => $student] = $this->makeGuardianInvoiceFixture();

        $this->assertTrue(Gate::forUser($owner)->allows('view', $invoice));
        $this->assertTrue(Gate::forUser($owner)->allows('pay', $invoice));

        $roleOnly = User::factory()->create([
            'approval_status' => User::APPROVAL_APPROVED,
            'account_status' => User::ACCOUNT_STATUS_ACTIVE,
        ]);
        $roleOnly->assignRole('guardian');

        $this->assertFalse(Gate::forUser($roleOnly)->allows('view', $invoice));
        $this->assertFalse(Gate::forUser($roleOnly)->allows('pay', $invoice));

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

        $guardian->students()->attach($student->id, [
            'relationship_label' => 'Aunt',
            'is_primary' => false,
        ]);

        $this->assertFalse(Gate::forUser($unverifiedLinked)->allows('view', $invoice));
        $this->assertFalse(Gate::forUser($unverifiedLinked)->allows('pay', $invoice));
    }

    public function test_payment_detail_routes_allow_the_exact_payer_without_verified_middleware_but_still_forbid_non_owner_guardian_contexts(): void
    {
        ['invoice' => $invoice] = $this->makeGuardianInvoiceFixture();

        $payer = User::factory()->unverified()->create([
            'approval_status' => User::APPROVAL_NOT_REQUIRED,
            'account_status' => User::ACCOUNT_STATUS_ACTIVE,
        ]);

        $payment = Payment::query()->create([
            'user_id' => $payer->id,
            'payable_type' => StudentFeeInvoice::class,
            'payable_id' => $invoice->id,
            'provider' => 'manual_bank',
            'status' => Payment::STATUS_AWAITING_MANUAL_PAYMENT,
            'verification_status' => Payment::VERIFICATION_PENDING,
            'currency' => 'BDT',
            'amount' => 1200,
            'idempotency_key' => 'prompt-40-manual-bank-view',
            'provider_reference' => 'PROMPT40-MANUAL-BANK',
        ]);

        $this->actingAs($payer)
            ->get(route('payments.manual-bank.show', $payment, false))
            ->assertOk();

        $unverifiedLinked = User::factory()->unverified()->create([
            'approval_status' => User::APPROVAL_NOT_REQUIRED,
            'account_status' => User::ACCOUNT_STATUS_ACTIVE,
        ]);

        $guardian = Guardian::query()->create([
            'user_id' => $unverifiedLinked->id,
            'name' => 'Unverified Secondary Guardian',
            'email' => $unverifiedLinked->email,
            'portal_enabled' => true,
            'isActived' => true,
            'isDeleted' => false,
        ]);

        $guardian->students()->attach($invoice->student_id, [
            'relationship_label' => 'Aunt',
            'is_primary' => false,
        ]);

        $this->actingAs($unverifiedLinked)
            ->get(route('payments.manual-bank.show', $payment, false))
            ->assertForbidden();
    }

    /**
     * @return array{owner: User, guardian: Guardian, student: Student, invoice: StudentFeeInvoice}
     */
    private function makeGuardianInvoiceFixture(): array
    {
        $owner = User::factory()->create([
            'approval_status' => User::APPROVAL_APPROVED,
            'account_status' => User::ACCOUNT_STATUS_ACTIVE,
        ]);

        $guardian = Guardian::query()->create([
            'user_id' => $owner->id,
            'name' => 'Owner Guardian',
            'email' => $owner->email,
            'portal_enabled' => true,
            'isActived' => true,
            'isDeleted' => false,
        ]);

        $student = Student::query()->create([
            'full_name' => 'Prompt 40 Student',
            'roll' => 401,
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
            'invoice_number' => 'INV-P40-001',
            'status' => 'open',
            'issued_at' => now()->subDays(2)->toDateString(),
            'due_at' => now()->addDays(7)->toDateString(),
            'subtotal_amount' => 1200,
            'total_amount' => 1200,
            'paid_amount' => 0,
            'balance_amount' => 1200,
            'currency' => 'BDT',
        ]);

        return compact('owner', 'guardian', 'student', 'invoice');
    }
}
