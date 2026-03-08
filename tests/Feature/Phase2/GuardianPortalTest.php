<?php

namespace Tests\Feature\Phase2;

use App\Models\Guardian;
use App\Models\Payment;
use App\Models\Receipt;
use App\Models\Role;
use App\Models\Student;
use App\Models\StudentFeeInvoice;
use App\Models\StudentFeeInvoiceItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GuardianPortalTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::query()->create(['name' => 'management', 'display_name' => 'Management']);
        Role::query()->create(['name' => 'guardian', 'display_name' => 'Guardian']);
        Role::query()->create(['name' => 'donor', 'display_name' => 'Donor']);
    }

    public function test_guardian_dashboard_and_history_only_show_linked_records(): void
    {
        [
            'guardianUser' => $guardianUser,
            'linkedInvoice' => $linkedInvoice,
            'hiddenInvoice' => $hiddenInvoice,
            'linkedReceipt' => $linkedReceipt,
            'hiddenReceipt' => $hiddenReceipt,
            'linkedStudent' => $linkedStudent,
            'hiddenStudent' => $hiddenStudent,
        ] = $this->makeGuardianFixture();

        $this->actingAs($guardianUser)
            ->get('/guardian')
            ->assertOk()
            ->assertSeeText($linkedStudent->full_name)
            ->assertSeeText($linkedInvoice->invoice_number)
            ->assertSeeText($linkedReceipt->receipt_number)
            ->assertDontSeeText($hiddenStudent->full_name)
            ->assertDontSeeText($hiddenInvoice->invoice_number)
            ->assertDontSeeText($hiddenReceipt->receipt_number);

        $this->actingAs($guardianUser)
            ->get('/guardian/history')
            ->assertOk()
            ->assertSeeText($linkedInvoice->invoice_number)
            ->assertSeeText($linkedReceipt->receipt_number)
            ->assertDontSeeText($hiddenInvoice->invoice_number)
            ->assertDontSeeText($hiddenReceipt->receipt_number);
    }

    public function test_guardian_student_and_invoice_pages_require_linked_records(): void
    {
        [
            'guardianUser' => $guardianUser,
            'linkedStudent' => $linkedStudent,
            'hiddenStudent' => $hiddenStudent,
            'linkedInvoice' => $linkedInvoice,
            'hiddenInvoice' => $hiddenInvoice,
        ] = $this->makeGuardianFixture();

        $this->actingAs($guardianUser)
            ->get("/guardian/students/{$linkedStudent->id}")
            ->assertOk()
            ->assertSeeText($linkedStudent->full_name);

        $this->actingAs($guardianUser)
            ->get("/guardian/invoices/{$linkedInvoice->id}")
            ->assertOk()
            ->assertSeeText($linkedInvoice->invoice_number);

        $this->actingAs($guardianUser)
            ->get("/guardian/students/{$hiddenStudent->id}")
            ->assertForbidden();

        $this->actingAs($guardianUser)
            ->get("/guardian/invoices/{$hiddenInvoice->id}")
            ->assertForbidden();
    }

    public function test_portal_only_guardians_are_redirected_or_blocked_from_legacy_management_surfaces(): void
    {
        ['guardianUser' => $guardianUser] = $this->makeGuardianFixture();

        $plainUser = User::factory()->create();

        $this->actingAs($guardianUser)
            ->get('/dashboard')
            ->assertRedirect('/guardian');

        $this->actingAs($guardianUser)
            ->get('/students')
            ->assertForbidden();

        $this->actingAs($plainUser)
            ->get('/students')
            ->assertOk();
    }

    private function makeGuardianFixture(): array
    {
        $guardianUser = User::factory()->create();
        $guardianUser->assignRole('guardian');

        $guardian = Guardian::query()->create([
            'user_id' => $guardianUser->id,
            'name' => 'Guardian One',
            'email' => $guardianUser->email,
            'mobile' => '01700000000',
            'portal_enabled' => true,
            'isActived' => true,
            'isDeleted' => false,
        ]);

        $otherGuardianUser = User::factory()->create();
        $otherGuardianUser->assignRole('guardian');

        $otherGuardian = Guardian::query()->create([
            'user_id' => $otherGuardianUser->id,
            'name' => 'Guardian Two',
            'email' => $otherGuardianUser->email,
            'portal_enabled' => true,
            'isActived' => true,
            'isDeleted' => false,
        ]);

        $linkedStudent = Student::query()->create([
            'full_name' => 'Linked Student',
            'roll' => 1,
            'isActived' => true,
            'isDeleted' => false,
        ]);

        $hiddenStudent = Student::query()->create([
            'full_name' => 'Hidden Student',
            'roll' => 2,
            'isActived' => true,
            'isDeleted' => false,
        ]);

        $guardian->students()->attach($linkedStudent->id, [
            'relationship_label' => 'Father',
            'is_primary' => true,
        ]);

        $otherGuardian->students()->attach($linkedStudent->id, [
            'relationship_label' => 'Sponsor',
            'is_primary' => false,
        ]);

        $otherGuardian->students()->attach($hiddenStudent->id, [
            'relationship_label' => 'Mother',
            'is_primary' => true,
        ]);

        $linkedInvoice = StudentFeeInvoice::query()->create([
            'student_id' => $linkedStudent->id,
            'guardian_id' => $guardian->id,
            'invoice_number' => 'INV-LINKED-001',
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

        $hiddenInvoice = StudentFeeInvoice::query()->create([
            'student_id' => $linkedStudent->id,
            'guardian_id' => $otherGuardian->id,
            'invoice_number' => 'INV-HIDDEN-001',
            'status' => 'open',
            'issued_at' => now()->subDays(4)->toDateString(),
            'due_at' => now()->addDays(4)->toDateString(),
            'subtotal_amount' => 800,
            'total_amount' => 800,
            'paid_amount' => 0,
            'balance_amount' => 800,
        ]);

        $hiddenStudentInvoice = StudentFeeInvoice::query()->create([
            'student_id' => $hiddenStudent->id,
            'guardian_id' => $otherGuardian->id,
            'invoice_number' => 'INV-HIDDEN-002',
            'status' => 'open',
            'issued_at' => now()->subDays(3)->toDateString(),
            'due_at' => now()->addDays(2)->toDateString(),
            'subtotal_amount' => 700,
            'total_amount' => 700,
            'paid_amount' => 0,
            'balance_amount' => 700,
        ]);

        $linkedPayment = Payment::query()->create([
            'user_id' => $guardianUser->id,
            'payable_type' => StudentFeeInvoice::class,
            'payable_id' => $linkedInvoice->id,
            'status' => 'paid',
            'provider' => 'manual',
            'currency' => 'BDT',
            'amount' => 600,
            'idempotency_key' => 'pay-linked-001',
            'provider_reference' => 'provider-linked-001',
            'initiated_at' => now()->subDays(2),
            'paid_at' => now()->subDays(2),
        ]);

        $linkedReceipt = Receipt::query()->create([
            'payment_id' => $linkedPayment->id,
            'issued_to_user_id' => $guardianUser->id,
            'receipt_number' => 'RCT-LINKED-001',
            'currency' => 'BDT',
            'amount' => 600,
            'issued_at' => now()->subDays(2),
        ]);

        $hiddenPayment = Payment::query()->create([
            'user_id' => $otherGuardianUser->id,
            'payable_type' => StudentFeeInvoice::class,
            'payable_id' => $hiddenStudentInvoice->id,
            'status' => 'paid',
            'provider' => 'manual',
            'currency' => 'BDT',
            'amount' => 700,
            'idempotency_key' => 'pay-hidden-001',
            'provider_reference' => 'provider-hidden-001',
            'initiated_at' => now()->subDay(),
            'paid_at' => now()->subDay(),
        ]);

        $hiddenReceipt = Receipt::query()->create([
            'payment_id' => $hiddenPayment->id,
            'issued_to_user_id' => $otherGuardianUser->id,
            'receipt_number' => 'RCT-HIDDEN-001',
            'currency' => 'BDT',
            'amount' => 700,
            'issued_at' => now()->subDay(),
        ]);

        return compact(
            'guardianUser',
            'guardian',
            'linkedStudent',
            'hiddenStudent',
            'linkedInvoice',
            'hiddenInvoice',
            'linkedReceipt',
            'hiddenReceipt',
        );
    }
}
