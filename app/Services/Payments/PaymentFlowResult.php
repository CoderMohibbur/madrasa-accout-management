<?php

namespace App\Services\Payments;

use App\Models\Payment;

final class PaymentFlowResult
{
    public function __construct(
        public readonly string $status,
        public readonly ?Payment $payment,
        public readonly string $message,
        public readonly array $context = [],
    ) {
    }
}
