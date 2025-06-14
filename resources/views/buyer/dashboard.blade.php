@extends('layouts.farmer')

@section('title', 'Dashboard')

@push('styles')
<style>
    .card {
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }
    .dashboard-metric {
        min-height: 110px;
    }
    .dashboard-metric h3 {
        font-size: 28px;
        margin: 0;
    }
    .dashboard-metric p {
        margin: 0;
        font-weight: 600;
    }
    .best-seller-img {
        width: 60px;
        height: 60px;
        object-fit: cover;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <h2 class="fw-bold text-success mb-4">DASHBOARD</h2>

    {{-- ✅ Metric Cards --}}
    <div class="row g-4 mb-4">
        <div class="col-md-2">
            <div class="card bg-success text-white text-center p-3 dashboard-metric">
                <h3>{{ $ordersCount }}</h3>
                <p>ORDERS</p>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-success text-white text-center p-3 dashboard-metric">
                <h3>{{ $messagesCount }}</h3>
                <p>MESSAGES</p>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-success text-white text-center p-3 dashboard-metric">
                <h3>{{ $refundCount }}</h3>
                <p>REFUND</p>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-success text-white text-center p-3 dashboard-metric">
                <h3>{{ $soldoutCount }}</h3>
                <p>SOLD OUT</p>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-success text-white text-center p-3 dashboard-metric">
                <h3>{{ $followerCount }}</h3>
                <p>FOLLOWERS</p>
            </div>
        </div>
    </div>

    {{-- ✅ Sales + Best Selling --}}
    <div class="row g-4">
        <div class="col-md-8">
            <div class="card bg-dark text-white p-4">
                <h3 class="fw-bold">SALES REPORT</h3>
                <p>₱250,000 SALES OVER TIME</p>
                <div class="text-end text-success mt-5">
                    ↑ 65.2% since last 4 months
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card bg-dark text-white p-3">
                <h5 class="fw-bold mb-3">BEST SELLING</h5>
                @foreach ($topProducts as $product)
                    <div class="d-flex align-items-start mb-3">
                        <img src="{{ asset('storage/' . $product->image) }}" class="me-3 rounded best-seller-img" alt="{{ $product->name }}">
                        <div>
                            <h6 class="fw-bold text-uppercase">{{ $product->name }}</h6>
                            <p class="mb-0">Sales: ₱{{ number_format($product->price, 2) }}</p>
                            <p class="mb-0">Rating: {{ number_format($product->reviews_avg_rating ?? 0, 1) }}</p>
                            <p class="mb-0">Reviews: {{ $product->reviews_count ?? 0 }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
