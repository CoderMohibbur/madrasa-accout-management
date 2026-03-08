<?php

namespace App\Policies;

use App\Models\StudentFeeInvoice;
use App\Models\User;

class StudentFeeInvoicePolicy
{
    public function view(User $user, StudentFeeInvoice $invoice): bool
    {
        if ($user->hasRole('management')) {
            return true;
        }

        $guardian = $user->guardianProfile;

        if (! $guardian || ! $guardian->portal_enabled || ! $guardian->isActived || $guardian->isDeleted) {
            return false;
        }

        if ($invoice->guardian_id && $invoice->guardian_id !== $guardian->getKey()) {
            return false;
        }

        return $guardian->students()->whereKey($invoice->student_id)->exists();
    }
}
