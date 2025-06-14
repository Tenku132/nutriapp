@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h3 class="mb-4 fw-bold">My Transactions</h3>

    @if($orders->isEmpty())
        <p class="text-muted">You have no transactions yet.</p>
    @else
        <table class="table table-bordered text-center">
            <thead class="table-success">
                <tr>
                    <th>Order Code</th>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Total Price</th>
                    <th>Status</th>
                    <th>Ordered At</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($orders as $order)
                <tr>
                    <td>{{ $order->order_code }}</td>
                    <td>{{ $order->product->name }}</td>
                    <td>{{ $order->quantity }}</td>
                    <td>₱{{ number_format($order->total_price, 2) }}</td>
                    <td><span class="badge bg-warning text-dark">{{ $order->status }}</span></td>
                    <td>{{ $order->created_at->format('M d, Y H:i') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection
