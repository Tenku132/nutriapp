<?php

namespace App\Http\Controllers\Farmer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Rating;
use App\Notifications\RatingReminderNotification;
use App\Notifications\OrderStatusUpdateNotification;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function index(Request $request)
{
    $farmerId = Auth::id();

    $query = Order::with(['product', 'buyer'])
        ->where('farmer_id', $farmerId);

    if ($request->has('status') && $request->status !== null) {
        $normalizedStatus = str_replace('_', ' ', strtolower($request->status));
        $query->whereRaw('LOWER(status) = ?', [$normalizedStatus]);
    }

    if ($request->filled('search')) {
        $query->whereHas('product', function ($q) use ($request) {
            $q->where('name', 'like', '%' . $request->search . '%');
        });
    }

    $orders = $query->latest()->get();

    // 🧮 Count per status
    $statusCounts = [
        'all' => Order::where('farmer_id', $farmerId)->count(),
        'paid' => Order::where('farmer_id', $farmerId)->where('status', 'paid')->count(),
        'to ship' => Order::where('farmer_id', $farmerId)->where('status', 'to ship')->count(),
        'completed' => Order::where('farmer_id', $farmerId)->where('status', 'completed')->count(),
        'cancelled' => Order::where('farmer_id', $farmerId)->where('status', 'cancelled')->count(),
    ];

    return view('farmer.orders.index', compact('orders', 'statusCounts'));
}


public function updateStatus(Request $request, $orderId)
{
    $request->validate([
        'status' => 'required|in:paid,to ship,shipped,completed,cancelled',
    ]);

    $order = Order::where('id', $orderId)
        ->whereHas('product', fn ($q) => $q->where('farmer_id', auth()->id()))
        ->firstOrFail();

    $oldStatus = $order->status;

    $order->status = $request->status;
    $order->save();

    // ✅ Only continue if status is actually changing
    if ($oldStatus === $request->status) {
        return back()->with('info', 'Status is already set to ' . ucfirst($request->status));
    }

    $product = $order->product;

    // ✅ Deduct stock ONCE when set to 'to ship'
    if ($request->status === 'to ship' && !$order->stock_deducted) {
        $product->stock -= $order->quantity;
        $product->save();
        $order->stock_deducted = true;
        $order->save();
    }

    // 🔁 Rollback if cancelled AND stock was deducted
    if ($request->status === 'cancelled' && $order->stock_deducted) {
        $product->stock += $order->quantity;
        $product->save();
        $order->stock_deducted = false;
        $order->save();
    }

    // 🔔 Notify buyer
    $order->buyer->notify(new OrderStatusUpdateNotification([
        'status' => $order->status,
        'product' => $order->product->name ?? 'your product'
    ]));

    return redirect()->back()->with('success', 'Order status updated!');
}




}
