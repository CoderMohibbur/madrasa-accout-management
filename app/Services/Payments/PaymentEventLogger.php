<?php

namespace App\Services\Payments;

use App\Models\Payment;
use App\Models\PaymentGatewayEvent;
use Illuminate\Support\Str;

class PaymentEventLogger
{
    public function record(
        ?Payment $payment,
        string $provider,
        string $eventName,
        array $payload = [],
        string $requestSource = 'system',
        ?string $providerOrderId = null,
        ?int $httpStatus = null,
        string $processingStatus = 'received',
        bool $authenticityValid = false,
    ): PaymentGatewayEvent {
        return PaymentGatewayEvent::query()->create([
            'payment_id' => $payment?->getKey(),
            'provider' => $provider,
            'provider_event_id' => (string) Str::ulid(),
            'provider_order_id' => $providerOrderId,
            'event_name' => $eventName,
            'request_source' => $requestSource,
            'http_status' => $httpStatus,
            'signature_valid' => $authenticityValid,
            'processing_status' => $processingStatus,
            'payload' => $payload,
            'received_at' => now(),
            'processed_at' => in_array($processingStatus, ['processed', 'accepted', 'verified'], true) ? now() : null,
        ]);
    }
}
