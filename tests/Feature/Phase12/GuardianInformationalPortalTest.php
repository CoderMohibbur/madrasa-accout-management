<?php

namespace Tests\Feature\Phase12;

use App\Models\Guardian;
use App\Models\Payment;
use App\Models\Receipt;
use App\Models\Role;
use App\Models\Student;
use App\Models\StudentFeeInvoice;
use App\Models\StudentFeeInvoiceItem;
use App\Models\User;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GuardianInformationalPortalTest extends TestCase
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
        Role::query()->create([
            'name' => 'guardian',
            'display_name' => 'Guardian',
        ]);
        Role::query()->create([
            'name' => 'donor',
            'display_name' => 'Donor',
        ]);
        Role::query()->create([
            'name' => 'management',
            'display_name' => 'Management',
        ]);

        config([
            'portal.admission.external_url' => 'https://attawheedic.com/admission/',
        ]);
    }

    public function test_unverified_guardian_foundation_accounts_can_log_in_and_use_the_guardian_informational_portal(): void
    {
        $user = User::factory()->unverified()->create([
            'email' => 'guardian-info@example.com',
            'approval_status' => User::APPROVAL_NOT_REQUIRED,
            'account_status' => User::ACCOUNT_STATUS_ACTIVE,
        ]);
        $user->assignRole(User::ROLE_REGISTERED_USER);

        Guardian::query()->create([
            'user_id' => $user->id,
            'name' => 'Guardian Foundation',
            'email' => $user->email,
            'portal_enabled' => false,
            'isActived' => true,
            'isDeleted' => false,
        ]);

        $this->post('/login', [
            'email' => 'guardian-info@example.com',
            'password' => 'password',
        ])->assertRedirect(route('guardian.info.dashboard', absolute: false));

        $this->get('/guardian/info')
            ->assertOk()
            ->assertSeeText('Guardian informational access')
            ->assertSeeText('Institution information')
            ->assertSeeText('Admission guidance')
            ->assertSeeText('Protected student, invoice, receipt, and payment details stay on separate guardian routes.');

        $this->get('/guardian/info/institution')
            ->assertOk()
            ->assertSeeText('Institution information')
            ->assertSeeText('Informational-only route');

        $this->get('/guardian/info/admission')
            ->assertOk()
            ->assertSeeText('Admission stays an external handoff')
            ->assertSeeText('Open external application');
    }

    public function test_verified_unlinked_guardian_accounts_redirect_from_dashboard_to_guardian_information_and_fail_closed_on_protected_routes(): void
    {
        $user = User::factory()->create();
        $user->assignRole(User::ROLE_REGISTERED_USER);

        Guardian::query()->create([
            'user_id' => $user->id,
            'name' => 'Verified Guardian Foundation',
            'email' => $user->email,
            'portal_enabled' => false,
            'isActived' => true,
            'isDeleted' => false,
        ]);

        $this->actingAs($user)
            ->get('/dashboard')
            ->assertRedirect(route('guardian.info.dashboard', absolute: false));

        $this->actingAs($user)
            ->get('/guardian')
            ->assertForbidden();
    }

    public function test_guardian_informational_surfaces_do_not_render_protected_records_and_keep_the_external_cta_off_the_protected_portal(): void
    {
        [
            'guardianUser' => $guardianUser,
            'linkedStudent' => $linkedStudent,
            'linkedInvoice' => $linkedInvoice,
            'linkedReceipt' => $linkedReceipt,
        ] = $this->makeProtectedGuardianFixture();

        $this->actingAs($guardianUser)
            ->get('/guardian')
            ->assertOk()
            ->assertSeeText($linkedStudent->full_name)
            ->assertSeeText($linkedInvoice->invoice_number)
            ->assertSeeText($linkedReceipt->receipt_number)
            ->assertDontSeeText('Open external application');

        $this->actingAs($guardianUser)
            ->get('/guardian/info')
            ->assertOk()
            ->assertDontSeeText($linkedStudent->full_name)
            ->assertDontSeeText($linkedInvoice->invoice_number)
            ->assertDontSeeText($linkedReceipt->receipt_number)
            ->assertDontSeeText('Recent Payment Activity')
            ->assertSeeText('Protected student, invoice, receipt, and payment details stay on separate guardian routes.');

        $this->actingAs($guardianUser)
            ->get('/guardian/info/admission')
            ->assertOk()
            ->assertSeeText('Open external application')
            ->assertSee('href="https://attawheedic.com/admission/"', false);
    }

    private function makeProtectedGuardianFixture(): array
    {
        $guardianUser = User::factory()->create();

        $guardian = Guardian::query()->create([
            'user_id' => $guardianUser->id,
            'name' => 'Linked Guardian',
            'email' => $guardianUser->email,
            'portal_enabled' => true,
            'isActived' => true,
            'isDeleted' => false,
        ]);

        $linkedStudent = Student::query()->create([
            'full_name' => 'Protected Student',
            'roll' => 1,
            'isActived' => true,
            'isDeleted' => false,
        ]);

        $guardian->students()->attach($linkedStudent->id, [
            'relationship_label' => 'Father',
            'is_primary' => true,
        ]);

        $linkedInvoice = StudentFeeInvoice::query()->create([
            'student_id' => $linkedStudent->id,
            'guardian_id' => $guardian->id,
            'invoice_number' => 'INV-GI-001',
            'status' => 'open',
            'issued_at' => now()->subDays(5)->toDateString(),
            'due_at' => now()->addDays(5)->toDateString(),
            'subtotal_amount' => 1200,
            'total_amount' => 1200,
            'paid_amount' => 600,
            'balance_amount' => 600,
        ]);

        StudentFeeInvoiceItem::query()->create([
            'student_fee_invoice_id' => $linkedInvoice->id,
            'title' => 'Monthly fee',
            'quantity' => 1,
            'unit_amount' => 1200,
            'line_total' => 1200,
        ]);

        $linkedPayment = Payment::query()->create([
            'user_id' => $guardianUser->id,
            'payable_type' => StudentFeeInvoice::class,
            'payable_id' => $linkedInvoice->id,
            'status' => 'paid',
            'provider' => 'manual',
            'currency' => 'BDT',
            'amount' => 600,
            'idempotency_key' => 'gi-payment-001',
            'provider_reference' => 'GI-PAY-001',
            'initiated_at' => now()->subDays(2),
            'paid_at' => now()->subDays(2),
        ]);

        $linkedReceipt = Receipt::query()->create([
            'payment_id' => $linkedPayment->id,
            'issued_to_user_id' => $guardianUser->id,
            'receipt_number' => 'RCT-GI-001',
            'currency' => 'BDT',
            'amount' => 600,
            'issued_at' => now()->subDays(2),
        ]);

        return compact('guardianUser', 'guardian', 'linkedStudent', 'linkedInvoice', 'linkedReceipt');
    }
}
