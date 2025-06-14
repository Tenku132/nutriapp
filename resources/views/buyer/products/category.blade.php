@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h4 class="mb-4 text-success text-uppercase">{{ ucfirst($category) }} Products</h4>
    <div class="mb-3">
        <a href="{{ route('buyer.products.index') }}" class="btn btn-outline-success">
            ← Back to Shop
        </a>
    </div>

    <!-- Search & Filter Panel -->
    <form method="GET" action="{{ route('buyer.products.search') }}" class="row row-cols-lg-auto g-3 align-items-end mb-4">
        <div class="col-12">
            <input type="text" name="search" placeholder="Search product name..." class="form-control" value="{{ request('search') }}">
        </div>
        <div class="col">
            <input type="number" name="min_price" class="form-control" placeholder="Min Price" value="{{ request('min_price') }}">
        </div>
        <div class="col">
            <input type="number" name="max_price" class="form-control" placeholder="Max Price" value="{{ request('max_price') }}">
        </div>
        <div class="col">
            <select name="min_rating" class="form-select">
                <option value="">Min Rating</option>
                @for ($i = 5; $i >= 1; $i--)
                    <option value="{{ $i }}" {{ request('min_rating') == $i ? 'selected' : '' }}>{{ $i }} ⭐</option>
                @endfor
            </select>
        </div>
        <div class="col">
            <select name="sort" class="form-select">
                <option value="">Sort By</option>
                <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Price Low to High</option>
                <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Price High to Low</option>
                <option value="rating" {{ request('sort') == 'rating' ? 'selected' : '' }}>Top Rated</option>
            </select>
        </div>
        <div class="col">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="in_stock" value="1" {{ request('in_stock') ? 'checked' : '' }}>
                <label class="form-check-label">In stock only</label>
            </div>
        </div>
        <div class="col">
            <button class="btn btn-success">Apply</button>
            <a href="{{ route('buyer.products.index') }}" class="btn btn-outline-secondary">Clear</a>
        </div>
    </form>

    {{-- Main Category Products --}}
    @if ($products->isEmpty())
        <div class="alert alert-dark text-center">No products found in this category.</div>
    @else
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
            @foreach ($products as $product)
                <div class="col">
                    <div class="card h-100 shadow-sm hover-lift">
                        <a href="{{ route('product.show', $product->id) }}">
                            <img src="{{ asset('storage/' . $product->image) }}" class="card-img-top product-card-img" alt="{{ $product->name }}">
                        </a>
                        <div class="card-body text-center">
                            <h6 class="card-title mb-1 text-uppercase">{{ $product->name }}</h6>

                            @if ($product->farmer)
                                <div class="small text-muted">Seller: <span class="fw-semibold">{{ $product->farmer->business_name ?? 'Organic Farmer' }}</span></div>
                            @endif

                            @if ($product->reviews_count > 0)
                                <div class="text-warning small">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <i class="{{ $i <= round($product->reviews_avg_rating) ? 'fas' : 'far' }} fa-star"></i>
                                    @endfor
                                    <span class="text-muted">({{ $product->reviews_count }})</span>
                                </div>
                            @else
                                <div class="text-muted small">Not yet rated</div>
                            @endif

                            <p class="card-text text-success fw-bold mt-1">₱{{ number_format($product->price, 2) }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Daily Discover Section --}}
    @if (!$discoverProducts->isEmpty())
        <div class="mt-5">
            <h5 class="text-center text-success fw-bold">DAILY DISCOVER</h5>
            <p class="text-muted text-center mb-4">Discover more from {{ ucfirst($category) }} category!</p>
            <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-lg-6 g-3">
                @foreach ($discoverProducts as $product)
                    <div class="col">
                        <div class="card h-100 shadow-sm hover-lift">
                            <a href="{{ route('product.show', $product->id) }}">
                               <img src="{{ asset('storage/' . $product->image) }}" class="card-img-top product-card-img" alt="{{ $product->name }}">
                            </a>
                            <div class="card-body text-center">
                                <h6 class="card-title mb-1 text-uppercase">{{ $product->name }}</h6>

                                @if ($product->farmer)
                                    <div class="small text-muted">Seller: <span class="fw-semibold">{{ $product->farmer->business_name ?? 'Organic Farmer' }}</span></div>
                                @endif

                                @if ($product->reviews_count > 0)
                                    <div class="text-warning small">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <i class="{{ $i <= round($product->reviews_avg_rating) ? 'fas' : 'far' }} fa-star"></i>
                                        @endfor
                                        <span class="text-muted">({{ $product->reviews_count }})</span>
                                    </div>
                                @else
                                    <div class="text-muted small">Not yet rated</div>
                                @endif

                                <p class="card-text text-success fw-bold mt-1">₱{{ number_format($product->price, 2) }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection
