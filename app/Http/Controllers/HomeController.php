<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Review;

class HomeController extends Controller
{

    public function index()
    {
        $bestSelling = Product::withCount('orders')
            ->orderByDesc('orders_count')
            ->take(3)
            ->get();

        $feedbacks = Review::with('buyer')
            ->whereNotNull('comment')
            ->latest()
            ->take(3)
            ->get();

        return view('home', compact('bestSelling', 'feedbacks'));
    }
}
