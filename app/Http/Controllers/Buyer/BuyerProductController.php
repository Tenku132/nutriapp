<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Cart;
use Illuminate\Support\Facades\Auth;
use App\Models\Category;
use App\Models\Rating;

class BuyerProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::where('status', 'approved')
            ->with(['farmer'])
            ->withAvg('reviews', 'rating')
            ->withCount('reviews');

        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $products = $query->latest()->get();

        $topProducts = Product::where('status', 'approved')
            ->with(['farmer'])
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->orderByDesc('reviews_avg_rating')
            ->take(4)
            ->get();

        $discoverProducts = Product::where('status', 'approved')
            ->with(['farmer'])
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->latest()
            ->take(12)
            ->get();

        $categories = ['Fruits', 'Vegetable', 'Grains', 'Spices'];

        return view('buyer.products.index', compact('products', 'topProducts', 'discoverProducts', 'categories'));
    }

    public function addToCart(Request $request, $productId)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $buyerId = Auth::id();
        $product = Product::where('status', 'approved')->findOrFail($productId);

        if ($request->quantity > $product->stock) {
            return back()->with('error', 'Quantity exceeds available stock.');
        }

        $cartItem = Cart::where('buyer_id', $buyerId)
                        ->where('product_id', $productId)
                        ->first();

        if ($cartItem) {
            $cartItem->increment('quantity', $request->quantity);
        } else {
            Cart::create([
                'buyer_id' => $buyerId,
                'product_id' => $productId,
                'quantity' => $request->quantity,
            ]);
        }

        return redirect()->route('buyer.cart.index')->with('success', 'Product added to cart!');
    }

    public function filterByCategory($category)
    {
        $products = Product::where('status', 'approved')
            ->where('category', $category)
            ->latest()
            ->get();

        return view('buyer.products.shop', compact('products', 'category'));
    }

    public function categoryProducts($slug)
    {
        $category = ucfirst(strtolower($slug));

        $products = Product::where('status', 'approved')
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->where('category', $category)
            ->latest()
            ->get();

        $discoverProducts = Product::where('status', 'approved')
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->where('category', $category)
            ->latest()
            ->take(8)
            ->get();

        return view('buyer.products.category', compact('category', 'products', 'discoverProducts'));
    }

    public function categoryPage(Request $request, $category)
    {
        $normalizedCategory = ucfirst(strtolower($category));

        $query = Product::where('status', 'approved')
            ->with(['farmer'])
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->where('category', $normalizedCategory);

        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        if ($request->filled('min_rating')) {
            $query->having('reviews_avg_rating', '>=', $request->min_rating);
        }

        if ($request->has('in_stock')) {
            $query->where('stock', '>', 0);
        }

        switch ($request->sort) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'rating':
                $query->orderByDesc('reviews_avg_rating');
                break;
            default:
                $query->latest();
        }

        $products = $query->get();

        $discoverProducts = Product::where('status', 'approved')
            ->with(['farmer'])
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->where('category', $normalizedCategory)
            ->latest()
            ->take(12)
            ->get();

        $categories = ['fruits', 'grains', 'vegetable', 'spices'];

        return view('buyer.products.category', compact(
            'categories',
            'products',
            'discoverProducts',
            'category'
        ));
    }

    public function search(Request $request)
    {
        $search = $request->input('search');

        $query = Product::where('status', 'approved')
            ->with(['farmer'])
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', '%' . $search . '%')
                  ->orWhere('description', 'LIKE', '%' . $search . '%');
            });

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        if ($request->filled('min_rating')) {
            $query->having('reviews_avg_rating', '>=', $request->min_rating);
        }

        if ($request->has('in_stock')) {
            $query->where('stock', '>', 0);
        }

        switch ($request->sort) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'rating':
                $query->orderByDesc('reviews_avg_rating');
                break;
            default:
                $query->latest();
        }

        $products = $query->paginate(12);
        $categories = ['fruits', 'vegetable', 'grains', 'spices'];

        return view('buyer.products.search-results', compact('products', 'search', 'categories'));
    }

    public function show($id)
    {
        $product = Product::with(['farmer.products', 'reviews.user'])
            ->where('status', 'approved')
            ->findOrFail($id);

        $reviews = $product->reviews;

        $averageRating = $reviews->count() > 0
            ? $reviews->avg('rating')
            : null;

        $sellerRating = Rating::whereIn('product_id', $product->farmer->products->pluck('id') ?? [])
            ->avg('rating');

        $relatedProducts = Product::where('status', 'approved')
            ->where('farmer_id', $product->farmer_id)
            ->where('id', '!=', $product->id)
            ->latest()
            ->take(4)
            ->get();

        return view('buyer.products.show', compact(
            'product',
            'reviews',
            'averageRating',
            'sellerRating',
            'relatedProducts'
        ));
    }

    public function buyNow(Request $request, $productId)
    {
        $product = Product::where('status', 'approved')->findOrFail($productId);

        if ($product->stock <= 0) {
            return redirect()->back()->with('error', 'This product is out of stock.');
        }

        $quantity = max((int) $request->input('quantity', 1), 1);

        session([
            'buy_now' => [
                'product_id' => $product->id,
                'quantity'   => $quantity,
                'buyer_id'   => auth()->id(),
            ]
        ]);

        return redirect()->route('buyer.payment.form')->with('success', 'Proceed to checkout');
    }
}
