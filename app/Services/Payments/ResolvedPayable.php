<?php

namespace App\Services\Payments;

use App\Models\StudentFeeInvoice;
use App\Models\User;

final class ResolvedPayable
{
    public function __construct(
        public readonly StudentFeeInvoice $invoice,
        public readonly User $user,
        public readonly float $amount,
        public readonly string $currency,
        public readonly array $customer,
        public readonly array $metadata,
    ) {
    }
}
