<?php

namespace App\Services\DonorPortal;

use App\Models\Donor;
use App\Models\Receipt;
use App\Models\Transactions;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Symfony\Component\HttpKernel\Exception\HttpException;

class DonorPortalData
{
    public function requireDonor(User $user): Donor
    {
        $donor = $user->donorProfile()->first();

        if (! $donor || ! $donor->portal_enabled || ! $donor->isActived || $donor->isDeleted) {
            throw new HttpException(403, 'Donor portal access is not enabled for this user.');
        }

        return $donor;
    }

    public function donationQuery(Donor $donor): Builder
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

    public function receiptQuery(Donor $donor): Builder
    {
        return Receipt::query()
            ->with('payment')
            ->where(function (Builder $query) use ($donor): void {
                $query
                    ->where('issued_to_user_id', $donor->user_id)
                    ->orWhereHas('payment', function (Builder $paymentQuery) use ($donor): void {
                        $paymentQuery->where('user_id', $donor->user_id);
                    });
            });
    }
}
