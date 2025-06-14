@extends('layouts.app')

@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@section('content')
<div class="container py-4">
    {{-- Product Section --}}
    <div class="row bg-white p-4 rounded shadow mb-4">
        {{-- Product Image --}}
        <div class="col-md-5">
            <img src="{{ asset('storage/' . $product->image) }}"
                class="img-fluid rounded mb-3 fixed-product-image"
                alt="{{ $product->name }}">
        </div>

    {{-- Product Info --}}
    <div class="col-md-7 d-flex flex-column justify-content-between">
        <div class="ps-4">

            <h3 class="fw-bold mb-3">{{ ucfirst($product->name) }}</h3>

            <p class="text-muted mb-3" style="font-size: 1rem;">
                {{ ucfirst($product->description) }}
            </p>

            <h4 class="text-success fw-bold mb-3">
                ₱{{ number_format($product->price, 2) }} <small class="text-muted fs-6">per kilo</small>
            </h4>

            @php
                $average = $averageRating ?? 0;
                $full = floor($average);
                $half = ($average - $full) >= 0.5;
                $empty = 5 - $full - ($half ? 1 : 0);
            @endphp

            <p class="mb-3">
                <strong>Rating:</strong>
                <span class="green-stars">
                    @for ($i = 0; $i < $full; $i++)
                        <i class="fas fa-star"></i>
                    @endfor
                    @if ($half)
                        <i class="fas fa-star-half-alt"></i>
                    @endif
                    @for ($i = 0; $i < $empty; $i++)
                        <i class="far fa-star"></i>
                    @endfor
                </span>

                @if ($reviews->count())
                    <span class="ms-1 text-dark">{{ number_format($average, 1) }} ({{ $reviews->count() }} {{ $reviews->count() === 1 ? 'review' : 'reviews' }})</span>
                @else
                    <span class="ms-1 text-muted">Not yet rated</span>
                @endif
            </p>


            <p class="mb-2"><strong>Location:</strong> {{ ucfirst($product->city ?? 'Unknown') }}, {{ ucfirst($product->province ?? 'Unknown') }}</p>
            <p class="mb-2"><strong>Stock:</strong> {{ $product->stock }} kg available</p>
            @if ($product->ripeness)
                <p class="mb-2"><strong>Ripeness:</strong> {{ $product->ripeness }}</p>
            @endif

            @if ($product->harvested_at)
                <p class="mb-2"><strong>Harvested:</strong> {{ \Carbon\Carbon::parse($product->harvested_at)->format('F d, Y') }}</p>
            @endif

            @if ($product->shelf_life)
                <p class="mb-2"><strong>Shelf Life:</strong> {{ ucfirst($product->shelf_life) }}</p>
            @endif

            @if ($product->storage_instructions)
                <p class="mb-4"><strong>Storage Instructions:</strong> {{ ucfirst($product->storage_instructions) }}</p>
            @endif

            {{-- Quantity + Cart/Buy Now --}}
            <div class="d-flex align-items-center gap-3 mb-4">

                <label for="quantityInput" class="me-2 mb-0"><strong>Quantity:</strong></label>

                <input
                    type="number"
                    id="quantityInput"
                    min="1"
                    max="{{ $product->stock }}"
                    value="1"
                    class="form-control w-25"
                    {{ $product->stock <= 0 ? 'disabled' : '' }}
                >

                {{-- Add to Cart --}}
                <form id="addToCartForm" action="{{ route('buyer.products.add', $product->id) }}" method="POST">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <input type="hidden" name="quantity" id="quantityAddToCart">
                    <button type="submit" class="btn btn-success" {{ $product->stock <= 0 ? 'disabled' : '' }}>
                        Add to Cart
                    </button>
                </form>

                {{-- Buy Now --}}
                <form id="buyNowForm" action="{{ route('buyer.products.buyNow', $product->id) }}" method="POST">
                    @csrf
                    <input type="hidden" name="quantity" id="quantityBuyNow">
                    <button type="submit" class="btn btn-dark" {{ $product->stock <= 0 ? 'disabled' : '' }}>
                        {{ $product->stock <= 0 ? 'Out of Stock' : 'Buy Now' }}
                    </button>
                </form>
            </div>
        </div>
    </div>


    {{-- Seller Info --}}
    <div class="p-4 rounded bg-success-subtle text-dark d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center">
        <img src="{{ asset('storage/business_photos/' . $product->farmer->business_photo) }}"
            class="rounded me-3" width="80" height="80" alt="Seller Avatar">
            <div>
                <h5 class="mb-0 fw-bold">{{ $product->farmer->business_name ?? $product->farmer->name ?? 'Unknown' }}</h5>
                <small class="text-muted">Active recently</small>
                <div class="mt-2">
                    <a class="btn btn-success" href="{{ route('buyer.messages.create', ['receiver_id' => $product->farmer->id]) }}">
                        <i class="bi bi-chat-dots"></i> Message
                    </a>
                    <a class="btn btn-success" href="{{ route('buyer.farmer-profile', ['id' => $product->farmer->id]) }}">View Shop</a>
                </div>
            </div>
        </div>
        <div class="border-start ps-4 d-flex flex-wrap gap-3">
            <div><strong>Seller Rating:</strong> {{ $sellerRating ? number_format($sellerRating, 2) . ' ⭐' : 'N/A' }}</div>
            <div><strong>Products:</strong> {{ $product->farmer->products->count() ?? 0 }}</div>
            <div><strong>Response Rate:</strong> 100%</div>
            <div><strong>Joined:</strong> {{ $product->farmer->created_at->diffForHumans() ?? 'Unknown' }}</div>
        </div>
    </div>

    {{-- Customer Reviews --}}
    <div>
        <h5 class="fw-bold mb-3">Customer Reviews</h5>
        @forelse ($reviews as $review)
            <div class="bg-white p-3 rounded mb-3 border shadow-sm">
                <p class="mb-1 fw-bold">{{ $review->user->name ?? 'buyer' }}</p>
                <small class="text-muted">{{ $review->created_at->format('F d, Y') }}</small>
                <p class="mb-1">{{ $review->comment }}</p>
                <div>⭐ {{ $review->rating }}</div>
            </div>
        @empty
            <p class="text-muted">No reviews yet.</p>
        @endforelse
    </div>

    {{-- Related Products --}}
    @if($relatedProducts->count())
        <hr class="my-5">
        <h5 class="fw-bold mb-3">More from this Seller</h5>
        <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-4">
            @foreach($relatedProducts as $item)
                @if ($item->id !== $product->id)
                    <div class="col">
                        <a href="{{ route('product.show', $item->id) }}" class="text-decoration-none text-dark">
                            {{-- Product Card --}}
                            <div class="card h-100 shadow-sm hover-lift">
                                <img src="{{ asset('storage/' . $item->image) }}"
                                     class="card-img-top"
                                     alt="{{ $item->name }}"
                                     style="height: 180px; object-fit: cover; border-radius: 10px;">
                                <div class="card-body">
                                    <h6 class="card-title">{{ strtoupper($item->name) }}</h6>
                                    <p class="card-text text-success fw-bold">₱{{ number_format($item->price, 2) }}</p>
                                </div>
                            </div>
                        </a>
                    </div>
                @endif
            @endforeach
        </div>
    @endif
</div>

{{-- JS to sync quantity input --}}
<script>
    const quantityInput = document.getElementById('quantityInput');
    const quantityAddToCart = document.getElementById('quantityAddToCart');
    const quantityBuyNow = document.getElementById('quantityBuyNow');

    const syncQuantity = () => {
        quantityAddToCart.value = quantityInput.value;
        quantityBuyNow.value = quantityInput.value;
    };

    document.addEventListener('DOMContentLoaded', syncQuantity);
    quantityInput.addEventListener('input', syncQuantity);
</script>
@endsection
