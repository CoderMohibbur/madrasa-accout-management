<?php

namespace App\Policies;

use App\Models\StudentFeeInvoice;
use App\Models\User;
use App\Services\GuardianPortal\GuardianPortalData;

class StudentFeeInvoicePolicy
{
    public function view(User $user, StudentFeeInvoice $invoice): bool
    {
        if (! $user->hasAccessibleAccountState()) {
            return false;
        }

        if ($user->hasRole('management')) {
            return true;
        }

        $access = app(GuardianPortalData::class)->resolveAccess($user);
        $guardian = $access->guardian;

        if (! $access->protectedEligible || ! $guardian) {
            return false;
        }

        if ($invoice->guardian_id && $invoice->guardian_id !== $guardian->getKey()) {
            return false;
        }

        return $guardian->students()->whereKey($invoice->student_id)->exists();
    }

    public function pay(User $user, StudentFeeInvoice $invoice): bool
    {
        return $this->view($user, $invoice);
    }
}
