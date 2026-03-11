<?php

namespace App\Policies;

use App\Models\Payment;
use App\Models\StudentFeeInvoice;
use App\Models\User;

class PaymentPolicy
{
    public function view(User $user, Payment $payment): bool
    {
        if (! $user->hasAccessibleAccountState()) {
            return false;
        }

        if ($user->hasRole('management')) {
            return true;
        }

        if ((int) $payment->user_id === (int) $user->getKey()) {
            return true;
        }

        $payable = $payment->payable;

        return $payable instanceof StudentFeeInvoice
            && app(StudentFeeInvoicePolicy::class)->view($user, $payable);
    }
}
