<?php

namespace App\Services\DonorPortal;

use App\Models\Donor;
use App\Models\DonationIntent;
use App\Models\DonationRecord;
use App\Models\Payment;
use App\Models\Receipt;
use App\Models\Transactions;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;
use Symfony\Component\HttpKernel\Exception\HttpException;

class DonorPortalData
{
    public function resolveAccess(User $user): DonorAccessState
    {
        $donor = $user->donorProfile()->first();
        $portalEligible = $donor
            && $donor->portal_enabled
            && $donor->isActived
            && ! $donor->isDeleted;
        $hasIdentifiedDonationContext = $this->hasIdentifiedDonationContext($user);
        $hasDonorContext = ! is_null($donor)
            || $user->hasRole('donor')
            || $hasIdentifiedDonationContext;

        $reason = match (true) {
            ! $user->hasAccessibleAccountState() => 'account_blocked',
            $portalEligible => 'portal_eligible',
            $donor && $donor->isDeleted => 'profile_deleted',
            $donor && ! $donor->portal_enabled => 'profile_pending',
            $donor && ! $donor->isActived => 'profile_inactive',
            $hasIdentifiedDonationContext => 'identified_only',
            $user->hasRole('donor') => 'role_only',
            default => 'none',
        };

        return new DonorAccessState(
            hasAccessibleAccountState: $user->hasAccessibleAccountState(),
            hasDonorContext: $hasDonorContext,
            portalEligible: $portalEligible,
            donor: $donor,
            reason: $reason,
        );
    }

    public function requireDonorAccess(User $user): DonorAccessState
    {
        $access = $this->resolveAccess($user);

        if (! $access->hasAccessibleAccountState || ! $access->hasDonorContext) {
            throw new HttpException(403, 'This account is not allowed to access donor routes.');
        }

        return $access;
    }

    public function requirePortalDonor(User $user): Donor
    {
        $access = $this->requireDonorAccess($user);

        if (! $access->portalEligible || ! $access->donor) {
            throw new HttpException(403, 'Donor portal access is not enabled for this user.');
        }

        return $access->donor;
    }

    public function shouldUseDonorHome(User $user): bool
    {
        if ($user->hasRole('management') || $user->guardianProfile()->exists() || $user->hasRole('guardian')) {
            return false;
        }

        $access = $this->resolveAccess($user);

        return $access->hasAccessibleAccountState && $access->hasDonorContext;
    }

    public function donationHistoryItems(Donor $donor): Collection
    {
        $legacyDonations = $this->legacyDonationQuery($donor)
            ->get()
            ->map(function (Transactions $transaction): array {
                return [
                    'source' => 'legacy_transaction',
                    'source_label' => 'Legacy record',
                    'title' => $transaction->c_s_1 ?: 'Donation #'.$transaction->id,
                    'reference' => 'TX-'.$transaction->id,
                    'account_name' => $transaction->account?->name ?: 'Account not set',
                    'note' => $transaction->note ?: 'Legacy donation entry',
                    'amount' => (float) $transaction->credit,
                    'currency' => 'BDT',
                    'status_label' => 'Recorded',
                    'display_label' => 'Portal-linked',
                    'occurred_at' => Carbon::parse($transaction->transactions_date ?: $transaction->created_at),
                ];
            });

        $onlineDonations = DonationRecord::query()
            ->with(['donationIntent', 'winningPayment.receipt'])
            ->where(function (Builder $query) use ($donor): void {
                $query
                    ->where('donor_id', $donor->getKey())
                    ->orWhere('user_id', $donor->user_id);
            })
            ->get()
            ->map(function (DonationRecord $record): array {
                $publicReference = $record->donationIntent?->public_reference ?: 'DON-'.$record->id;
                $displayLabel = $record->display_mode === DonationIntent::DISPLAY_MODE_ANONYMOUS
                    ? 'Anonymous display'
                    : 'Identified';

                return [
                    'source' => 'donation_record',
                    'source_label' => 'Online donation',
                    'title' => 'Online Donation '.$publicReference,
                    'reference' => $publicReference,
                    'account_name' => 'Secure checkout',
                    'note' => 'Verified through the donor checkout flow.',
                    'amount' => (float) $record->amount,
                    'currency' => $record->currency ?: 'BDT',
                    'status_label' => strtoupper($record->winningPayment?->status ?: Payment::STATUS_PAID),
                    'display_label' => $displayLabel,
                    'occurred_at' => $record->donated_at ?: $record->created_at,
                ];
            });

        return $legacyDonations
            ->concat($onlineDonations)
            ->sortByDesc(fn (array $item) => $item['occurred_at']?->getTimestamp() ?? 0)
            ->values();
    }

    public function receiptHistoryItems(Donor $donor): Collection
    {
        return $this->portalReceiptQuery($donor)
            ->get()
            ->map(function (Receipt $receipt): array {
                $isOnlineDonation = $receipt->payment?->payable_type === DonationIntent::class;

                return [
                    'source' => $isOnlineDonation ? 'donation_record' : 'legacy_receipt',
                    'source_label' => $isOnlineDonation ? 'Online donation' : 'Legacy receipt',
                    'receipt_number' => $receipt->receipt_number,
                    'provider' => $receipt->payment?->provider ?: 'manual',
                    'status_label' => strtoupper($receipt->payment?->status ?: 'recorded'),
                    'amount' => (float) $receipt->amount,
                    'currency' => $receipt->currency ?: 'BDT',
                    'reference' => $receipt->payment?->provider_reference ?: $receipt->payment?->idempotency_key ?: '-',
                    'issued_at' => $receipt->issued_at ?: $receipt->created_at,
                ];
            })
            ->sortByDesc(fn (array $item) => $item['issued_at']?->getTimestamp() ?? 0)
            ->values();
    }

    public function paginateItems(Collection $items, int $perPage, string $pageName = 'page'): LengthAwarePaginator
    {
        $page = Paginator::resolveCurrentPage($pageName);
        $pageItems = $items->forPage($page, $perPage)->values();

        return new LengthAwarePaginator(
            $pageItems,
            $items->count(),
            $perPage,
            $page,
            [
                'path' => Paginator::resolveCurrentPath(),
                'pageName' => $pageName,
            ],
        );
    }

    private function hasIdentifiedDonationContext(User $user): bool
    {
        return DonationIntent::query()->where('user_id', $user->getKey())->exists()
            || DonationRecord::query()->where('user_id', $user->getKey())->exists();
    }

    private function legacyDonationQuery(Donor $donor): Builder
    {
        return Transactions::query()
            ->with(['account', 'type'])
            ->where('doner_id', $donor->getKey())
            ->whereHas('type', function (Builder $query): void {
                $query->where('key', 'donation');
            })
            ->where(function (Builder $query): void {
                $query->whereNull('isDeleted')
                    ->orWhere('isDeleted', false);
            });
    }

    private function portalReceiptQuery(Donor $donor): Builder
    {
        return Receipt::query()
            ->with('payment')
            ->whereHas('payment', function (Builder $paymentQuery) use ($donor): void {
                $paymentQuery
                    ->where('user_id', $donor->user_id)
                    ->where(function (Builder $typeQuery): void {
                        $typeQuery
                            ->whereNull('payable_type')
                            ->orWhere('payable_type', DonationIntent::class);
                    });
            });
    }
}
