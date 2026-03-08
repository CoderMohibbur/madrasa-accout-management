<?php

namespace App\Services\Payments;

use App\Models\Payment;
use Illuminate\Support\Str;

class PaymentReferenceGenerator
{
    public function idempotencyKey(string $provider): string
    {
        return Str::upper($provider).'-'.Str::ulid();
    }

    public function merchantOrderId(Payment $payment, string $channel = 'INV'): string
    {
        $prefix = Str::upper((string) config('payments.shurjopay.order_prefix', 'HFS'));
        $prefix = Str::limit(preg_replace('/[^A-Z0-9]/', '', $prefix) ?: 'HFS', 5, '');
        $channel = Str::upper(preg_replace('/[^A-Z0-9]/', '', $channel) ?: 'INV');

        return "{$prefix}-{$channel}-{$payment->getKey()}";
    }

    public function receiptNumber(Payment $payment): string
    {
        $prefix = $payment->provider_mode === 'live'
            ? (string) config('payments.receipt.live_prefix', 'RCT-LIVE')
            : (string) config('payments.receipt.sandbox_prefix', 'RCT-SBX');

        return sprintf('%s-%06d', Str::upper($prefix), $payment->getKey());
    }
}
