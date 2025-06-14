<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PaymentTestController extends Controller
{
    /**
     * Display the test card form page
     */
    public function showTestForm()
    {
        return view('payments.test');
    }

    /**
     * Create a test payment intent using PayMongo
     */
    public function createIntent(Request $request)
    {
        // ✅ Validate input
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'card_number' => 'required',
            'exp_month' => 'required|numeric',
            'exp_year' => 'required|numeric',
            'cvc' => 'required|numeric',
        ]);

        $secretKey = config('services.paymongo.secret');

        // ✅ 1. Create Payment Method
        $paymentMethodResponse = Http::withBasicAuth($secretKey, '')
            ->post('https://api.paymongo.com/v1/payment_methods', [
                'data' => [
                    'attributes' => [
                        'type' => 'card',
                        'details' => [
                            'card_number' => $request->card_number,
                            'exp_month'   => (int) $request->exp_month,
                            'exp_year'    => (int) $request->exp_year,
                            'cvc'         => $request->cvc,
                        ],
                        'billing' => [
                            'name'  => 'Test User',
                            'email' => 'test@example.com',
                            'phone' => '09123456789',
                        ]
                    ]
                ]
            ]);

        if ($paymentMethodResponse->failed()) {
            return back()->withErrors(['error' => '❌ Failed to create payment method'])->withInput();
        }

        $paymentMethodId = $paymentMethodResponse->json('data.id');

        // ✅ 2. Create Payment Intent
        $paymentIntentResponse = Http::withBasicAuth($secretKey, '')
            ->post('https://api.paymongo.com/v1/payment_intents', [
                'data' => [
                    'attributes' => [
                        'amount' => $request->amount * 100, // centavos
                        'payment_method_allowed' => ['card'],
                        'payment_method_options' => ['card'],
                        'currency' => 'PHP',
                        'description' => 'Test Transaction',
                        'capture_type' => 'automatic',
                        'statement_descriptor' => 'NutriApp Test'
                    ]
                ]
            ]);

if ($paymentIntentResponse->failed()) {
    dd([
        'status' => $paymentIntentResponse->status(),
        'body' => $paymentIntentResponse->json(),
    ]);
}

        $intentId = $paymentIntentResponse->json('data.id');

        // ✅ 3. Attach Payment Method
        $attachResponse = Http::withBasicAuth($secretKey, '')
            ->post("https://api.paymongo.com/v1/payment_intents/{$intentId}/attach", [
                'data' => [
                    'attributes' => [
                        'payment_method' => $paymentMethodId
                    ]
                ]
            ]);

        if ($attachResponse->failed()) {
            return back()->withErrors(['error' => '❌ Failed to attach payment method'])->withInput();
        }

        return redirect()->back()->with('success', '✅ Payment Intent Created and Attached Successfully!');
    }
}
