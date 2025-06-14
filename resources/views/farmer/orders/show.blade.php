@extends('layouts.farmer')

@section('title', 'Order Details')

@push('styles')
<style>
  .order-details-card {
    background: #f8f9fa;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.05);
  }

  .section-header {
    border-bottom: 1px solid #ccc;
    margin-bottom: 15px;
    padding-bottom: 6px;
    font-size: 1.2rem;
    font-weight: 600;
    color: #2e7d32;
  }

  .order-info-label {
    font-weight: 600;
    color: #444;
    width: 130px;
    display: inline-block;
  }

  .product-image {
    width: 80px;
    height: 80px;
    object-fit: cover;
    border-radius: 8px;
    margin-right: 15px;
  }

  .info-block {
    margin-bottom: 12px;
  }

  .btn-back {
    margin-top: 30px;
  }
</style>
@endpush

@section('content')
<div class="container py-4">
  <h2 class="mb-4 fw-bold text-success">Order Details</h2>

  <div class="order-details-card">

    <div class="mb-4">
      <div class="section-header">Order Summary</div>
      <div class="info-block"><span class="order-info-label">Order ID:</span> {{ $order->order_code }}</div>
      <div class="info-block"><span class="order-info-label">Status:</span> {{ ucfirst($order->status) }}</div>
      <div class="info-block"><span class="order-info-label">Ordered On:</span> {{ \Carbon\Carbon::parse($order->created_at)->format('F d, Y - h:i A') }}</div>
      <div class="info-block"><span class="order-info-label">Total Price:</span> ₱{{ number_format($order->total_price, 2) }}</div>
      <div class="info-block"><span class="order-info-label">Quantity:</span> x{{ $order->quantity }} kilo</div>
    </div>

    @if ($order->product)
    <div class="mb-4">
      <div class="section-header">Product Details</div>
      <div class="d-flex align-items-center mb-2">
        <img src="{{ asset('storage/' . $order->product->image) }}" alt="{{ $order->product->name }}" class="product-image">
        <div>
          <div class="info-block"><strong>{{ $order->product->name }}</strong></div>
          <div class="text-muted small">{{ $order->product->description ?? 'No description provided.' }}</div>
        </div>
      </div>
      <div class="info-block"><span class="order-info-label">Category:</span> {{ $order->product->category ?? 'N/A' }}</div>
      <div class="info-block"><span class="order-info-label">Province:</span> {{ $order->product->province ?? 'N/A' }}</div>
      <div class="info-block"><span class="order-info-label">Harvested:</span> {{ $order->product->harvested_at ? \Carbon\Carbon::parse($order->product->harvested_at)->diffForHumans() : 'N/A' }}</div>
      <div class="info-block"><span class="order-info-label">Ripeness:</span> {{ ucfirst($order->product->ripeness) ?? 'N/A' }}</div>
    </div>
    @endif

    <div class="mb-4">
      <div class="section-header">Buyer Information</div>
      <div class="info-block"><span class="order-info-label">Name:</span> {{ $order->buyer->name ?? 'N/A' }}</div>
      <div class="info-block"><span class="order-info-label">Phone:</span> {{ $order->buyer_phone ?? 'N/A' }}</div>
      <div class="info-block">
        <span class="order-info-label">Address:</span>
        {{ implode(', ', array_filter([
          $order->buyer_address,
          $order->buyer_city,
          $order->buyer_region,
          $order->buyer_postal_code
        ])) ?: 'N/A' }}
      </div>
    </div>

    <a href="{{ route('farmer.orders.index') }}" class="btn btn-outline-secondary btn-back">
      ← Back to Orders
    </a>
  </div>
</div>
@endsection
