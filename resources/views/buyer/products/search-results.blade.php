@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h4 class="mb-4">Search Results for "<span class="text-success">{{ $search }}</span>"</h4>

    <!-- Filter Panel -->
<form method="GET" action="{{ route('buyer.products.search') }}" class="row g-3 mb-4 align-items-end">
    <div class="col-md-2">
        <label for="min_price" class="form-label fw-bold">Min Price</label>
        <input type="number" id="min_price" name="min_price" class="form-control" value="{{ request('min_price') }}">
    </div>
    <div class="col-md-2">
        <label for="max_price" class="form-label fw-bold">Max Price</label>
        <input type="number" id="max_price" name="max_price" class="form-control" value="{{ request('max_price') }}">
    </div>
    <div class="col-md-2">
        <label for="min_rating" class="form-label fw-bold">Min Rating</label>
        <select id="min_rating" name="min_rating" class="form-select">
            <option value="">Any</option>
            @for ($i = 5; $i >= 1; $i--)
                <option value="{{ $i }}" {{ request('min_rating') == $i ? 'selected' : '' }}>{{ $i }} ⭐</option>
            @endfor
        </select>
    </div>
    <div class="col-md-2">
        <label class="form-label fw-bold">Availability</label>
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="in_stock" id="in_stock" value="1" {{ request('in_stock') ? 'checked' : '' }}>
            <label class="form-check-label" for="in_stock">In stock only</label>
        </div>
    </div>
    <div class="col-md-2">
        <label for="sort" class="form-label fw-bold">Sort By</label>
        <select id="sort" name="sort" class="form-select">
            <option value="">Default</option>
            <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Price Low to High</option>
            <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Price High to Low</option>
            <option value="rating" {{ request('sort') == 'rating' ? 'selected' : '' }}>Top Rated</option>
        </select>
    </div>
    <div class="col-md-1 d-grid">
        <button type="submit" class="btn btn-success">Apply</button>
    </div>
    <div class="col-md-1 d-grid">
        <a href="{{ route('buyer.products.index') }}" class="btn btn-outline-secondary">Clear</a>
    </div>
</form>

    @if ($products->isEmpty())
        <div class="alert alert-warning text-center">No products matched your search.</div>
    @else
        <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 g-4">
            @foreach ($products as $product)
                <div class="col">
                    <div class="card h-100">
                        <a href="{{ route('product.show', $product->id) }}">
                            <img src="{{ asset('storage/' . $product->image) }}" class="card-img-top" alt="{{ $product->name }}">
                        </a>
                        <div class="card-body text-center">
                            <h6 class="card-title">{{ ucfirst($product->name) }}</h6>
                            <p class="product-price text-success fw-bold">₱{{ number_format($product->price, 2) }}</p>
                            @if($product->reviews_count > 0)
                                <div class="text-warning">⭐ {{ number_format($product->reviews_avg_rating, 1) }}</div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $products->withQueryString()->links() }}
        </div>
    @endif
</div>
@endsection
