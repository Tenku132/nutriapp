<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PaymongoService;
use App\Models\Payment;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    protected $paymongo;

    public function __construct(PaymongoService $paymongo)
    {
        $this->paymongo = $paymongo;
    }

    public function createPaymentIntent(Request $request)
    {
        $amount = $request->amount;
        $desc = $request->description ?? 'Test';
        return $this->paymongo->createPaymentIntent($amount, $desc);
    }

    public function createPaymentMethod(Request $request)
    {
        $card = $request->card;
        $billing = $request->billing;
        return $this->paymongo->createPaymentMethod($card, $billing);
    }

public function attachPaymentMethod(Request $request)
{
    $intentId = $request->intent_id;
    $methodId = $request->method_id;

    $response = $this->paymongo->attachPaymentMethod($intentId, $methodId);

    \Log::info('Attach Payment Method Response:', $response);

    // 🛠 Fix: access status from data.attributes.status
    $status = $response['data']['attributes']['status'] ?? null;

    if ($status === 'succeeded') {
        \Log::info('Saving payment to DB...', [
            'intent_id' => $intentId,
            'method_id' => $methodId,
            'amount' => $response['data']['attributes']['amount'] ?? 'missing',
            'status' => $status,
            'user_id' => auth()->id()
        ]);

        Payment::create([
            'payment_intent_id' => $intentId,
            'payment_method_id' => $methodId,
            'amount' => $response['data']['attributes']['amount'] ?? 0,
            'status' => $status,
            'user_id' => auth()->id() ?? 1,
        ]);
    } else {
        \Log::warning('Payment NOT saved — response not succeeded', [
            'intent_id' => $intentId,
            'status' => $status ?? 'no-status'
        ]);
    }

    return $response;
}

    public function index()
    {
        $payments = Payment::latest()->get();
        return view('payments.index', compact('payments'));
    }


    public function showForm()
    {
        return view('buyer.payment.form');
    }

    public function saveForm(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'address' => 'required|string',
            'city' => 'required|string',
            'region' => 'required|string',
            'postal_code' => 'required|string',
            'phone' => 'required|string',
        ]);

        session(['shipping_info' => $validated]);

        return redirect()->route('buyer.payment.review'); // you can replace this if needed
    }
}
