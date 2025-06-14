<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymongoService
{
    protected $baseUrl;
    protected $secretKey;

    public function __construct()
    {
        $this->baseUrl = config('paymongo.base_url', 'https://api.paymongo.com/v1');
        $this->secretKey = env('PAYMONGO_SECRET_KEY');
    }

    public function createPaymentIntent($amount, $description = 'Test Transaction')
    {
        $response = Http::withBasicAuth($this->secretKey, '')
            ->post("{$this->baseUrl}/payment_intents", [
                'data' => [
                    'attributes' => [
                        'amount' => $amount,
                        'payment_method_allowed' => ['card'],
                        'currency' => 'PHP',
                        'description' => $description,
                    ]
                ]
            ]);

        return $response->json();
    }

    public function createPaymentMethod($cardDetails, $billing)
    {
        $response = Http::withBasicAuth($this->secretKey, '')
            ->post("{$this->baseUrl}/payment_methods", [
                'data' => [
                    'attributes' => [
                        'type' => 'card',
                        'details' => $cardDetails,
                        'billing' => $billing,
                    ]
                ]
            ]);

        return $response->json();
    }

    public function attachPaymentMethod($paymentIntentId, $paymentMethodId)
    {
        $response = Http::withBasicAuth($this->secretKey, '')
            ->post("{$this->baseUrl}/payment_intents/{$paymentIntentId}/attach", [
                'data' => [
                    'attributes' => [
                        'payment_method' => $paymentMethodId
                    ]
                ]
            ]);

        return $response->json();
    }

    /**
     * Create PayMongo Redirect Flow (GCash, Maya, Card)
     */
    public function createRedirectPayment($amount, array $billing, string $method = 'gcash')
    {
        $response = Http::withBasicAuth($this->secretKey, '')
            ->post("{$this->baseUrl}/payment_intents", [
                'data' => [
                    'attributes' => [
                        'amount' => $amount,
                        'payment_method_allowed' => [$method],
                        'payment_method_options' => [
                            'card' => ['request_three_d_secure' => 'any']
                        ],
                        'currency' => 'PHP',
                        'description' => 'Checkout from NutriApp',
                        'statement_descriptor' => 'NUTRITECH ONLINE STORE',
                        'billing' => $billing,
                        'redirect' => [
                            'success' => route('buyer.payment.success'),
                            'failed' => route('buyer.payment.failure')
                        ],
                    ]
                ]
            ]);

if ($response->successful()) {
    $nextUrl = $response['data']['attributes']['next_action']['redirect']['url'] ?? null;

    // If real redirect exists, use it
    if ($nextUrl) {
        return $nextUrl;
    }

    // Otherwise simulate mock redirection
    \Log::info('🔁 No redirect.url found. Using mock thank you route.');
    return route('buyer.checkout.thankYou');
}


        Log::error('❌ Failed to create payment intent (redirect)', [
            'status' => $response->status(),
            'body' => $response->body(),
        ]);

        throw new \Exception('Failed to create payment intent');
    }
}
