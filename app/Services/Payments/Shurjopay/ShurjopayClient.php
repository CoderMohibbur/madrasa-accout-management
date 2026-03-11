<?php

namespace App\Services\Payments\Shurjopay;

use App\Models\Payment;
use App\Services\Payments\ResolvedPayable;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class ShurjopayClient
{
    public function initiatePayment(ResolvedPayable $resolvedPayable, Payment $payment, string $merchantOrderId): array
    {
        $payload = [
            'prefix' => config('payments.shurjopay.order_prefix'),
            'return_url' => config('payments.shurjopay.success_url') ?: route('payments.shurjopay.return.success'),
            'fail_url' => config('payments.shurjopay.fail_url') ?: route('payments.shurjopay.return.fail'),
            'cancel_url' => config('payments.shurjopay.cancel_url') ?: route('payments.shurjopay.return.cancel'),
            'amount' => $resolvedPayable->amount,
            'order_id' => $merchantOrderId,
            'currency' => $resolvedPayable->currency,
            'customer_name' => $resolvedPayable->customer['name'],
            'customer_address' => $resolvedPayable->customer['address'],
            'customer_email' => $resolvedPayable->customer['email'],
            'customer_phone' => $resolvedPayable->customer['phone'],
            'customer_city' => $resolvedPayable->customer['city'],
            'customer_post_code' => $resolvedPayable->customer['post_code'],
            'customer_state' => $resolvedPayable->customer['state'],
            'customer_country' => $resolvedPayable->customer['country'],
            'shipping_address' => $resolvedPayable->customer['address'],
            'shipping_city' => $resolvedPayable->customer['city'],
            'shipping_country' => $resolvedPayable->customer['country'],
            'received_person_name' => $resolvedPayable->customer['name'],
            'shipping_phone_number' => $resolvedPayable->customer['phone'],
            'client_ip' => request()?->ip() ?: '127.0.0.1',
            'value1' => (string) $payment->getKey(),
            'value2' => $resolvedPayable->metadata['invoice_number'] ?? null,
            'value3' => $resolvedPayable->metadata['student_id'] ?? null,
            'value4' => $payment->idempotency_key,
        ];

        return $this->initiateCheckout($payload);
    }

    public function initiateCheckout(array $payload): array
    {
        $config = $this->activeConfig();
        $auth = $this->authenticate($config);
        $requestPayload = array_merge([
            'token' => $auth['token'],
            'store_id' => $auth['store_id'],
        ], $payload);

        $response = Http::acceptJson()
            ->asJson()
            ->withToken($auth['token'])
            ->timeout(20)
            ->post($config['base_url'].$config['endpoints']['payment'], $requestPayload);

        if (! $response->successful()) {
            throw new RuntimeException('shurjoPay initiation failed with HTTP '.$response->status().'.');
        }

        $responseData = $response->json();
        $checkoutUrl = $responseData['checkout_url'] ?? null;
        $providerOrderId = $responseData['sp_order_id'] ?? $responseData['order_id'] ?? null;

        if (! $checkoutUrl || ! $providerOrderId) {
            throw new RuntimeException('shurjoPay initiation response did not include checkout details.');
        }

        return [
            'auth' => $auth,
            'request' => $requestPayload,
            'response' => $responseData,
            'http_status' => $response->status(),
            'checkout_url' => $checkoutUrl,
            'provider_order_id' => $providerOrderId,
        ];
    }

    public function verifyPayment(string $providerOrderId): array
    {
        $config = $this->activeConfig();
        $auth = $this->authenticate($config);

        $response = Http::acceptJson()
            ->asJson()
            ->withToken($auth['token'])
            ->timeout(20)
            ->post($config['base_url'].$config['endpoints']['verify'], [
                'order_id' => $providerOrderId,
            ]);

        if (! $response->successful()) {
            throw new RuntimeException('shurjoPay verification failed with HTTP '.$response->status().'.');
        }

        return [
            'auth' => $auth,
            'response' => $response->json(),
            'http_status' => $response->status(),
        ];
    }

    private function authenticate(array $config): array
    {
        try {
            $response = Http::acceptJson()
                ->asJson()
                ->timeout(20)
                ->post($config['base_url'].$config['endpoints']['token'], [
                    'username' => $config['username'],
                    'password' => $config['password'],
                ]);
        } catch (ConnectionException $exception) {
            throw new RuntimeException('Could not connect to shurjoPay.', previous: $exception);
        }

        if (! $response->successful()) {
            throw new RuntimeException('shurjoPay authentication failed with HTTP '.$response->status().'.');
        }

        $data = $response->json();

        if (! ($data['token'] ?? null) || ! ($data['store_id'] ?? null)) {
            throw new RuntimeException('shurjoPay authentication response was incomplete.');
        }

        return $data;
    }

    private function activeConfig(): array
    {
        if (config('payments.provider_mode') !== 'sandbox') {
            throw new RuntimeException('Live shurjoPay mode is intentionally disabled in this phase.');
        }

        $sandbox = config('payments.shurjopay.sandbox');
        $baseUrl = rtrim((string) ($sandbox['base_url'] ?? ''), '/');

        if ($baseUrl === '' || ! ($sandbox['username'] ?? null) || ! ($sandbox['password'] ?? null)) {
            throw new RuntimeException('Sandbox shurjoPay credentials are not configured.');
        }

        return [
            'base_url' => $baseUrl,
            'username' => $sandbox['username'],
            'password' => $sandbox['password'],
            'endpoints' => config('payments.shurjopay.endpoints'),
        ];
    }
}
