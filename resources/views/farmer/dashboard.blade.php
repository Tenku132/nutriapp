@extends('layouts.farmer')

@section('title', 'Dashboard')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
@endpush

@php
    // Customize this variable to change the chart background color
    $chartBgColor = '#68db8b'; // light green, change as desired
@endphp

@section('content')
<div class="container-fluid">

    @if (!auth()->user()->is_verified)
        <div class="alert alert-warning d-flex justify-content-between align-items-center">
            <div>
                <i class="bi bi-exclamation-triangle-fill me-2 text-warning"></i>
                Your account is not verified. Some features are disabled.
            </div>
            <a href="{{ route('farmer.settings') }}" class="btn btn-sm btn-outline-success">
                Go to Settings
            </a>
        </div>
    @endif


    <h2 class="fw-bold text-success mb-4">DASHBOARD</h2>

    {{-- ✅ Dashboard Metrics --}}
    <div class="row row-cols-2 row-cols-md-5 g-4 mb-4">
        <div class="col">
            <div class="card bg-success text-white text-center p-3 {{ !auth()->user()->is_verified ? 'opacity-50' : '' }}">

                <div class="mb-1"><i class="bi bi-cart-check-fill fs-4"></i></div>
                <h3>{{ $ordersCount }}</h3>
                <p class="mb-1">ORDERS</p>
                <a href="{{ route('farmer.orders.index') }}" class="text-white text-decoration-underline small">View Orders</a>
            </div>
        </div>
        <div class="col">
            <div class="card bg-success text-white text-center p-3 {{ !auth()->user()->is_verified ? 'opacity-50' : '' }}">

                <div class="mb-1"><i class="bi bi-chat-dots-fill fs-4"></i></div>
                <h3>{{ $messagesCount }}</h3>
                <p class="mb-1">MESSAGES</p>
                <a href="{{ route('farmer.messages.inbox') }}" class="text-white text-decoration-underline small">View Messages</a>
            </div>
        </div>
        <div class="col">
            <div class="card bg-success text-white text-center p-3 {{ !auth()->user()->is_verified ? 'opacity-50' : '' }}">

                <div class="mb-1"><i class="bi bi-cash-stack fs-4"></i></div>
                <h3>{{ $refundCount }}</h3>
                <p class="mb-1">REFUND</p>
                <a href="#" class="text-white text-decoration-underline small">View Refunds</a>
            </div>
        </div>
        <div class="col">
            <div class="card bg-success text-white text-center p-3 {{ !auth()->user()->is_verified ? 'opacity-50' : '' }}">

                <div class="mb-1"><i class="bi bi-box-seam fs-4"></i></div>
                <h3>{{ $soldOutCount }}</h3>
                <p class="mb-1">SOLD OUT</p>
                <a href="#" class="text-white text-decoration-underline small">Restock</a>
            </div>
        </div>
        <div class="col">
            <div class="card bg-success text-white text-center p-3 {{ !auth()->user()->is_verified ? 'opacity-50' : '' }}">

                <div class="mb-1"><i class="bi bi-people-fill fs-4"></i></div>
                <h3>{{ $followerCount }}</h3>
                <p class="mb-1">FOLLOWERS</p>
                <a href="#" class="text-white text-decoration-underline small">View Followers</a>
            </div>
        </div>
    </div>

    {{-- 📈 Sales Report & 🏆 Best Selling --}}
    <div class="row g-4">
        <div class="col-md-8">
                <div class="analytics-card">
                    <h5>📊 Sales by Product</h5>
                    <canvas id="salesChart" height="220"></canvas>
                </div>

                <div class="analytics-card">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">📈 Monthly Sales ({{ now()->year }})</h5>
                        <select id="chartTypeToggle" class="form-select form-select-sm w-auto">
                            <option value="line" selected>Line Chart</option>
                            <option value="bar">Bar Chart</option>
                        </select>
                    </div>
                    <canvas id="monthlySalesChart" height="150"></canvas>
            </div>

            <div class="analytics-card">
                <h5><i class="bi bi-graph-up-arrow me-2"></i>SALES REPORT</h5>
                <p class="mb-1">₱{{ number_format($totalSales, 2) }} SALES OVER TIME</p>
                <div class="text-muted text-end small">
                    ↑ 65.2% since last 4 months
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card bg-dark text-white p-3 {{ !auth()->user()->is_verified ? 'opacity-50' : '' }}">

                <h5 class="fw-bold mb-3"><i class="bi bi-award-fill me-2"></i>BEST SELLING</h5>
                @foreach ($topProducts as $product)
                    <div class="d-flex align-items-start mb-3">
                        <img src="{{ asset('storage/' . $product->image) }}" class="me-3 rounded" style="width: 60px; height: 60px; object-fit: cover;">
                        <div>
                            <h6 class="fw-bold text-uppercase">{{ $product->name }}</h6>
                            <p class="mb-1">Sales: ₱{{ number_format($product->total_sales, 2) }}</p>

                            <p class="mb-1">
                                Rating: {{ number_format($product->reviews_avg_rating ?? 0, 1) }}
                                @for ($i = 0; $i < floor($product->reviews_avg_rating); $i++)
                                    <i class="bi bi-star-fill text-warning"></i>
                                @endfor
                            </p>
                            <p class="mb-0">Reviews: {{ $product->reviews_count }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Plugin to set chart area background
    const chartAreaBg = {
        id: 'customCanvasBackgroundColor',
        beforeDraw: (chart, args, options) => {
            const {ctx, chartArea} = chart;
            ctx.save();
            ctx.fillStyle = options.color || '#fff';
            ctx.fillRect(chartArea.left, chartArea.top, chartArea.right - chartArea.left, chartArea.bottom - chartArea.top);
            ctx.restore();
        }
    };

    // Product Sales Chart
    const salesCtx = document.getElementById('salesChart').getContext('2d');
    new Chart(salesCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($productNames) !!},
            datasets: [{
                label: '₱ Sales',
                data: {!! json_encode($productSalesTotals) !!},
                backgroundColor: 'rgba(40, 167, 69, 0.7)',
                borderColor: 'rgba(40, 167, 69, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { callback: value => '₱' + value }
                }
            }
        },
        plugins: [chartAreaBg]
    });

    // Monthly Sales Chart with Toggle
    const ctx = document.getElementById('monthlySalesChart').getContext('2d');
    let chartType = 'line';
    let monthlyChart;

    function renderMonthlyChart(type) {
        if (monthlyChart) monthlyChart.destroy();

        const baseDataset = {
            label: '₱ Total Sales',
            data: {!! json_encode($monthTotals) !!},
            backgroundColor: type === 'bar' ? 'rgba(75, 192, 192, 0.4)' : 'rgba(75, 192, 192, 0.2)',
            borderColor: 'rgba(75, 192, 192, 1)',
            borderWidth: 2
        };
        if (type === 'line') {
            baseDataset.fill = true;
            baseDataset.tension = 0.3;
            baseDataset.pointRadius = 4;
            baseDataset.pointHoverRadius = 6;
        }
        monthlyChart = new Chart(ctx, {
            type: type,
            data: {
                labels: {!! json_encode($monthLabels) !!},
                datasets: [baseDataset]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: value => '₱' + value
                        }
                    }
                }
            },
            plugins: {
                customCanvasBackgroundColor: {
                    color: '{{ $chartBgColor }}'
                }
            },
            plugins: [chartAreaBg]
        });
    }
    renderMonthlyChart(chartType);
    document.getElementById('chartTypeToggle').addEventListener('change', function () {
        renderMonthlyChart(this.value);
    });
</script>
@endpush
