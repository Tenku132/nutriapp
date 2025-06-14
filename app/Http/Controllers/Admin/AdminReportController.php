<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use Carbon\Carbon;
use App\Exports\SalesReportExport;
use Maatwebsite\Excel\Facades\Excel;

class AdminReportController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->input('start_date') ?? Carbon::now()->subMonth()->toDateString();
        $endDate = $request->input('end_date') ?? Carbon::now()->toDateString();

        // ✅ Make sure it matches 'completed' for consistency
        $orders = Order::with(['product', 'buyer'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->paginate(10);

        return view('admin.reports.index', compact('orders', 'startDate', 'endDate'));
    }

    public function export(Request $request)
    {
        $startDate = $request->input('start_date') ?? Carbon::now()->subMonth()->toDateString();
        $endDate = $request->input('end_date') ?? Carbon::now()->toDateString();

        // ✅ Match the same filtering logic as index()
        $orders = Order::with('product', 'buyer')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->get();

        return Excel::download(new SalesReportExport($orders), 'sales_report.xlsx');
    }
}
