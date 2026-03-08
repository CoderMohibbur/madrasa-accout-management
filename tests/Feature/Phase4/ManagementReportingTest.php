<?php

namespace Tests\Feature\Phase4;

use App\Models\Payment;
use App\Models\Receipt;
use App\Models\Role;
use App\Models\Student;
use App\Models\StudentFeeInvoice;
use App\Models\Transactions;
use App\Models\TransactionsType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ManagementReportingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::query()->create(['name' => 'management', 'display_name' => 'Management']);
        Role::query()->create(['name' => 'guardian', 'display_name' => 'Guardian']);
        Role::query()->create(['name' => 'donor', 'display_name' => 'Donor']);
    }

    public function test_management_reporting_requires_management_role_and_shows_safe_domain_summaries(): void
    {
        [
            'manager' => $manager,
            'plainUser' => $plainUser,
            'openInvoice' => $openInvoice,
            'receipt' => $receipt,
        ] = $this->makeReportingFixture();

        $this->actingAs($manager)
            ->get('/management/reporting?from=2026-03-01&to=2026-03-31')
            ->assertOk()
            ->assertSeeText('Safe Management Reporting')
            ->assertSeeText('Student Fees')
            ->assertSeeText('Donations')
            ->assertSeeText($openInvoice->invoice_number)
            ->assertSeeText($receipt->receipt_number);

        $this->actingAs($plainUser)
            ->get('/management/reporting')
            ->assertForbidden();
    }

    public function test_management_reporting_respects_the_selected_date_range(): void
    {
        [
            'manager' => $manager,
            'inRangeInvoice' => $inRangeInvoice,
            'outOfRangeInvoice' => $outOfRangeInvoice,
            'inRangeReceipt' => $inRangeReceipt,
            'outOfRangeReceipt' => $outOfRangeReceipt,
        ] = $this->makeReportingFixture();

        $this->actingAs($manager)
            ->get('/management/reporting?from=2026-03-01&to=2026-03-31')
            ->assertOk()
            ->assertSeeText($inRangeInvoice->invoice_number)
            ->assertSeeText($inRangeReceipt->receipt_number)
            ->assertDontSeeText($outOfRangeInvoice->invoice_number)
            ->assertDontSeeText($outOfRangeReceipt->receipt_number);
    }

    private function makeReportingFixture(): array
    {
        $manager = User::factory()->create();
        $manager->assignRole('management');

        $plainUser = User::factory()->create();

        $studentFeeType = TransactionsType::query()->create([
            'name' => 'Student Fee',
            'key' => 'student_fee',
            'isActived' => true,
            'isDeleted' => false,
        ]);

        $donationType = TransactionsType::query()->create([
            'name' => 'Donation',
            'key' => 'donation',
            'isActived' => true,
            'isDeleted' => false,
        ]);

        $student = Student::query()->create([
            'full_name' => 'Report Student',
            'roll' => 10,
            'isActived' => true,
            'isDeleted' => false,
        ]);

        $openInvoice = StudentFeeInvoice::query()->create([
            'student_id' => $student->id,
            'invoice_number' => 'INV-OPEN-001',
            'status' => 'open',
            'issued_at' => '2026-03-10',
            'due_at' => '2026-03-15',
            'subtotal_amount' => 1200,
            'total_amount' => 1200,
            'paid_amount' => 400,
            'balance_amount' => 800,
        ]);

        $paidInvoice = StudentFeeInvoice::query()->create([
            'student_id' => $student->id,
            'invoice_number' => 'INV-PAID-001',
            'status' => 'paid',
            'issued_at' => '2026-03-12',
            'due_at' => '2026-03-20',
            'subtotal_amount' => 900,
            'total_amount' => 900,
            'paid_amount' => 900,
            'balance_amount' => 0,
        ]);

        $outOfRangeInvoice = StudentFeeInvoice::query()->create([
            'student_id' => $student->id,
            'invoice_number' => 'INV-OLD-001',
            'status' => 'open',
            'issued_at' => '2026-02-10',
            'due_at' => '2026-02-15',
            'subtotal_amount' => 700,
            'total_amount' => 700,
            'paid_amount' => 0,
            'balance_amount' => 700,
        ]);

        Transactions::query()->create([
            'student_id' => $student->id,
            'transactions_type_id' => $studentFeeType->id,
            'transactions_date' => '2026-03-11',
            'c_s_1' => 'March Student Fee',
            'total_fees' => 400,
            'debit' => 0,
            'credit' => 400,
            'isActived' => true,
            'isDeleted' => false,
        ]);

        Transactions::query()->create([
            'transactions_type_id' => $donationType->id,
            'transactions_date' => '2026-03-13',
            'c_s_1' => 'Scholarship Donation',
            'total_fees' => 1500,
            'debit' => 0,
            'credit' => 1500,
            'isActived' => true,
            'isDeleted' => false,
        ]);

        Transactions::query()->create([
            'transactions_type_id' => $donationType->id,
            'transactions_date' => '2026-02-05',
            'c_s_1' => 'Old Donation',
            'total_fees' => 900,
            'debit' => 0,
            'credit' => 900,
            'isActived' => true,
            'isDeleted' => false,
        ]);

        $payment = Payment::query()->create([
            'user_id' => $manager->id,
            'payable_type' => StudentFeeInvoice::class,
            'payable_id' => $paidInvoice->id,
            'status' => 'paid',
            'provider' => 'manual',
            'currency' => 'BDT',
            'amount' => 900,
            'idempotency_key' => 'mgmt-pay-in-range',
            'provider_reference' => 'mgmt-ref-in-range',
            'initiated_at' => '2026-03-13 10:00:00',
            'paid_at' => '2026-03-13 10:00:00',
        ]);

        $receipt = Receipt::query()->create([
            'payment_id' => $payment->id,
            'issued_to_user_id' => $manager->id,
            'receipt_number' => 'RCT-MGMT-001',
            'currency' => 'BDT',
            'amount' => 900,
            'issued_at' => '2026-03-13 10:00:00',
        ]);

        $oldPayment = Payment::query()->create([
            'user_id' => $manager->id,
            'payable_type' => StudentFeeInvoice::class,
            'payable_id' => $openInvoice->id,
            'status' => 'paid',
            'provider' => 'manual',
            'currency' => 'BDT',
            'amount' => 300,
            'idempotency_key' => 'mgmt-pay-old',
            'provider_reference' => 'mgmt-ref-old',
            'initiated_at' => '2026-02-12 10:00:00',
            'paid_at' => '2026-02-12 10:00:00',
        ]);

        $outOfRangeReceipt = Receipt::query()->create([
            'payment_id' => $oldPayment->id,
            'issued_to_user_id' => $manager->id,
            'receipt_number' => 'RCT-MGMT-OLD-001',
            'currency' => 'BDT',
            'amount' => 300,
            'issued_at' => '2026-02-12 10:00:00',
        ]);

        return [
            'manager' => $manager,
            'plainUser' => $plainUser,
            'openInvoice' => $openInvoice,
            'inRangeInvoice' => $openInvoice,
            'outOfRangeInvoice' => $outOfRangeInvoice,
            'receipt' => $receipt,
            'inRangeReceipt' => $receipt,
            'outOfRangeReceipt' => $outOfRangeReceipt,
        ];
    }
}
