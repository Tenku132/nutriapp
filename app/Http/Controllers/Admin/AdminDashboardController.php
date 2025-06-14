<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use App\Exports\SalesExport;
use Maatwebsite\Excel\Facades\Excel;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // 📊 Core counts
        $totalFarmers = User::where('role', 'farmer')->count();
        $totalBuyers = User::where('role', 'buyer')->count();
        $totalProducts = Product::count();
        $totalOrders = Order::count();
        $totalRevenue = Order::where('status', 'completed')->sum('total_price');

        // 📌 STEP 1: Query and eager load products AFTER getting results
        $topProductData = Order::select(
                'product_id',
                DB::raw('SUM(quantity) as total_quantity'),
                DB::raw('SUM(total_price) as total_revenue')
            )
            ->where('status', 'completed')
            ->groupBy('product_id')
            ->orderByDesc('total_quantity')
            ->take(5)
            ->get();

        // 📌 STEP 2: Manually eager load products (avoid N+1 problem)
        $topProductData->load('product');

        // 📌 STEP 3: Map into desired structure
        $topProducts = $topProductData->map(function ($order) {
            return (object) [
                'product_id' => $order->product_id,
                'name' => $order->product->name ?? 'Unknown',
                'total_quantity_sold' => $order->total_quantity,
                'total_revenue' => $order->total_revenue,
                'image' => $order->product->image ?? 'img/default.png',
            ];
        });

        // 🕒 Recent Orders
        $recentOrders = Order::with('product', 'buyer')
            ->latest()
            ->take(5)
            ->get();

        // 📈 Sales Over Time
        $salesData = Order::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('SUM(total_price) as total')
        )
        ->where('status', 'completed')
        ->groupBy('date')
        ->orderBy('date', 'ASC')
        ->get()
        ->map(fn ($item) => [
            'date' => $item->date,
            'total' => (float) $item->total,
        ]);

        return view('admin.dashboard', compact(
            'totalFarmers',
            'totalBuyers',
            'totalProducts',
            'totalOrders',
            'totalRevenue',
            'topProducts',      // ✅ renamed to match Blade file
            'recentOrders',
            'salesData'
        ));
    }

    public function exportSales()
    {
        return Excel::download(new SalesExport, 'sales_data.xlsx');
    }
}
