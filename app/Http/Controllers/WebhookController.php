<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Payment;

class WebhookController extends Controller
{
    public function handle(Request $request)
    {
        // Optional: Validate using secret header (for extra security)
        // if ($request->header('Webhook-Secret') !== env('PAYMONGO_WEBHOOK_SECRET')) {
        //     return response('Unauthorized', 401);
        // }

        $payload = $request->all();

        if (!isset($payload['data']['attributes']['payment_intent_id'])) {
            return response('Invalid payload', 400);
        }

        $intentId = $payload['data']['attributes']['payment_intent_id'];
        $status = $payload['data']['attributes']['status'];

        // Log it
        Log::info('📥 PayMongo Webhook received', [
            'intent_id' => $intentId,
            'status' => $status
        ]);

        // Update matching payment
        $payment = Payment::where('intent_id', $intentId)->first();

        if ($payment) {
            $payment->status = $status;
            $payment->save();
        }

        return response('OK', 200);
    }
}
