@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 px-4 py-3">
            <div class="text-center bg-white p-3 rounded shadow-sm">
                @php $initial = strtoupper(substr(Auth::user()->name, 0, 1)); @endphp

                <div class="d-flex justify-content-center">
                    @if (Auth::user()->profile_photo_path)
                        <img src="{{ asset('storage/' . Auth::user()->profile_photo_path) }}"
                             class="rounded-circle mb-2" width="100" height="100" alt="User Photo">
                    @else
                        <div class="rounded-circle bg-secondary text-white d-flex justify-content-center align-items-center mb-2"
                             style="width:100px; height:100px; font-size:32px;">
                            {{ $initial }}
                        </div>
                    @endif
                </div>

                <h6 class="fw-bold mt-2 mb-3"><i class="bi bi-person-circle me-1"></i> My Account</h6>

                <ul class="nav flex-column gap-2 text-start">
                    <li class="nav-item">
                        <a href="{{ route('buyer.profile.show') }}"
                           class="nav-link {{ request()->is('buyer/profile') ? 'fw-bold text-primary' : 'text-dark' }}">
                            Profile
                        </a>
                    </li>
                    <li class="nav-item"><a href="#" class="nav-link text-dark">Payments</a></li>
                    <li class="nav-item"><a href="#" class="nav-link text-dark">Address</a></li>
                    <li class="nav-item"><a href="#" class="nav-link text-dark">Change Password</a></li>
                    <li class="nav-item">
                        <a href="{{ route('buyer.orders.history') }}"
                           class="nav-link {{ request()->is('buyer/orders/history') ? 'fw-bold text-success' : 'text-dark' }}">
                            <i class="bi bi-clipboard-check me-1"></i> My Purchase
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-md-9 py-4 pe-5">
            <h4 class="text-success mb-4">My Purchase</h4>

            <ul class="nav nav-tabs mb-4">
                <li class="nav-item"><a class="nav-link {{ request('status') == null ? 'active' : '' }}" href="{{ route('buyer.orders.history') }}">All</a></li>
                <li class="nav-item"><a class="nav-link {{ request('status') == 'To Ship' ? 'active' : '' }}" href="{{ route('buyer.orders.history', ['status' => 'To Ship']) }}">To Ship</a></li>
                <li class="nav-item"><a class="nav-link {{ request('status') == 'Pending' ? 'active' : '' }}" href="{{ route('buyer.orders.history', ['status' => 'Pending']) }}">Pending</a></li>
                <li class="nav-item"><a class="nav-link {{ request('status') == 'Completed' ? 'active' : '' }}" href="{{ route('buyer.orders.history', ['status' => 'Completed']) }}">Completed</a></li>
                <li class="nav-item"><a class="nav-link {{ request('status') == 'Cancelled' ? 'active' : '' }}" href="{{ route('buyer.orders.history', ['status' => 'Cancelled']) }}">Cancelled</a></li>
                <li class="nav-item"><a class="nav-link {{ request('status') == 'Return/Refund' ? 'active' : '' }}" href="{{ route('buyer.orders.history', ['status' => 'Return/Refund']) }}">Return/Refund</a></li>
            </ul>

            @forelse ($orders as $order)
                <div class="card mb-3">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <strong>{{ $order->product->farmer->name ?? 'AGRO FRESH' }}</strong>
                        <span class="status {{ strtolower(str_replace([' ', '/'], ['-', '-'], $order->status)) }}">
                            {{ strtoupper($order->status) }}
                        </span>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            @if ($order->product)
                                <a href="{{ route('product.show', $order->product->id) }}" class="text-decoration-none text-dark d-flex align-items-center">
                                    <img src="{{ asset('storage/' . $order->product->image) }}" width="60" class="me-3" onerror="this.src='{{ asset('img/default.png') }}'">
                                    <div>
                                        <h6 class="mb-0 fw-bold">{{ $order->product->name }}</h6>
                                        <small class="text-muted">{{ $order->quantity }}kg @ ₱{{ number_format($order->price, 2) }} / kg</small>
                                    </div>
                                </a>
                            @else
                                <div class="d-flex align-items-center text-muted">
                                    <img src="{{ asset('img/default.png') }}" width="60" class="me-3">
                                    <div>
                                        <h6 class="mb-0 fw-bold">[Product Deleted]</h6>
                                        <small class="text-muted">{{ $order->quantity }}kg — order preserved</small>
                                    </div>
                                </div>
                            @endif
                        </div>

                        {{-- 💳 Payment Status --}}
                        <div class="mb-2">
                            @php
                                $status = $order->payment_status;
                                $label = match ($status) {
                                    'paid' => '✅ PAID',
                                    'pending' => '⏳ PENDING',
                                    'failed' => '❌ FAILED',
                                    default => '⚠ UNKNOWN',
                                };
                            @endphp
                            <small class="text-muted">💳 Payment: {{ $label }}</small>
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <strong class="text-success">₱{{ number_format($order->total_price, 2) }}</strong>
                            <div class="d-flex gap-2 flex-wrap">

                            {{-- Cancel: Allow if Pending or To Ship --}}
                            @if (in_array(strtolower($order->status), ['pending', 'to ship']))
                                <form method="POST" action="{{ route('buyer.orders.cancel', $order->id) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-outline-danger btn-sm"
                                            onclick="return confirm('Are you sure you want to cancel this order?')">
                                        Cancel
                                    </button>
                                </form>
                            @endif

                            {{-- Confirm: If Shipped --}}
                            @if (strtolower($order->status) === 'shipped')
                                <form method="POST" action="{{ route('buyer.orders.confirm', $order->id) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-outline-success btn-sm"
                                            onclick="return confirm('Confirm delivery for this order?')">
                                        Confirm Delivery
                                    </button>
                                </form>
                            @endif

                                {{-- Return/Refund: Allow only if Shipped (NOT Completed) --}}
                                @if (strtolower($order->status) === 'shipped')
                                    <form method="POST" action="{{ route('buyer.orders.requestReturn', $order->id) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-outline-danger btn-sm"
                                                onclick="return confirm('Request return/refund for this order?')">
                                            Return / Refund
                                        </button>
                                    </form>
                                @endif

                                {{-- Rate: Show if Completed and no rating yet --}}
                                @if (strtolower($order->status) === 'completed' && !$order->rating)
                                    <a href="{{ route('buyer.orders.rate.create', $order->id) }}" class="btn btn-outline-primary btn-sm">
                                        Rate Product
                                    </a>
                                @endif

                            </div>
                        </div>

                    </div>
                </div>
            @empty
                <div class="alert alert-info text-center">No orders found.</div>
            @endforelse
        </div>
    </div>
</div>
@endsection
