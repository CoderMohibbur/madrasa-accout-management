<?php

namespace Tests\Unit\Policies;

use App\Models\Guardian;
use App\Models\Role;
use App\Models\Student;
use App\Models\StudentFeeInvoice;
use App\Models\User;
use App\Policies\StudentFeeInvoicePolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentFeeInvoicePolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_linked_guardians_can_view_their_invoices_but_unrelated_guardians_cannot(): void
    {
        Role::query()->create(['name' => 'management', 'display_name' => 'Management']);
        Role::query()->create(['name' => 'guardian', 'display_name' => 'Guardian']);

        $managementUser = User::factory()->create();
        $managementUser->assignRole('management');

        $guardianUser = User::factory()->create();
        $guardianUser->assignRole('guardian');

        $otherGuardianUser = User::factory()->create();
        $otherGuardianUser->assignRole('guardian');

        $guardian = Guardian::query()->create([
            'user_id' => $guardianUser->id,
            'name' => 'Guardian One',
            'portal_enabled' => true,
            'isActived' => true,
            'isDeleted' => false,
        ]);

        $otherGuardian = Guardian::query()->create([
            'user_id' => $otherGuardianUser->id,
            'name' => 'Guardian Two',
            'portal_enabled' => true,
            'isActived' => true,
            'isDeleted' => false,
        ]);

        $student = Student::query()->create([
            'full_name' => 'Student One',
        ]);

        $guardian->students()->attach($student->id, [
            'relationship_label' => 'Parent',
            'is_primary' => true,
        ]);

        $otherGuardian->students()->attach($student->id, [
            'relationship_label' => 'Relative',
            'is_primary' => false,
        ]);

        $invoice = StudentFeeInvoice::query()->create([
            'student_id' => $student->id,
            'guardian_id' => $guardian->id,
            'invoice_number' => 'INV-1001',
            'status' => 'issued',
            'issued_at' => now()->toDateString(),
            'due_at' => now()->addWeek()->toDateString(),
            'subtotal_amount' => 1000,
            'discount_amount' => 0,
            'total_amount' => 1000,
            'paid_amount' => 0,
            'balance_amount' => 1000,
        ]);

        $policy = new StudentFeeInvoicePolicy();

        $this->assertTrue($policy->view($managementUser, $invoice));
        $this->assertTrue($policy->view($guardianUser, $invoice));
        $this->assertFalse($policy->view($otherGuardianUser, $invoice));
    }
}
