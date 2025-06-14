<?php

namespace App\Http\Controllers;
use App\Models\Product;
use App\Models\Rating;
use Illuminate\Http\Request;

class ProductDisplayController extends Controller
{
public function show($id)
{
    $product = Product::with(['farmer.products', 'reviews.user'])->findOrFail($id);

    $reviews = $product->reviews;
    $averageRating = $product->reviews()->avg('rating');

    $sellerRating = $product->farmer
        ? Rating::whereIn('product_id', $product->farmer->products->pluck('id'))->avg('rating')
        : null;

    return view('buyer.products.show', compact('product', 'reviews', 'averageRating', 'sellerRating'));
}

}