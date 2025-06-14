<!-- resources/views/farmer/orders/index.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Orders</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>

<div class="container py-5">
    <h2 class="text-success mb-4">Manage Orders</h2>

    <!-- Status Tabs -->
    <ul class="nav nav-tabs mb-4">
        <li class="nav-item">
            <a class="nav-link {{ is_null($status) ? 'active' : '' }}" href="{{ route('farmer.orders.index') }}">All</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $status === 'Pending' ? 'active' : '' }}" href="{{ route('farmer.orders.index', ['status' => 'Pending']) }}">To Ship</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $status === 'Shipped' ? 'active' : '' }}" href="{{ route('farmer.orders.index', ['status' => 'Shipped']) }}">To Receive</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $status === 'Delivered' ? 'active' : '' }}" href="{{ route('farmer.orders.index', ['status' => 'Delivered']) }}">Completed</a>
        </li>
    </ul>

    <!-- Orders -->
    @forelse ($orders as $order)
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between">
                <strong>Order: {{ $order->order_code }}</strong>
<span class="status {{ strtolower(str_replace([' ', '/'], ['-', '-'], $order->status)) }}">
    {{ strtoupper($order->status) }}
</span>

            </div>
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <img src="{{ asset('storage/' . $order->product->image) }}" width="60" class="me-3" onerror="this.src='{{ asset('img/default.png') }}'">
                    <div>
                        <h6>{{ $order->product->name }}</h6>
                        <small>{{ $order->quantity }}kg @ ₱{{ number_format($order->price, 2) }} / kg</small>
                    </div>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <span class="fw-bold">Total: ₱{{ number_format($order->total_price, 2) }}</span>
                    @if ($order->status === 'Pending')
                        <form method="POST" action="{{ route('farmer.orders.updateStatus', $order->id) }}">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="Shipped">
                            <button type="submit" class="btn btn-outline-primary btn-sm">Mark as Shipped</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    @empty
        <div class="alert alert-info">No orders found for this filter.</div>
    @endforelse
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
