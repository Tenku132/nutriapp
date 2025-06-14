@extends('layouts.farmer')

@section('title', 'Orders')

@push('styles')
<style>
  .order-card {
    background: #f5f5f5;
    border: 1px solid #ccc;
    border-left: 5px solid #17631d;
    padding: 1rem;
    margin-bottom: 1rem;
    border-radius: 8px;
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
  }

  .order-left {
    display: flex;
    gap: 1rem;
    align-items: center;
    flex: 1 1 300px;
  }

  .order-left img {
    width: 80px;
    height: 80px;
    object-fit: cover;
    border-radius: 8px;
  }

  .order-center {
    flex: 0 0 140px;
    text-align: center;
    align-self: center;
    font-weight: bold;
    color: #17631d;
  }

  .order-right {
    flex: 0 0 220px;
    text-align: right;
  }

  .order-controls a {
    text-decoration: none;
    margin-right: 1rem;
    color: #14532d;
    font-weight: bold;
  }

  .order-controls a.active {
    text-decoration: underline;
  }
</style>
@endpush

@section('content')
<h2 class="fw-bold text-success mb-4">ORDERS</h2>

<!-- 🔍 Filter Tabs & Search -->
<div class="d-flex justify-content-between align-items-center flex-wrap mb-4 px-1 order-controls">
  <div class="d-flex flex-wrap gap-3">
    @foreach (['' => 'ALL', 'paid' => 'PENDING', 'to ship' => 'TO SHIP', 'completed' => 'COMPLETED', 'cancelled' => 'CANCELED'] as $key => $label)
      <a href="{{ route('farmer.orders.index', $key ? ['status' => $key] : []) }}" class="{{ request('status') === $key ? 'active' : '' }}">
        {{ $label }}
        <span class="badge {{ $key == 'paid' ? 'bg-warning text-dark' : ($key == 'to ship' ? 'bg-info text-dark' : ($key == 'completed' ? 'bg-success' : ($key == 'cancelled' ? 'bg-danger' : 'bg-secondary'))) }}">
          {{ $statusCounts[$key ?: 'all'] }}
        </span>
      </a>
    @endforeach
  </div>

  <form action="{{ route('farmer.orders.index') }}" method="GET" class="d-flex gap-2" style="max-width: 300px;">
    <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm" placeholder="Search product">
    <button type="submit" class="btn btn-success btn-sm">SEARCH</button>
  </form>
</div>

<!-- 📦 Order Cards -->
@foreach($orders as $order)
  @if ($order->product)
    @php $normalized = strtolower($order->status); @endphp

    <div class="order-card">
      <!-- 🧊 Left -->
      <div class="order-left">
        <img src="{{ asset('storage/' . $order->product->image) }}" alt="{{ $order->product->name }}">
        <div>
          <strong>{{ strtoupper($order->product->name) }}</strong><br>
          <small>Total Order: x{{ $order->quantity }} KILO</small><br>
          <small>Total Price: ₱{{ number_format($order->total_price, 2) }}</small><br>
          <small><strong>Buyer Address:</strong>
            {{ implode(', ', array_filter([
              $order->buyer_address,
              $order->buyer_city,
              $order->buyer_region,
              $order->buyer_postal_code
            ])) ?: 'N/A' }}
          </small>
        </div>
      </div>

      <!-- 🏷 Center Badge -->
      <div class="order-center text-uppercase">
        @switch($normalized)
          @case('paid')
          @case('pending')
            <span class="badge bg-warning text-dark">🕓 Pending</span> @break
          @case('to ship')
            <span class="badge bg-info text-dark">📦 To Ship</span> @break
          @case('shipped')
            <span class="badge bg-primary">✈ Shipped</span> @break
          @case('completed')
            <span class="badge bg-success">✔ Completed</span> @break
          @case('cancelled')
            <span class="badge bg-secondary">❌ Cancelled</span> @break
          @case('return/refund')
            <span class="badge bg-danger">↩ Return/Refund</span> @break
        @endswitch
      </div>

      <!-- 🔘 Right Actions -->
      <div class="order-right">
        <small class="d-block">Order ID: {{ $order->order_code }}</small>
        <small class="text-muted d-block">Ordered on: {{ \Carbon\Carbon::parse($order->created_at)->format('F d, Y') }}</small>

        @if (!in_array($normalized, ['completed', 'cancelled', 'return/refund']))
          @if ($normalized === 'paid' || $normalized === 'pending')
            <form method="POST" action="{{ route('farmer.orders.updateStatus', $order->id) }}" class="mt-2">
              @csrf @method('PATCH')
              <input type="hidden" name="status" value="to ship">
              <button type="submit" class="btn btn-sm btn-outline-primary">Mark as To Ship</button>
            </form>
          @elseif ($normalized === 'to ship')
            <form method="POST" action="{{ route('farmer.orders.updateStatus', $order->id) }}" class="mt-2">
              @csrf @method('PATCH')
              <input type="hidden" name="status" value="shipped">
              <button type="submit" class="btn btn-sm btn-outline-info">Mark as Shipped</button>
            </form>
          @endif
        @endif

        <button class="btn btn-sm btn-outline-success mt-2" data-bs-toggle="modal" data-bs-target="#orderModal{{ $order->id }}">
          View Details
        </button>
      </div>
    </div>

