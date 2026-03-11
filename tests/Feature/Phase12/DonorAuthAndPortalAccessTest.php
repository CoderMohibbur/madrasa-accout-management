<?php

namespace Tests\Feature\Phase12;

use App\Models\Account;
use App\Models\DonationIntent;
use App\Models\DonationRecord;
use App\Models\Donor;
use App\Models\Payment;
use App\Models\Receipt;
use App\Models\Role;
use App\Models\Transactions;
use App\Models\TransactionsType;
use App\Models\User;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DonorAuthAndPortalAccessTest extends TestCase
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
            'name' => 'donor',
            'display_name' => 'Donor',
        ]);
        Role::query()->create([
            'name' => 'guardian',
            'display_name' => 'Guardian',
        ]);
        Role::query()->create([
            'name' => 'management',
            'display_name' => 'Management',
        ]);
    }

    public function test_donor_foundation_accounts_can_log_in_to_the_donor_no_portal_state_without_email_verification(): void
    {
        $user = User::factory()->unverified()->create([
            'email' => 'foundation@example.com',
            'approval_status' => User::APPROVAL_NOT_REQUIRED,
            'account_status' => User::ACCOUNT_STATUS_ACTIVE,
        ]);
        $user->assignRole(User::ROLE_REGISTERED_USER);

        Donor::query()->create([
            'user_id' => $user->id,
            'name' => 'Foundation Donor',
            'email' => $user->email,
            'portal_enabled' => false,
            'isActived' => false,
            'isDeleted' => false,
        ]);

        $this->post('/login', [
            'email' => 'foundation@example.com',
            'password' => 'password',
        ])->assertRedirect(route('donor.dashboard', absolute: false));

        $this->get('/donor')
            ->assertOk()
            ->assertSeeText('Donor no-portal state')
            ->assertSeeText('portal history is still off')
            ->assertSeeText('Registration, login, and identified donation stay separate from donor portal eligibility.');

        $this->get('/donor/donations')
            ->assertRedirect(route('donor.dashboard', absolute: false));
    }

    public function test_portal_eligible_donor_history_includes_legacy_and_new_online_records_without_requiring_the_donor_role(): void
    {
        $donationType = TransactionsType::query()->create([
            'name' => 'Donation',
            'key' => 'donation',
            'isActived' => true,
            'isDeleted' => false,
        ]);

        $account = Account::query()->create([
            'name' => 'Main Cash',
            'account_number' => 'ACC-2001',
            'account_details' => 'General fund account',
            'opening_balance' => 0,
            'current_balance' => 0,
            'isActived' => true,
            'isDeleted' => false,
        ]);

        $user = User::factory()->unverified()->create([
            'approval_status' => User::APPROVAL_NOT_REQUIRED,
            'account_status' => User::ACCOUNT_STATUS_ACTIVE,
        ]);

        $donor = Donor::query()->create([
            'user_id' => $user->id,
            'name' => 'Portal Donor',
            'email' => $user->email,
            'portal_enabled' => true,
            'isActived' => true,
            'isDeleted' => false,
        ]);

        Transactions::query()->create([
            'doner_id' => $donor->id,
            'transactions_type_id' => $donationType->id,
            'account_id' => $account->id,
            'transactions_date' => now()->subDays(5)->toDateString(),
            'note' => 'Legacy donor entry',
            'c_s_1' => 'Legacy Ramadan Donation',
            'total_fees' => 2500,
            'debit' => 0,
            'credit' => 2500,
            'isActived' => true,
            'isDeleted' => false,
            'created_by_id' => $user->id,
        ]);

        $intent = DonationIntent::query()->create([
            'user_id' => $user->id,
            'donor_id' => $donor->id,
            'donor_mode' => DonationIntent::DONOR_MODE_IDENTIFIED,
            'display_mode' => DonationIntent::DISPLAY_MODE_ANONYMOUS,
            'amount' => 3200,
            'currency' => 'BDT',
            'status' => DonationIntent::STATUS_SUCCEEDED,
            'public_reference' => 'DON-P35-001',
            'guest_access_token_hash' => null,
            'name_snapshot' => 'Portal Donor',
            'email_snapshot' => $user->email,
            'metadata' => ['source' => 'phase-35-test'],
            'settled_at' => now()->subDay(),
        ]);

        $payment = Payment::query()->create([
            'user_id' => $user->id,
            'payable_type' => DonationIntent::class,
            'payable_id' => $intent->id,
            'status' => Payment::STATUS_PAID,
            'verification_status' => Payment::VERIFICATION_VERIFIED,
            'provider' => 'shurjopay',
            'currency' => 'BDT',
            'amount' => 3200,
            'idempotency_key' => 'p35-online-payment',
            'provider_reference' => 'SP-P35-001',
            'initiated_at' => now()->subDay(),
            'paid_at' => now()->subDay(),
            'verified_at' => now()->subDay(),
        ]);

        DonationRecord::query()->create([
            'donation_intent_id' => $intent->id,
            'winning_payment_id' => $payment->id,
            'user_id' => $user->id,
            'donor_id' => $donor->id,
            'donor_mode' => DonationIntent::DONOR_MODE_IDENTIFIED,
            'display_mode' => DonationIntent::DISPLAY_MODE_ANONYMOUS,
            'amount' => 3200,
            'currency' => 'BDT',
            'donated_at' => now()->subDay(),
            'posting_status' => DonationRecord::POSTING_SKIPPED,
            'name_snapshot' => 'Portal Donor',
            'email_snapshot' => $user->email,
        ]);

        Receipt::query()->create([
            'payment_id' => $payment->id,
            'issued_to_user_id' => $user->id,
            'receipt_number' => 'RCT-P35-001',
            'currency' => 'BDT',
            'amount' => 3200,
            'issued_at' => now()->subDay(),
        ]);

        $legacyReceiptPayment = Payment::query()->create([
            'user_id' => $user->id,
            'status' => Payment::STATUS_PAID,
            'provider' => 'manual',
            'currency' => 'BDT',
            'amount' => 2500,
            'idempotency_key' => 'p35-legacy-payment',
            'provider_reference' => 'LEG-P35-001',
            'initiated_at' => now()->subDays(4),
            'paid_at' => now()->subDays(4),
        ]);

        Receipt::query()->create([
            'payment_id' => $legacyReceiptPayment->id,
            'issued_to_user_id' => $user->id,
            'receipt_number' => 'RCT-P35-LEGACY',
            'currency' => 'BDT',
            'amount' => 2500,
            'issued_at' => now()->subDays(4),
        ]);

        $this->actingAs($user)
            ->get('/donor')
            ->assertOk()
            ->assertSeeText('Portal Donor')
            ->assertSeeText('Legacy Ramadan Donation')
            ->assertSeeText('Online Donation DON-P35-001')
            ->assertSeeText('RCT-P35-001');

        $this->actingAs($user)
            ->get('/donor/donations')
            ->assertOk()
            ->assertSeeText('Legacy record')
            ->assertSeeText('Online donation')
            ->assertSeeText('Anonymous display')
            ->assertSeeText('DON-P35-001');

        $this->actingAs($user)
            ->get('/donor/receipts')
            ->assertOk()
            ->assertSeeText('RCT-P35-001')
            ->assertSeeText('RCT-P35-LEGACY')
            ->assertSeeText('Online donation')
            ->assertSeeText('Legacy receipt');
    }

    public function test_identified_donors_without_portal_eligibility_stay_on_the_no_portal_surface(): void
    {
        $user = User::factory()->create([
            'approval_status' => User::APPROVAL_NOT_REQUIRED,
            'account_status' => User::ACCOUNT_STATUS_ACTIVE,
        ]);
        $user->assignRole(User::ROLE_REGISTERED_USER);

        $intent = DonationIntent::query()->create([
            'user_id' => $user->id,
            'donor_mode' => DonationIntent::DONOR_MODE_IDENTIFIED,
            'display_mode' => DonationIntent::DISPLAY_MODE_IDENTIFIED,
            'amount' => 1800,
            'currency' => 'BDT',
            'status' => DonationIntent::STATUS_SUCCEEDED,
            'public_reference' => 'DON-P35-IDENTIFIED',
            'name_snapshot' => $user->name,
            'email_snapshot' => $user->email,
            'settled_at' => now()->subHours(6),
        ]);

        $payment = Payment::query()->create([
            'user_id' => $user->id,
            'payable_type' => DonationIntent::class,
            'payable_id' => $intent->id,
            'status' => Payment::STATUS_PAID,
            'verification_status' => Payment::VERIFICATION_VERIFIED,
            'provider' => 'shurjopay',
            'currency' => 'BDT',
            'amount' => 1800,
            'idempotency_key' => 'p35-identified-only',
            'provider_reference' => 'SP-P35-IDENTIFIED',
            'initiated_at' => now()->subHours(6),
            'paid_at' => now()->subHours(6),
            'verified_at' => now()->subHours(6),
        ]);

        DonationRecord::query()->create([
            'donation_intent_id' => $intent->id,
            'winning_payment_id' => $payment->id,
            'user_id' => $user->id,
            'donor_mode' => DonationIntent::DONOR_MODE_IDENTIFIED,
            'display_mode' => DonationIntent::DISPLAY_MODE_IDENTIFIED,
            'amount' => 1800,
            'currency' => 'BDT',
            'donated_at' => now()->subHours(6),
            'posting_status' => DonationRecord::POSTING_SKIPPED,
            'name_snapshot' => $user->name,
            'email_snapshot' => $user->email,
        ]);

        $this->actingAs($user)
            ->get('/donor')
            ->assertOk()
            ->assertSeeText('identified donor activity')
            ->assertSeeText('payment completion does not auto-grant donor portal access');

        $this->actingAs($user)
            ->get('/donor/receipts')
            ->assertRedirect(route('donor.dashboard', absolute: false));
    }
}
