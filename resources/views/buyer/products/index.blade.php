@extends('layouts.app')

@section('content')
<style>
    .product-img {
        width: 100%;
        height: 200px;
        object-fit: cover;
        border-radius: 12px;
    }

    .category-img {
        width: 100%;
        height: 180px;
        object-fit: cover;
        transition: transform 0.2s ease;
        border-radius: 12px;
    }

    .category-img:hover {
        transform: scale(1.05);
        cursor: pointer;
    }

    .product-card {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 10px rgba(0,0,0,0.08);
        transition: transform 0.2s ease;
        text-align: center;
        padding: 15px 10px;
        display: flex;
        flex-direction: column;
        height: 100%;
    }

    .product-card:hover {
        transform: scale(1.02);
    }

    .product-name {
        font-weight: 700;
        text-transform: uppercase;
        font-size: 15px;
        margin-top: 8px;
        min-height: 40px;
        color: black
    }

    .product-location {
        font-size: 13px;
        color: #666;
        min-height: 35px;
    }

    .product-rating {
        color: #f4b400;
        font-size: 14px;
        min-height: 24px;
    }

    .product-price {
        color: #2e7d32;
        font-weight: bold;
        margin-top: auto;
        font-size: 16px;
    }
</style>

<div class="container my-4">

<!-- Search Bar -->
<form action="{{ route('buyer.products.search') }}" method="GET" class="mb-4">
    <label for="search" class="form-label fw-bold">Search</label>
    <input type="text" id="search" name="search" class="form-control" placeholder="Search product name..." value="{{ request('search') }}">
</form>

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


<!-- Categories Section -->
<div class="mb-5">
    <h5 class="mb-3 fw-bold">CATEGORIES</h5>
    <div class="row row-cols-2 row-cols-md-3 row-cols-lg-5 g-3">

        @php
            $staticCategories = ['Grains', 'Fruits', 'Vegetable', 'Spices', 'Beverages'];
        @endphp

        @foreach ($staticCategories as $cat)
            <div class="col text-center">
                <a href="{{ route('buyer.products.byCategory', ['category' => strtolower($cat)]) }}" class="text-decoration-none text-dark">
                    <img src="{{ asset('img/categories/' . strtolower($cat) . '.jpg') }}"
                         onerror="this.src='{{ asset('img/categories/default.jpg') }}';"
                         class="img-fluid rounded category-img"
                         alt="{{ $cat }}">

                    <p class="fw-semibold mt-2 {{ isset($categoryName) && strtolower($categoryName) == strtolower($cat) ? 'text-success' : '' }}">
                        {{ strtoupper($cat) }}
                    </p>
                </a>
            </div>
        @endforeach
    </div>
</div>


<!-- Products Title -->
<h5 class="fw-bold mb-4 text-center text-success">
    {{ isset($categoryName) ? strtoupper($categoryName) . ' PRODUCTS' : 'TOP PRODUCTS' }}
</h5>

<!-- Top Products -->
<div class="top-products p-4 rounded mb-5 text-white" style="background-color: #1b4d3e;">
    <div class="row g-4">
        @forelse ($topProducts as $product)
            <div class="col-md-3">
                <div class="product-card">
                    <a href="{{ route('product.show', $product->id) }}">
                        <img src="{{ asset('storage/' . $product->image) }}" class="product-img" alt="{{ strtoupper($product->name) }}">
                    </a>
                    <div class="product-name">{{ strtoupper($product->name) }}</div>
                    <div class="product-location">
                        Seller: {{ strtoupper($product->farmer->business_name ?? $product->farmer->name ?? 'Unknown') }}
                        <br>
                        {{ ucfirst($product->city ?? 'Unknown') }}, {{ ucfirst($product->province ?? 'Unknown') }}
                    </div>
                        <div class="product-rating text-warning">
                            @for ($i = 1; $i <= 5; $i++)
                                <i class="{{ $i <= round($product->reviews_avg_rating) ? 'fas' : 'far' }} fa-star"></i>
                            @endfor
                            <span class="text-muted ms-1">
                                ({{ $product->reviews_count ?? 0 }})
                            </span>
                        </div>
                    <div class="product-price">₱{{ number_format($product->price, 2) }}</div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center text-white">No products found in this category.</div>
        @endforelse
    </div>
</div>

<!-- Daily Discover -->
<div class="bg-lighter p-4 rounded">
    <h4 class="text-success text-center fw-bold">DAILY DISCOVER</h4>
    <p class="text-muted text-center mb-4">Discover some of our new and fresh crops today!</p>
    <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-lg-6 g-4">
        @foreach ($discoverProducts as $product)
            <div class="col">
                <div class="product-card">
                    <a href="{{ route('product.show', $product->id) }}">
                        <img src="{{ asset('storage/' . $product->image) }}" class="product-img" alt="{{ strtoupper($product->name) }}">
                    </a>
                    <div class="product-name">{{ strtoupper($product->name) }}</div>
                    <div class="product-location">
                        Seller: {{ strtoupper($product->farmer->business_name ?? $product->farmer->name ?? 'Unknown') }}
                        <br>
                        {{ ucfirst($product->city ?? 'Unknown') }}, {{ ucfirst($product->province ?? 'Unknown') }}
                    </div>
                        <div class="product-rating text-warning">
                            {{-- Display stars based on average rating --}}
                            @for ($i = 1; $i <= 5; $i++)
                                <i class="{{ $i <= round($product->reviews_avg_rating) ? 'fas' : 'far' }} fa-star"></i>
                            @endfor
                            <span class="text-muted ms-1">
                                ({{ $product->reviews_count ?? 0 }})
                            </span>
                        </div>
                    <div class="product-price">₱{{ number_format($product->price, 2) }}</div>
                </div>
            </div>
        @endforeach
    </div>
</div>
</div>
@endsection
