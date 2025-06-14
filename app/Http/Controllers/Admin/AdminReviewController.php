<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Rating;
use Illuminate\Http\Request;

class AdminReviewController extends Controller
{
    public function index()
    {
        $ratings = Rating::with(['product', 'buyer', 'order'])
            ->latest()
            ->paginate(10);

        return view('admin.reviews.index', compact('ratings'));
    }
}
