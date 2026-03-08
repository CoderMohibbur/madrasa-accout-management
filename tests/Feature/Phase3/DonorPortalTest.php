<?php

namespace Tests\Feature\Phase3;

use App\Models\Account;
use App\Models\Donor;
use App\Models\Payment;
use App\Models\Receipt;
use App\Models\Role;
use App\Models\Transactions;
use App\Models\TransactionsType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DonorPortalTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::query()->create(['name' => 'management', 'display_name' => 'Management']);
        Role::query()->create(['name' => 'guardian', 'display_name' => 'Guardian']);
        Role::query()->create(['name' => 'donor', 'display_name' => 'Donor']);
    }

    public function test_donor_dashboard_and_history_only_show_own_records(): void
    {
        [
            'donorUser' => $donorUser,
            'linkedDonation' => $linkedDonation,
            'hiddenDonation' => $hiddenDonation,
            'linkedReceipt' => $linkedReceipt,
            'hiddenReceipt' => $hiddenReceipt,
        ] = $this->makeDonorFixture();

        $this->actingAs($donorUser)
            ->get('/donor')
            ->assertOk()
            ->assertSeeText('Donor One')
            ->assertSeeText($linkedDonation->c_s_1)
            ->assertSeeText($linkedReceipt->receipt_number)
            ->assertDontSeeText($hiddenDonation->c_s_1)
            ->assertDontSeeText($hiddenReceipt->receipt_number);

        $this->actingAs($donorUser)
            ->get('/donor/donations')
            ->assertOk()
            ->assertSeeText($linkedDonation->c_s_1)
            ->assertDontSeeText($hiddenDonation->c_s_1);

        $this->actingAs($donorUser)
            ->get('/donor/receipts')
            ->assertOk()
            ->assertSeeText($linkedReceipt->receipt_number)
            ->assertDontSeeText($hiddenReceipt->receipt_number);
    }

    public function test_portal_only_donors_are_redirected_or_blocked_from_legacy_management_surfaces(): void
    {
        ['donorUser' => $donorUser] = $this->makeDonorFixture();

        $plainUser = User::factory()->create();

        $this->actingAs($donorUser)
            ->get('/dashboard')
            ->assertRedirect('/donor');

        $this->actingAs($donorUser)
            ->get('/donors')
            ->assertForbidden();

        $this->actingAs($plainUser)
            ->get('/donors')
            ->assertOk();
    }

    private function makeDonorFixture(): array
    {
        $donationType = TransactionsType::query()->create([
            'name' => 'Donation',
            'key' => 'donation',
            'isActived' => true,
            'isDeleted' => false,
        ]);

        $account = Account::query()->create([
            'name' => 'Main Cash',
            'account_number' => 'ACC-1001',
            'account_details' => 'General fund account',
            'opening_balance' => 0,
            'current_balance' => 0,
            'isActived' => true,
            'isDeleted' => false,
        ]);

        $donorUser = User::factory()->create();
        $donorUser->assignRole('donor');

        $donor = Donor::query()->create([
            'user_id' => $donorUser->id,
            'name' => 'Donor One',
            'email' => $donorUser->email,
            'mobile' => '01700000000',
            'portal_enabled' => true,
            'address' => 'Dhaka',
            'notes' => 'Supporter of the scholarship fund.',
            'isActived' => true,
            'isDeleted' => false,
        ]);

        $otherDonorUser = User::factory()->create();
        $otherDonorUser->assignRole('donor');

        $otherDonor = Donor::query()->create([
            'user_id' => $otherDonorUser->id,
            'name' => 'Donor Two',
            'email' => $otherDonorUser->email,
            'portal_enabled' => true,
            'isActived' => true,
            'isDeleted' => false,
        ]);

        $linkedDonation = Transactions::query()->create([
            'doner_id' => $donor->id,
            'transactions_type_id' => $donationType->id,
            'account_id' => $account->id,
            'transactions_date' => now()->subDays(4)->toDateString(),
            'note' => 'Scholarship support',
            'c_s_1' => 'Ramadan Donation',
            'total_fees' => 2500,
            'debit' => 0,
            'credit' => 2500,
            'isActived' => true,
            'isDeleted' => false,
            'created_by_id' => $donorUser->id,
        ]);

        $hiddenDonation = Transactions::query()->create([
            'doner_id' => $otherDonor->id,
            'transactions_type_id' => $donationType->id,
            'account_id' => $account->id,
            'transactions_date' => now()->subDays(2)->toDateString(),
            'note' => 'Building support',
            'c_s_1' => 'Hidden Donation',
            'total_fees' => 4000,
            'debit' => 0,
            'credit' => 4000,
            'isActived' => true,
            'isDeleted' => false,
            'created_by_id' => $otherDonorUser->id,
        ]);

        $linkedPayment = Payment::query()->create([
            'user_id' => $donorUser->id,
            'status' => 'paid',
            'provider' => 'manual',
            'currency' => 'BDT',
            'amount' => 2500,
            'idempotency_key' => 'donor-pay-linked-001',
            'provider_reference' => 'donor-provider-linked-001',
            'initiated_at' => now()->subDays(3),
            'paid_at' => now()->subDays(3),
        ]);

        $linkedReceipt = Receipt::query()->create([
            'payment_id' => $linkedPayment->id,
            'issued_to_user_id' => $donorUser->id,
            'receipt_number' => 'RCT-DONOR-LINKED-001',
            'currency' => 'BDT',
            'amount' => 2500,
            'issued_at' => now()->subDays(3),
        ]);

        $hiddenPayment = Payment::query()->create([
            'user_id' => $otherDonorUser->id,
            'status' => 'paid',
            'provider' => 'manual',
            'currency' => 'BDT',
            'amount' => 4000,
            'idempotency_key' => 'donor-pay-hidden-001',
            'provider_reference' => 'donor-provider-hidden-001',
            'initiated_at' => now()->subDay(),
            'paid_at' => now()->subDay(),
        ]);

        $hiddenReceipt = Receipt::query()->create([
            'payment_id' => $hiddenPayment->id,
            'issued_to_user_id' => $otherDonorUser->id,
            'receipt_number' => 'RCT-DONOR-HIDDEN-001',
            'currency' => 'BDT',
            'amount' => 4000,
            'issued_at' => now()->subDay(),
        ]);

        return compact(
            'donorUser',
            'donor',
            'linkedDonation',
            'hiddenDonation',
            'linkedReceipt',
            'hiddenReceipt',
        );
    }
}
