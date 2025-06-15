@extends('layouts.farmer')

@section('title', 'Products')

@push('styles')
<style>
  .product-list-container {
    background-color: #eee;
    padding: 20px;
    border-radius: 10px;
  }

  .product-item {
    background-color: #f5f5f5;
    border: 1px solid #ccc;
    border-left: 5px solid #17631d;
    padding: 15px;
    margin-bottom: 10px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
  }

  .product-info {
    display: flex;
    align-items: center;
    gap: 15px;
    width: 30%;
    min-width: 250px;
  }

  .product-info img {
    width: 60px;
    height: 60px;
    object-fit: cover;
    border-radius: 6px;
  }

  .product-price-stock {
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    width: 15%;
    min-width: 120px;
    text-align: center;
    font-size: 0.95rem;
  }

  .product-statistics {
    width: 20%;
    min-width: 140px;
    display: flex;
    flex-direction: column;
    justify-content: center;
  }

  .bar-track {
    height: 8px;
    background: #d9d9d9;
    border-radius: 5px;
    overflow: hidden;
  }

  .bar-fill {
    height: 100%;
    background: #17631d;
  }

  .product-header {
    display: flex;
    font-weight: bold;
    text-transform: uppercase;
    color: #6c757d;
    margin-bottom: 0.75rem;
    padding: 0 10px;
  }

  .product-header > div {
    text-align: center;
  }

  .product-header div:nth-child(1) { width: 30%; text-align: left; }
  .product-header div:nth-child(2),
  .product-header div:nth-child(3) { width: 15%; }
  .product-header div:nth-child(4) { width: 20%; }
  .product-header div:nth-child(5) { width: 15%; }
</style>
@endpush

@section('content')
{{-- @if(session('success'))
  <div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
@endif --}}

<h2 class="fw-bold text-success mb-4">PRODUCTS</h2>

<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
  <div class="d-flex flex-wrap gap-2">
    <input type="text" class="form-control" placeholder="Search product..." style="max-width: 200px;">
    <button class="btn btn-success">Search</button>
    <select class="form-select" style="max-width: 180px;">
      <option selected>Show: All Products</option>
      <option value="in_stock">In Stock</option>
      <option value="sold_out">Sold Out</option>
    </select>
  </div>
  @php $isVerified = auth()->user()->is_verified; @endphp

  @if ($isVerified)
    <a href="{{ route('farmer.products.create') }}" class="btn btn-success">
      <i class="bi bi-plus-lg"></i> Add Product
    </a>
  @else
    <button class="btn btn-secondary" disabled data-bs-toggle="tooltip" title="Account verification required to add products.">
      <i class="bi bi-lock-fill"></i> Add Product
    </button>
  @endif

</div>

<div class="product-list-container">
  <div class="d-flex fw-bold text-uppercase text-muted mb-2 px-2">
    <div style="width: 30%;">Product</div>
    <div style="width: 23%; text-align: center;">Price</div>
    <div style="width: 12%; text-align: center;">Stock</div>
    <div style="width: 28%; text-align: center;">Statistics</div>
    <div style="width: 7%; text-align: center;">Actions</div>
  </div>

  @forelse ($products as $product)
  <div class="product-item">
    <div class="product-info" style="width: 30%;">
      <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}">
      <div>
        <strong class="text-uppercase">{{ $product->name }}</strong>
        <div class="text-muted small">
          Created: {{ $product->created_at->format('M d, Y') }}<br>
          Updated: {{ $product->updated_at->format('M d, Y') }}
        </div>
      </div>
    </div>

    <div class="product-price-stock text-center" style="width: 23%;">
      <div><strong>₱{{ number_format($product->price, 2) }}</strong></div>
      <div class="text-muted small">per kilo</div>
    </div>

    <div class="product-price-stock text-center" style="width: 12%;">
      <div><strong>{{ $product->stock }}</strong></div>
      <div class="text-muted small">kilo</div>
    </div>

    <div class="product-statistics d-flex flex-column justify-content-center" style="width: 28%;">
      <small class="text-end">{{ $product->sales_count }} sales</small>
      <div class="bar-track mt-1">
        <div class="bar-fill" style="width: {{ min(100, $product->sales_count) }}%;"></div>
      </div>
    </div>

    <div class="d-flex gap-1" style="width: 7%;">
      <a href="{{ route('farmer.products.edit', $product->id) }}" class="btn btn-sm btn-outline-success">
        <i class="bi bi-pencil"></i>
      </a>
      <form action="{{ route('farmer.products.destroy', $product->id) }}" method="POST" onsubmit="return confirm('Delete this product?')">
        @csrf
        @method('DELETE')
        <button class="btn btn-sm btn-outline-danger">
          <i class="bi bi-trash"></i>
        </button>
      </form>
    </div>
  </div>
  @empty
    <p class="text-muted text-center">No products found.</p>
  @endforelse
</div>

@if ($products->count())
  <div class="d-flex justify-content-end align-items-center mt-3">
    {{ $products->links() }}
  </div>
@endif

<footer class="text-center mt-5 text-muted small">
  COPYRIGHT 2025–2026 NUTRITECH. ALL RIGHTS RESERVED<br>
  <a href="#" class="text-success text-decoration-none">RETURN AND REFUND POLICY</a>
</footer>
@endsection
