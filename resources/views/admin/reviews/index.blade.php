@extends('layouts.admin')

@section('title', 'Product Ratings')

@section('content')
<div class="container py-4">
    <h4 class="mb-4 text-white">Product Ratings & Feedback</h4>

    <div class="card p-3 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover custom-product-table align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Product</th>
                        <th>Buyer</th>
                        <th>Order Code</th>
                        <th>Rating</th>
                        <th>Comment</th>
                        <th>Rated On</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($ratings as $rating)
                        <tr>
                            <td>{{ $rating->product->name ?? '—' }}</td>
                            <td>{{ $rating->buyer->name ?? '—' }}</td>
                            <td>{{ $rating->order->order_code ?? '—' }}</td>
                            <td>{{ $rating->rating }} ⭐</td>
                            <td>{{ $rating->comment ?? '—' }}</td>
                            <td>{{ $rating->created_at->format('Y-m-d') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">No ratings found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-end mt-3">
            {{ $ratings->links() }}
        </div>
    </div>
</div>
@endsection
