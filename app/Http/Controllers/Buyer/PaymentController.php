<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Order;
use App\Models\User;
use App\Models\Product;
use App\Models\Payment;
use App\Services\PaymongoService;
use App\Notifications\FarmerOrderPlacedNotification;
use App\Notifications\AdminAlertNotification;

class PaymentController extends Controller
{
    public function checkoutPreview()
    {
        $cartIds = session('checkout_items', []);
        $buyerId = Auth::id();

        $cartItems = Cart::with('product')
            ->whereIn('id', $cartIds)
            ->where('buyer_id', $buyerId)
            ->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('buyer.cart.index')->with('error', 'No items to preview.');
        }

        $displayItems = $cartItems->map(function ($item) {
            return [
                'name' => $item->product->name,
                'quantity' => $item->quantity,
                'price' => $item->product->price,
                'total' => $item->product->price * $item->quantity,
            ];
        });

        $totalAmount = $displayItems->sum('total');

        return view('buyer.payment.checkout-preview', [
            'checkout_items' => $displayItems,
            'totalAmount' => $totalAmount,
        ]);
    }

    public function processForm(Request $request)
    {
        session([
            'checkout_phone' => $request->phone,
            'checkout_address' => $request->address,
            'checkout_city' => $request->city,
            'checkout_region' => $request->region,
            'checkout_postal_code' => $request->postal_code,
        ]);

        $request->validate([
            'contact_email' => 'required|email',
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'region' => 'required|string|max:100',
            'postal_code' => 'required|string|max:20',
            'phone' => 'required|string|max:20',
            'payment_method' => 'required|in:gcash,paymaya,card',
        ]);

        $amount = 0;
        $cartIds = session('checkout_items', []);
        $cartItems = Cart::with(['product' => fn($q) => $q->withTrashed()])
            ->whereIn('id', $cartIds)
            ->where('buyer_id', Auth::id())
            ->get();

        foreach ($cartItems as $item) {
            $amount += $item->product->price * $item->quantity;
        }

        $amount = (int) ($amount * 100);

        $billing = [
            'name' => $request->first_name . ' ' . $request->last_name,
            'email' => $request->contact_email,
            'phone' => $request->phone,
            'address' => [
                'line1' => $request->address,
                'city' => $request->city,
                'state' => $request->region,
                'postal_code' => $request->postal_code,
                'country' => 'PH',
            ],
        ];

        $redirectUrl = app(PaymongoService::class)->createRedirectPayment(
            $amount, $billing, $request->payment_method
        );

        return redirect()->away($redirectUrl);
    }

    public function paymentSuccess()
    {
        $buyer = Auth::user();
        $intentId = request()->query('payment_intent') ?? 'test_' . Str::random(6);
        $method = request()->query('method') ?? 'unknown';

        $cartIds = session('checkout_items', []);
        $cartItems = Cart::with(['product' => fn($q) => $q->withTrashed()])
            ->whereIn('id', $cartIds)
            ->where('buyer_id', $buyer->id)
            ->get();

        $amount = 0;

        foreach ($cartItems as $item) {
            $product = $item->product;
            $amount += $product->price * $item->quantity;

            $farmer = $product->user;

            $order = Order::create([
                'order_code' => Order::generateStructuredOrderCode($farmer),
                'buyer_id' => $buyer->id,
                'product_id' => $product->id,
                'farmer_id' => $farmer->id,
                'quantity' => $item->quantity,
                'price' => $product->price,
                'total_price' => $product->price * $item->quantity,
                'status' => 'Pending',
                'payment_status' => 'paid',
                'buyer_phone' => session('checkout_phone'),
                'buyer_address' => session('checkout_address'),
                'buyer_city' => session('checkout_city'),
                'buyer_region' => session('checkout_region'),
                'buyer_postal_code' => session('checkout_postal_code'),
            ]);

            $this->notifyUsers($order, $buyer);
        }


        Cart::whereIn('id', $cartIds)->delete();
        session()->forget('checkout_items');

        Payment::create([
            'intent_id' => $intentId,
            'method' => $method,
            'amount' => $amount * 100,
            'status' => 'paid',
            'buyer_id' => $buyer->id,
        ]);

        return redirect()->route('buyer.orders.confirmation')->with('success', 'Order placed successfully!');
    }

    protected function notifyUsers(Order $order, $buyer)
    {
        if ($farmer = User::find($order->farmer_id)) {
            $farmer->notify(new FarmerOrderPlacedNotification($order));
        }

        $admins = User::where('role', 'admin')->get();
        Notification::send($admins, new AdminAlertNotification([
            'message' => 'Order ' . $order->order_code . ' has been placed by ' . $buyer->name,
            'icon' => 'bi-cart-check',
            'link' => route('admin.orders.index', ['highlight_order' => $order->id]),
            'type' => 'order',
        ]));
    }

    public function thankYou()
    {
        $reference = session('reference') ?? 'MOCK_REF_' . Str::random(6);
        return view('buyer.payment.thank-you', compact('reference'));
    }


    public function review()
    {
        $buyNow = session('buy_now');
        $items = [];
        $totalAmount = 0;

        if ($buyNow) {
            $product = Product::withTrashed()->findOrFail($buyNow['product_id']);
            $items[] = [
                'product' => $product,
                'quantity' => $buyNow['quantity'],
                'total' => $product->price * $buyNow['quantity'],
            ];
            $totalAmount = $items[0]['total'];
        } else {
            $items = Cart::with(['product' => fn($q) => $q->withTrashed()])
                ->where('buyer_id', Auth::id())
                ->get()
                ->map(fn($item) => [
                    'product' => $item->product,
                    'quantity' => $item->quantity,
                    'total' => $item->product->price * $item->quantity,
                ]);

            $totalAmount = $items->sum('total');
        }

        return view('buyer.payment.review', compact('items', 'totalAmount', 'buyNow'));
    }

    public function showForm()
    {
        return view('buyer.payment.form');
    }

    public function paymentFailure()
    {
        return view('buyer.payment.failure');
    }

    public function mockSuccess(Request $request)
    {
        $request->validate([
            'contact_email' => 'required|email',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'phone' => 'required|string',
            'address' => 'required|string',
            'city' => 'required|string',
            'region' => 'required|string',
            'postal_code' => 'required|string',
        ]);

        $buyer = Auth::user();
        $cartItems = Cart::with('product')->where('buyer_id', $buyer->id)->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('buyer.cart.index')->with('error', 'Cart is empty.');
        }

        $totalAmount = 0;

        foreach ($cartItems as $item) {
            $product = $item->product;
            $totalAmount += $product->price * $item->quantity;

    $order = Order::create([
        'order_code' => Order::generateStructuredOrderCode($product->user),
        'buyer_id' => $buyer->id,
        'product_id' => $product->id,
        'farmer_id' => $product->farmer_id ?? $product->user_id,
        'quantity' => $item->quantity,
        'price' => $product->price,
        'total_price' => $product->price * $item->quantity,
        'status' => 'Pending',
        'payment_status' => 'paid',
        
        // ✅ Use request data directly (not session)
        'buyer_phone' => $request->phone,
        'buyer_address' => $request->address,
        'buyer_city' => $request->city,
        'buyer_region' => $request->region,
        'buyer_postal_code' => $request->postal_code,
    ]);


            // Notifications
            $this->notifyUsers($order, $buyer);
        }

        Cart::where('buyer_id', $buyer->id)->delete();

        Payment::create([
            'intent_id' => 'mock_' . Str::random(10),
            'method' => 'mock',
            'amount' => $totalAmount * 100,
            'status' => 'paid',
            'buyer_id' => $buyer->id,
        ]);

       $mockRef = 'MOCK_REF_' . Str::random(6);
        session(['reference' => $mockRef]);

        return redirect()->route('buyer.checkout.thankYou');

    }
}
