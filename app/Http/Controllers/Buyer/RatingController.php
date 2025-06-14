<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Rating;
use App\Models\User;
use App\Notifications\NewRatingNotification;
use Illuminate\Support\Facades\Notification;
use App\Notifications\ProductRatedNotification;


class RatingController extends Controller
{
    public function create($orderId)
    {
        $order = Order::with('product')->where('buyer_id', auth()->id())->findOrFail($orderId);

        // Prevent rating if not yet Delivered or Completed
        if (!in_array($order->status, ['Delivered', 'Completed'])) {
            return redirect()->route('buyer.orders.history')
                ->with('error', 'You can only rate orders that are delivered or completed.');
        }

        // Prevent duplicate rating
        $existingRating = Rating::where('order_id', $order->id)->first();
        if ($existingRating) {
            return redirect()->route('buyer.orders.history')
                ->with('info', 'You have already rated this order.');
        }

        return view('buyer.orders.rate', compact('order'));
    }

public function store(Request $request, $orderId)
{
    $order = Order::where('buyer_id', auth()->id())->findOrFail($orderId);

    // ✅ Ensure the order is eligible for rating
    if (!in_array($order->status, ['Delivered', 'Completed'])) {
        return redirect()->route('buyer.orders.history')
            ->with('error', 'You can only rate orders that are delivered or completed.');
    }

    // ✅ Prevent duplicate ratings
    if (Rating::where('order_id', $order->id)->exists()) {
        return redirect()->route('buyer.orders.history')
            ->with('info', 'You already submitted a rating for this order.');
    }

    $validated = $request->validate([
        'rating' => 'required|integer|min:1|max:5',
        'comment' => 'nullable|string|max:1000',
    ]);

    // ✅ Save the rating
    $rating = Rating::create([
        'order_id'   => $order->id,
        'product_id' => $order->product_id,
        'buyer_id'   => auth()->id(),
        'rating'     => $validated['rating'],
        'comment'    => $validated['comment'],
    ]);

    // 🔔 Notify the farmer
    $farmer = $order->product->user;
    Notification::send($farmer, new ProductRatedNotification($rating));

    // 🔔 Notify all admins
    $admins = User::where('role', 'admin')->get();
    Notification::send($admins, new NewRatingNotification($order->product, auth()->user(), $validated['rating']));

    return redirect()->route('buyer.orders.history', ['status' => 'Completed'])
        ->with('success', 'Thanks for your review!');
}



}
