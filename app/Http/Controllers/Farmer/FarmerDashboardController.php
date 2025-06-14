<?php

namespace App\Http\Controllers\Farmer;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\Product;
use App\Models\Follow;
use Illuminate\Http\Request;


class FarmerDashboardController extends Controller
{
public function index()
{
    $farmerId = Auth::id();

    // Existing metrics
    $ordersCount = Order::where('farmer_id', $farmerId)->count();
    $messagesCount = 12;
    $refundCount = 0;
    $soldOutCount = Product::where('farmer_id', $farmerId)->where('stock', 0)->count();
    $followerCount = 0;

    $totalSales = Order::where('farmer_id', $farmerId)
        ->where('status', 'Completed')
        ->sum('total_price');

    $topProducts = Product::where('farmer_id', $farmerId)
        ->withCount([
            'sales as total_sales' => function ($query) {
                $query->where('status', 'Completed')
                      ->select(\DB::raw('COALESCE(SUM(total_price), 0)'));
            },
            'reviews'
        ])
        ->withAvg('reviews', 'rating')
        ->orderByDesc('total_sales')
        ->take(3)
        ->get();

    // 📊 Chart Data: Sales per Product
    $productSales = Product::where('farmer_id', $farmerId)
        ->withCount([
            'sales as total_sales' => function ($query) {
                $query->where('status', 'Completed')
                      ->select(\DB::raw('COALESCE(SUM(total_price), 0)'));
            }
        ])
        ->orderByDesc('total_sales')
        ->take(5)
        ->get();

    $productNames = $productSales->pluck('name');
    $productSalesTotals = $productSales->pluck('total_sales');

    $monthlySales = Order::selectRaw('MONTH(created_at) as month, SUM(total_price) as total')
    ->where('farmer_id', $farmerId)
    ->where('status', 'Completed')
    ->whereYear('created_at', now()->year)
    ->groupBy('month')
    ->orderBy('month')
    ->pluck('total', 'month');

// Fill missing months with 0
$months = collect(range(1, 12))->map(function ($m) {
    return now()->startOfYear()->addMonths($m - 1)->format('F');
});

$salesData = $months->map(function ($label, $index) use ($monthlySales) {
    return [
        'month' => $label,
        'total' => $monthlySales[$index + 1] ?? 0
    ];
});

$monthLabels = $salesData->pluck('month');
$monthTotals = $salesData->pluck('total');

return view('farmer.dashboard', compact(
    'ordersCount', 'messagesCount', 'refundCount', 'soldOutCount',
    'followerCount', 'totalSales', 'topProducts',
    'productNames', 'productSalesTotals',
    'monthLabels', 'monthTotals'
));
}



}