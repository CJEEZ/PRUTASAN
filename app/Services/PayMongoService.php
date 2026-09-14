<?php

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class PayMongoService
{
    private function client(): PendingRequest
    {
        $secretKey = config('services.paymongo.secret_key');

        if (!$secretKey) {
            throw new RuntimeException('PayMongo is not configured. Add PAYMONGO_SECRET_KEY to the environment.');
        }

        return Http::withBasicAuth($secretKey, '')
            ->acceptJson()
            ->baseUrl('https://api.paymongo.com/v1');
    }

    public function createCheckoutSession(int $orderId, string $orderNumber, array $lineItems): array
    {
        $response = $this->client()->post('/checkout_sessions', [
            'data' => [
                'attributes' => [
                    'line_items' => $lineItems,
                    'payment_method_types' => ['gcash'],
                    'description' => 'Order ' . $orderNumber,
                    'send_email_receipt' => false,
                    'metadata' => [
                        'order_id' => (string) $orderId,
                        'order_number' => $orderNumber,
                    ],
                    'success_url' => route('payment.paymongo.success', ['order' => $orderId]) . '?checkout_session_id={CHECKOUT_SESSION_ID}',
                    'cancel_url' => route('payment.paymongo.cancel', ['order' => $orderId]),
                ],
            ],
        ]);

        $response->throw();

        return $response->json('data');
    }

    public function retrieveCheckoutSession(string $sessionId): array
    {
        $response = $this->client()->get('/checkout_sessions/' . rawurlencode($sessionId));
        $response->throw();

        return $response->json('data');
    }

    public function isCheckoutPaid(array $session): bool
    {
        $attributes = $session['attributes'] ?? [];
        $payments = $attributes['payments'] ?? [];

        foreach ($payments as $payment) {
            if (($payment['attributes']['status'] ?? null) === 'paid') {
                return true;
            }
        }

        return ($attributes['payment_intent']['attributes']['status'] ?? null) === 'succeeded';
    }
}
