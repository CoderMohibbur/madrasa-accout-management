<?php

namespace App\Services\Payments;

use App\Models\StudentFeeInvoice;
use App\Models\User;
use App\Policies\StudentFeeInvoicePolicy;
use Illuminate\Auth\Access\AuthorizationException;

class StudentFeeInvoicePayableResolver
{
    public function resolveForUser(User $user, StudentFeeInvoice $invoice): ResolvedPayable
    {
        $invoice->loadMissing(['student', 'guardian']);

        if (! app(StudentFeeInvoicePolicy::class)->pay($user, $invoice)) {
            throw new AuthorizationException('You are not allowed to pay this invoice.');
        }

        $amount = (float) $invoice->balance_amount;

        if ($amount <= 0) {
            throw new \InvalidArgumentException('This invoice no longer has an outstanding balance.');
        }

        $customerDefaults = config('payments.customer_defaults');
        $guardian = $invoice->guardian ?: $user->guardianProfile()->first();

        return new ResolvedPayable(
            invoice: $invoice,
            user: $user,
            amount: $amount,
            currency: (string) ($invoice->currency ?: config('payments.default_currency', 'BDT')),
            customer: [
                'name' => (string) ($guardian?->name ?: $user->name),
                'email' => (string) ($guardian?->email ?: $user->email),
                'phone' => (string) ($guardian?->mobile ?: '01700000000'),
                'address' => (string) ($guardian?->address ?: $customerDefaults['address']),
                'city' => (string) $customerDefaults['city'],
                'post_code' => (string) $customerDefaults['post_code'],
                'state' => (string) $customerDefaults['state'],
                'country' => (string) $customerDefaults['country'],
            ],
            metadata: [
                'invoice_number' => $invoice->invoice_number,
                'student_id' => $invoice->student_id,
                'student_name' => $invoice->student?->full_name,
                'balance_amount' => $amount,
                'payable_type' => StudentFeeInvoice::class,
                'payable_id' => $invoice->getKey(),
            ],
        );
    }
}
