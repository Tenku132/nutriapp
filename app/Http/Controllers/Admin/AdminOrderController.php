<?php

namespace App\Http\Controllers\Admin;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AdminOrderController extends Controller
{
  public function index(Request $request)
  {
      $orders = Order::with(['buyer', 'product.user']) // product.user = farmer
          ->when($request->search, function ($query, $search) {
              $query->where(function ($q) use ($search) {
                  // Search by product name
                  $q->whereHas('product', function ($q1) use ($search) {
                      $q1->where('name', 'like', "%$search%");
                  })
                  // Search by buyer name
                  ->orWhereHas('buyer', function ($q2) use ($search) {
                      $q2->where('name', 'like', "%$search%");
                  })
                  // Search by farmer name (product.user)
                  ->orWhereHas('product.user', function ($q3) use ($search) {
                      $q3->where('name', 'like', "%$search%");
                  })
                  // Search by formatted Order ID (MD5 substring)
                  ->orWhere('order_code', 'like', "%$search%");

              });
          })
          ->when($request->status, fn($q, $status) => $q->where('status', $status))
          ->orderBy('created_at', 'desc')
          ->paginate(10);
  
      return view('admin.orders.index', compact('orders'));
  }

  public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|string',
        ]);

        $order->status = $request->status;
        $order->save();

        return redirect()->back()->with('success', 'Order status updated.');
    }

    public function show(Order $order)
    {
        $order->load(['buyer', 'product.farmer']); // Ensure all relations are loaded
        return view('admin.orders.partials.details', compact('order'));
    }


}
