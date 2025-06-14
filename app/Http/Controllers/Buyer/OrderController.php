<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Cart;
use App\Models\Rating;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use App\Notifications\AdminAlertNotification;
use App\Notifications\OrderStatusUpdateNotification;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status');
        $buyerId = Auth::id();

        $orders = Order::with(['product', 'product.farmer'])
            ->where('buyer_id', $buyerId)
            ->when($status, fn($query) => $query->where('status', $status))
            ->latest()
            ->get();

        return view('buyer.orders.index', compact('orders', 'status'));
    }

    public function history(Request $request)
    {
        $status = $request->query('status');

        $orders = Order::with([
            'product.reviews',
            'product.farmer'
        ])
        ->where('buyer_id', Auth::id())
        ->when($status, fn($query) => $query->where('status', $status))
        ->latest()
        ->get();

        return view('buyer.orders.history', compact('orders', 'status'));
    }

    public function buyAgain($orderId)
    {
        $order = Order::with('product')->findOrFail($orderId);

        Cart::create([
            'buyer_id'   => Auth::id(),
            'product_id' => $order->product_id,
            'quantity'   => 1
        ]);

        return redirect()->route('buyer.cart.index')->with('success', 'Item added to cart again!');
    }

    public function rate($orderId)
    {
        $order = Order::with('product')->findOrFail($orderId);
        return view('buyer.orders.rate', compact('order'));
    }

    public function submitRating(Request $request, $orderId)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string',
        ]);

        $order = Order::findOrFail($orderId);
        $buyerId = auth()->id();

        if (!$buyerId) {
            return redirect()->route('login')->with('error', 'You must be logged in to rate a product.');
        }

        Rating::create([
            'order_id'   => $order->id,
            'product_id' => $order->product_id,
            'buyer_id'   => $buyerId,
            'rating'     => $request->rating,
            'comment'    => $request->comment,
        ]);

        return redirect()->route('buyer.orders.history')->with('success', 'Thank you for your rating!');
    }

    public function transactions()
    {
        $orders = Order::with('product')
            ->where('buyer_id', Auth::id())
            ->orderByDesc('created_at')
            ->get();

        return view('buyer.orders.transactions', compact('orders'));
    }

    public function show($id)
    {
        $order = Order::with('product')->findOrFail($id);
        return view('farmer.orders.show', compact('order'));
    }

    public function checkout(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $order = new Order();
        $order->order_code = 'ORD-' . strtoupper(uniqid());
        $order->buyer_id = Auth::id();
        $order->product_id = $request->product_id;
        $order->farmer_id = $request->farmer_id;
        $order->quantity = $request->quantity;
        $order->price = $request->price;
        $order->total_price = $request->price * $request->quantity;
        $order->status = 'Pending';
        $order->save();

        $admins = User::where('role', 'admin')->get();
        Notification::send($admins, new AdminAlertNotification([
            'message' => 'Order ' . $order->order_code . ' has been placed by ' . ($order->buyer->name ?? 'Unknown'),
            'icon'    => 'bi-cart-check',
            'link' => route('admin.orders.index', ['search' => $order->order_code]),
            'type'    => 'order',
            'extra'   => [
                'order_id' => $order->id,
                'buyer_id' => $order->buyer_id,
            ]
        ]));

        return redirect()->route('buyer.orders.history')
            ->with('success', 'Order processed with simulated payment.');
    }

    public function cancel(Order $order)
    {
        if ($order->buyer_id !== auth()->id()) {
            abort(403);
        }

        if (!in_array(strtolower($order->status), ['pending', 'to ship'])) {
            return back()->with('error', 'Only orders that are still pending or not yet shipped can be canceled.');
        }

        $product = $order->product;
        if ($product && $order->stock_deducted) {
            $product->stock += $order->quantity;
            $product->save();
            $order->stock_deducted = false;
            $order->save();
        }

        $order->status = 'Cancelled';
        $order->save();

        return back()->with('success', 'Order has been canceled and stock restored.');
    }

    public function confirm(Request $request, $orderId)
    {
        $order = Order::where('id', $orderId)->where('buyer_id', auth()->id())->firstOrFail();

        if ($order->status !== 'shipped') {
            return back()->with('error', 'Only shipped orders can be confirmed.');
        }

        $order->status = 'completed';
        $order->save();

        return back()->with('success', 'Order confirmed as completed.');
    }

    public function requestReturn(Request $request, $orderId)
    {
        $order = Order::where('id', $orderId)->where('buyer_id', auth()->id())->firstOrFail();

        if (!in_array($order->status, ['shipped', 'completed'])) {
            return back()->with('error', 'Return/Refund only available for shipped or completed orders.');
        }

        $product = $order->product;
        if ($product) {
            $product->stock += $order->quantity;
            $product->save();
        }

        $order->status = 'Return/Refund';
        $order->stock_deducted = false;
        $order->save();

        return back()->with('success', 'Return/Refund requested and stock rolled back.');
    }
}