<!-- 📋 Modal -->
<div class="modal fade" id="orderModal{{ $order->id }}" tabindex="-1" aria-labelledby="orderModalLabel{{ $order->id }}" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title">🧾 Order Details – {{ $order->order_code }}</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <div class="mb-3">
          <strong>Status:</strong> 
          <span class="badge bg-{{ $normalized == 'pending' || $normalized == 'paid' ? 'warning text-dark' : ($normalized == 'to ship' ? 'info text-dark' : ($normalized == 'shipped' ? 'primary' : ($normalized == 'completed' ? 'success' : 'secondary'))) }}">
            {{ strtoupper($order->status) }}
          </span>
          <br>
          <small class="text-muted">Ordered on: {{ \Carbon\Carbon::parse($order->created_at)->format('F d, Y - h:i A') }}</small>
        </div>

        <hr class="my-3">

        <h6 class="fw-bold text-success mb-2">🛒 Product Information</h6>
        <div class="row mb-3">
          <div class="col-md-6">
            <p><strong>Product:</strong> {{ strtoupper($order->product->name) }}</p>
            <p><strong>Category:</strong> {{ $order->product->category }}</p>
            <p><strong>Province:</strong> {{ $order->product->province }}</p>
          </div>
          <div class="col-md-6">
            <p><strong>Quantity:</strong> {{ $order->quantity }} kilogram{{ $order->quantity > 1 ? 's' : '' }}</p>
            <p><strong>Total Price:</strong> ₱{{ number_format($order->total_price, 2) }}</p>
          </div>
        </div>

        <hr class="my-3">

        <h6 class="fw-bold text-success mb-2">📍 Delivery Address</h6>
        <div class="row mb-3">
          <div class="col-md-6">
            <p><strong>Street:</strong> {{ $order->buyer_address ?? 'N/A' }}</p>
            <p><strong>City:</strong> {{ $order->buyer_city ?? 'N/A' }}</p>
          </div>
          <div class="col-md-6">
            <p><strong>Region:</strong> {{ $order->buyer_region ?? 'N/A' }}</p>
            <p><strong>Postal Code:</strong> {{ $order->buyer_postal_code ?? 'N/A' }}</p>
          </div>
        </div>

        <hr class="my-3">

        <h6 class="fw-bold text-success mb-2">👤 Buyer Information</h6>
        <p><strong>Name:</strong> {{ $order->buyer->name ?? 'N/A' }}</p>
        <p><strong>Phone:</strong> 
          @if ($order->buyer_phone)
            <a href="tel:{{ $order->buyer_phone }}">{{ $order->buyer_phone }}</a>
          @else
            N/A
          @endif
        </p>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

  @endif
@endforeach
@endsection
