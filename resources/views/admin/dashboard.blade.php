@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid">

  {{-- Summary Cards --}}
  <div class="row g-4 mb-4">
      <div class="col-md-3">
          <div class="card dashboard-metric bg-soft-blue">
              <div class="card-body">
                  <div class="icon mb-2">
                      <i class="bi bi-box-seam fs-3 text-primary"></i>
                  </div>
                  <small class="text-muted">Total Products</small>
                  <h4 class="fw-bold mt-1">{{ $totalProducts }}</h4>
              </div>
          </div>
      </div>
      <div class="col-md-3">
          <div class="card dashboard-metric bg-soft-green">
              <div class="card-body">
                  <div class="icon mb-2">
                      <i class="bi bi-cart-check fs-3 text-success"></i>
                  </div>
                  <small class="text-muted">Total Orders</small>
                  <h4 class="fw-bold mt-1">{{ $totalOrders }}</h4>
              </div>
          </div>
      </div>
      <div class="col-md-3">
          <div class="card dashboard-metric bg-soft-yellow">
              <div class="card-body">
                  <div class="icon mb-2">
                      <i class="bi bi-people-fill fs-3 text-warning"></i>
                  </div>
                  <small class="text-muted">Total Farmers</small>
                  <h4 class="fw-bold mt-1">{{ $totalFarmers }}</h4>
              </div>
          </div>
      </div>
      <div class="col-md-3">
          <div class="card dashboard-metric bg-soft-pink">
              <div class="card-body">
                  <div class="icon mb-2">
                      <i class="bi bi-person-circle fs-3 text-danger"></i>
                  </div>
                  <small class="text-muted">Total Buyers</small>
                  <h4 class="fw-bold mt-1">{{ $totalBuyers }}</h4>
              </div>
          </div>
      </div>
  </div>



    {{-- Sales Trend Chart --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white fw-bold">
            Sales Analytics (Last 30 Days)
        </div>
        <div class="card-body">
            <canvas id="salesChart" height="100"></canvas>
        </div>
    </div>

    {{-- Top Selling Products --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white fw-bold">
            Top Selling Products
        </div>
        <div class="card-body table-responsive">
            <table class="table table-bordered align-middle text-center">
                <thead class="table-light">
                    <tr>
                        <th>Product</th>
                        <th>Total Quantity Sold</th>
                        <th>Total Revenue (₱)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($topProducts as $item)
                        <tr>
                            <td>{{ $item->name }}</td>
                            <td>{{ $item->total_quantity_sold }}</td>
                            <td>₱{{ number_format($item->total_revenue, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3">No data available.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Recent Orders --}}
    <div class="card shadow-sm">
        <div class="card-header bg-white fw-bold">
            Recent Orders
        </div>
        <div class="card-body table-responsive">
            <table class="table table-bordered align-middle text-center">
                <thead class="table-light">
                    <tr>
                        <th>Order ID</th>
                        <th>Product</th>
                        <th>Buyer</th>
                        <th>Total Price</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($recentOrders as $order)
                        <tr>
                            <td>{{ $order->id }}</td>
                            <td>{{ $order->product->name ?? '-' }}</td>
                            <td>{{ $order->buyer->name ?? '-' }}</td>
                            <td>₱{{ number_format($order->total_price, 2) }}</td>
                            <td><span class="badge bg-success">{{ $order->status }}</span></td>
                            <td>{{ $order->created_at->format('Y-m-d') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6">No recent orders.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const salesData = @json($salesData);

    const chartCtx = document.getElementById('salesChart').getContext('2d');
    const chart = new Chart(chartCtx, {
        type: 'line',
        data: {
            labels: salesData.map(item => item.date),
            datasets: [{
                label: '₱ Sales',
                data: salesData.map(item => item.total),
                borderColor: 'rgba(75, 192, 192, 1)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                tension: 0.3,
                fill: true,
                pointRadius: 4,
                pointHoverRadius: 6
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    title: { display: true, text: 'Revenue (₱)' }
                },
                x: {
                    title: { display: true, text: 'Date' }
                }
            },
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: context => `₱${context.formattedValue}`
                    }
                }
            }
        }
    });
</script>
@endpush
