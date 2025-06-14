@extends('layouts.farmer')

@section('title', 'Dashboard')

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const ctx = document.getElementById('salesChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: @json($months),
            datasets: [
                {
                    label: 'This Year',
                    data: @json($salesThisYear),
                    backgroundColor: '#28a745',
                },
                {
                    label: 'Last Year',
                    data: @json($salesLastYear),
                    backgroundColor: '#c3e6cb',
                }
            ]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '₱' + value.toLocaleString();
                        }
                    }
                }
            }
        }
    });
});
</script>
@endpush

@section('content')
<div class="container-fluid py-4">
    <h2 class="fw-bold text-success mb-4">📊 DASHBOARD</h2>

    {{-- 📌 Metric Cards --}}
    <div class="row row-cols-2 row-cols-md-5 g-4 mb-5">
        <div class="col">
            <div class="card bg-success text-white text-center p-3 shadow-sm">
                <div class="fs-2">🛒</div>
                <h3>{{ $ordersCount }}</h3>
                <p>ORDERS</p>
                <a href="{{ route('farmer.orders.index') }}" class="text-white small">View Orders →</a>
            </div>
        </div>
        <div class="col">
            <div class="card bg-success text-white text-center p-3 shadow-sm">
                <div class="fs-2">💬</div>
                <h3>{{ $messagesCount }}</h3>
                <p>MESSAGES</p>
                <a href="{{ route('farmer.messages.inbox') }}" class="text-white small">Go to Inbox →</a>
            </div>
        </div>
        <div class="col">
            <div class="card bg-success text-white text-center p-3 shadow-sm">
                <div class="fs-2">💸</div>
                <h3>{{ $refundCount }}</h3>
                <p>REFUNDS</p>
                <a href="#" class="text-white small">View Refunds →</a>
            </div>
        </div>
        <div class="col">
            <div class="card bg-success text-white text-center p-3 shadow-sm">
                <div class="fs-2">📦</div>
                <h3>{{ $soldOutCount }}</h3>
                <p>SOLD OUT</p>
                <a href="#" class="text-white small">Restock →</a>
            </div>
        </div>
        <div class="col">
            <div class="card bg-success text-white text-center p-3 shadow-sm">
                <div class="fs-2">👥</div>
                <h3>{{ $followerCount }}</h3>
                <p>FOLLOWERS</p>
                <a href="#" class="text-white small">View Followers →</a>
            </div>
        </div>
    </div>

    <hr class="my-4">

    {{-- 📈 Sales + 🏆 Best Sellers --}}
    <div class="row g-4">
        <div class="col-md-8">
            <div class="card p-4 shadow-sm">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0">📈 SALES REPORT</h5>
                    <form method="GET" action="{{ route('farmer.dashboard') }}" class="d-flex">
                        <select name="range" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="6months" {{ request('range') === '6months' ? 'selected' : '' }}>Last 6 Months</option>
                            <option value="year" {{ request('range') === 'year' ? 'selected' : '' }}>This Year</option>
                            <option value="7days" {{ request('range') === '7days' ? 'selected' : '' }}>Last 7 Days</option>
                            <option value="month" {{ request('range') === 'month' ? 'selected' : '' }}>This Month</option>
                        </select>
                    </form>
                </div>
                <canvas id="salesChart" height="180"></canvas>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card p-3 shadow-sm">
                <h5 class="fw-bold mb-3">🏆 BEST SELLING PRODUCTS</h5>
                @forelse ($topProducts as $product)
                    <div class="d-flex align-items-start mb-3 border-bottom pb-2">
                        <img src="{{ asset('storage/' . $product->image) }}" class="me-3 rounded" style="width: 60px; height: 60px; object-fit: cover;">
                        <div>
                            <h6 class="fw-bold text-uppercase mb-1">{{ $product->name }}</h6>
                            <div class="mb-1">Sales: ₱{{ number_format($product->price, 2) }}</div>
                            <div class="mb-1">
                                ⭐ {{ number_format($product->reviews_avg_rating ?? 0, 1) }} ({{ $product->reviews_count }} reviews)
                            </div>
                            <a href="{{ route('farmer.products.edit', $product->id) }}" class="btn btn-sm btn-outline-success me-2">Edit</a>
                            <a href="#" class="btn btn-sm btn-outline-secondary">Restock</a>
                        </div>
                    </div>
                @empty
                    <p class="text-muted">No best-selling products yet.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
