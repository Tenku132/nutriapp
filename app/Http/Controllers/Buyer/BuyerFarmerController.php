<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class BuyerFarmerController extends Controller
{
    public function show($id, Request $request)
    {
        $farmer = User::with(['products.reviews', 'followers'])->findOrFail($id);

        // Start query
        $query = $farmer->products();

        // Apply search filter
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Apply category filter
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Apply sorting
        switch ($request->sort) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'rating_desc':
                $query->withAvg('reviews', 'rating')->orderByDesc('reviews_avg_rating');
                break;
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        // Paginate and preserve query parameters
        $products = $query->paginate(12)->withQueryString();

        // Check if the current buyer is following the farmer
        $isFollowing = Auth::check() && $farmer->followers->contains(Auth::id());

        // Load all categories
        $categories = \App\Models\Category::all();

        return view('buyer.farmer-profile', compact('farmer', 'products', 'isFollowing', 'categories'));
    }
}
