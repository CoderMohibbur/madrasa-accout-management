<?php

namespace App\Policies;

use App\Models\Receipt;
use App\Models\StudentFeeInvoice;
use App\Models\User;

class ReceiptPolicy
{
    public function view(User $user, Receipt $receipt): bool
    {
        if ($user->hasRole('management')) {
            return true;
        }

        if ($receipt->issued_to_user_id && $receipt->issued_to_user_id === $user->getKey()) {
            return true;
        }

        $payable = $receipt->payment?->payable;

        if ($payable instanceof StudentFeeInvoice) {
            return (new StudentFeeInvoicePolicy())->view($user, $payable);
        }

        return false;
    }
}
