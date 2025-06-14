<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Product;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
use App\Notifications\AdminAlertNotification;

class CartController extends Controller
{
    public function index()
    {
        $buyerId = Auth::id();

        $cartItems = Cart::with('product')
            ->where('buyer_id', $buyerId)
            ->get();

        return view('buyer.cart.index', compact('cartItems'));
    }

    public function addToCart($productId)
    {
        $buyerId = Auth::id();

        if (!$buyerId) {
            return redirect()->route('login')->with('error', 'Please log in as buyer.');
        }

        $cartItem = Cart::where('buyer_id', $buyerId)
                        ->where('product_id', $productId)
                        ->first();

        if ($cartItem) {
            $cartItem->increment('quantity');
        } else {
            Cart::create([
                'buyer_id' => $buyerId,
                'product_id' => $productId,
                'quantity' => 1,
            ]);
        }

        return redirect()->back()->with('success', 'Product added to cart!');
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'nullable|integer|min:1',
        ]);

        $buyerId = Auth::id();
        $product = Product::findOrFail($request->product_id);
        $quantity = $request->input('quantity', 1);

        if ($quantity > $product->stock) {
            return back()->with('error', 'Quantity exceeds available stock.');
        }

        $existing = Cart::where('buyer_id', $buyerId)
                        ->where('product_id', $product->id)
                        ->first();

        if ($existing) {
            $existing->quantity += $quantity;
            $existing->save();
        } else {
            Cart::create([
                'buyer_id' => $buyerId,
                'product_id' => $product->id,
                'quantity' => $quantity,
            ]);
        }

        return redirect()->route('buyer.cart.index')->with('success', 'Product added to cart!');
    }


    public function removeFromCart($id)
    {
        $cartItem = Cart::find($id);

        if (!$cartItem) {
            return response()->json(['error' => 'Item not found.'], 404);
        }

        if ($cartItem->buyer_id != Auth::id()) {
            return response()->json(['error' => 'Unauthorized access.'], 403);
        }

        $cartItem->delete();

        return response()->json(['success' => true]);
    }

public function checkoutSelected(Request $request)
{
    $selectedItems = $request->input('cart_ids', []);
    $quantities = $request->input('quantities', []);

    if (empty($selectedItems)) {
        return redirect()->route('buyer.cart.index')->with('error', 'No items selected for checkout.');
    }

    foreach ($selectedItems as $cartId) {
        $cartItem = Cart::with('product')->where('id', $cartId)
            ->where('buyer_id', Auth::id())
            ->first();

        if (!$cartItem) continue;

        // 🛡️ Only validate and save if ID is in both cart_ids and quantities
        $requestedQty = isset($quantities[$cartId]) ? (int) $quantities[$cartId] : $cartItem->quantity;

        if ($requestedQty < 1 || $requestedQty > $cartItem->product->stock) {
            return redirect()->route('buyer.cart.index')
                ->with('error', 'Invalid quantity for "' . $cartItem->product->name . '".');
        }

        // ✅ Save quantity only for selected item
        $cartItem->quantity = $requestedQty;
        $cartItem->save();
    }

    session(['checkout_items' => $selectedItems]);

    return redirect()->route('buyer.payment.form');
}



    public function showPaymentForm()
    {
        $cartIds = session('checkout_items', []);
        $buyerId = Auth::id();

        $cartItems = Cart::with('product')
            ->whereIn('id', $cartIds)
            ->where('buyer_id', $buyerId)
            ->get();

        $totalAmount = $cartItems->sum(fn ($item) => $item->product->price * $item->quantity);

        return view('buyer.payment.form', compact('cartItems', 'totalAmount'));
    }

    public function completeCheckout()
    {
        $buyerId = Auth::id();
        $cartIds = session('checkout_items', []);

        $cartItems = Cart::with('product')
            ->whereIn('id', $cartIds)
            ->where('buyer_id', $buyerId)
            ->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('buyer.cart.index')->with('error', 'Selected items not found.');
        }

        foreach ($cartItems as $item) {
            if ($item->quantity > $item->product->stock) {
                return redirect()->route('buyer.cart.index')->with('error', 'Quantity for "' . $item->product->name . '" exceeds available stock.');
            }
        }

        $orders = [];

        foreach ($cartItems as $item) {
            $orders[] = Order::create([
                'order_code' => 'ORD-' . strtoupper(uniqid()),
                'buyer_id' => $buyerId,
                'product_id' => $item->product_id,
                'farmer_id' => $item->product->user_id,
                'quantity' => $item->quantity,
                'price' => $item->product->price,
                'total_price' => $item->quantity * $item->product->price,
                'status' => 'Pending',
            ]);
            $item->delete();
        }

        if (!empty($orders)) {
            $total = collect($orders)->sum('total_price');
            $firstOrder = $orders[0];

            $admins = User::where('role', 'admin')->get();
            Notification::send($admins, new AdminAlertNotification([
                'message' => 'New order placed by ' . Auth::user()->name . ' for ₱' . number_format($total, 2),
                'icon' => 'bi-cart-check',
                'link' => route('admin.orders.show', $firstOrder->id),
                'type' => 'order',
            ]));

            \Log::info('✅ Admin notified of new order', [
                'admin_count' => $admins->count(),
                'buyer_id' => $buyerId,
                'total' => $total
            ]);
        }

        session()->forget('checkout_items');

        return view('buyer.payment.success');
    }

    public function updateQuantity(Request $request)
    {
        $request->validate([
            'cart_id' => 'required|integer|exists:carts,id',
            'quantity' => 'required|integer|min:1'
        ]);

        $cart = Cart::with('product')->where('id', $request->cart_id)->where('buyer_id', Auth::id())->first();

        if (!$cart) {
            return response()->json(['error' => 'Cart item not found.'], 404);
        }

        if ($request->quantity > $cart->product->stock) {
            return response()->json(['error' => 'Quantity exceeds stock.'], 422);
        }

        $cart->quantity = $request->quantity;
        $cart->save();

        $total = $cart->product->price * $cart->quantity;

        return response()->json([
            'success' => true,
            'new_total' => number_format($total, 2)
        ]);
    }


}