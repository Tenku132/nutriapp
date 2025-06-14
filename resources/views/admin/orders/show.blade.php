@extends('layouts.admin')

@section('title', 'Order Details')

@section('content')
<div class="container py-4">
    <h3 class="mb-4">Order Details</h3>

    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <p><strong>Order Code:</strong> {{ $order->order_code }}</p>
            <p><strong>Product:</strong> {{ $order->product->name ?? 'N/A' }}</p>
            <p><strong>Farmer:</strong> {{ $order->product->farmer->name ?? 'Unknown' }}</p>
            <p><strong>Buyer:</strong> {{ $order->buyer->name ?? 'Unknown' }}</p>
            <p><strong>Email:</strong> {{ $order->buyer->email ?? '—' }}</p>
            <p><strong>Delivery Address:</strong> {{ $order->buyer->address ?? '—' }}</p>
            <p><strong>Status:</strong> 
                <span class="badge bg-secondary">{{ $order->status }}</span>
            </p>
            <p><strong>Quantity:</strong> {{ $order->quantity }}</p>
            <p><strong>Total Price:</strong> ₱{{ number_format($order->total_price, 2) }}</p>
            <p><strong>Ordered On:</strong> {{ $order->created_at->format('d M Y, h:i A') }}</p>
        </div>
    </div>

    <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">← Back to Orders</a>
</div>
@endsection
