@extends('layouts.admin')

@section('title', 'Products Management')

@section('content')
<div class="container py-4 admin-dark-skin">
    <h2 class="mb-4 fw-bold text-white">Products</h2>

    <!-- ✅ Filter/Search Form -->
    <form action="{{ route('admin.products.index') }}" method="GET" class="row align-items-end g-3 mb-4">
        <div class="col-md-4">
            <label class="form-label">Search by product name</label>
            <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search...">
        </div>

        <div class="col-md-3">
            <label class="form-label">Farmer</label>
            <select name="farmer" class="form-select">
                <option value="">All Farmers</option>
                @foreach ($farmers as $id => $name)
                    <option value="{{ $id }}" {{ request('farmer') == $id ? 'selected' : '' }}>{{ $name }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-3">
            <label class="form-label">Stock Status</label>
            <select name="stock_status" class="form-select">
                <option value="">All Stock Status</option>
                <option value="in_stock" {{ request('stock_status') == 'in_stock' ? 'selected' : '' }}>In Stock</option>
                <option value="low_stock" {{ request('stock_status') == 'low_stock' ? 'selected' : '' }}>Low Stock</option>
                <option value="out_of_stock" {{ request('stock_status') == 'out_of_stock' ? 'selected' : '' }}>Out of Stock</option>
            </select>
        </div>

        <div class="col-md-2 d-flex flex-column gap-2">
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-utility btn-search">Search</button>
                <a href="{{ route('admin.products.index') }}" class="btn btn-utility btn-reset">Reset</a>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.products.export') }}" class="btn btn-utility btn-export">
                    <i class="bi bi-download me-2"></i> Export
                </a>
                <a href="{{ route('admin.products.create') }}" class="btn btn-utility btn-add-product">
                    + Add Product
                </a>
            </div>
        </div>
    </form>
</div>

<!-- ✅ Product Table -->
<div class="container pb-4">
    <div class="card">
        <div class="card-body p-0">
            <table class="table table-hover table-borderless custom-product-table align-middle table-rounded">
                <thead class="table-light">
                    <tr>
                        <th>Product</th>
                        <th>Stock</th>
                        <th>Price</th>
                        <th>Farmer</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($products as $product)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="{{ asset('storage/' . $product->image) }}" class="product-thumb me-3" alt="Image">
                                    <div>
                                        <strong>{{ $product->name }}</strong><br>
                                        <span class="text-muted small">{{ Str::limit($product->description, 40) }}</span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if ($product->stock <= 5)
                                    <span class="badge bg-danger">Low stock ({{ $product->stock }})</span>
                                @elseif ($product->stock <= 20)
                                    <span class="badge bg-warning text-dark">Limited ({{ $product->stock }})</span>
                                @else
                                    <span class="badge bg-success">In stock</span>
                                @endif
                            </td>
                            <td>₱{{ number_format($product->price, 2) }}</td>
                            <td>
                                <span class="badge bg-info text-dark">{{ $product->user->name ?? 'N/A' }}</span>
                            </td>
                            <td>
                                <span class="badge 
                                    {{ $product->status === 'approved' ? 'bg-success' : 
                                        ($product->status === 'rejected' ? 'bg-danger' : 'bg-secondary') }}">
                                    {{ ucfirst($product->status) }}
                                </span>
                            </td>
                            <td class="d-flex gap-1 align-items-center">
                                @if ($product->status === 'pending')
                                    <form action="{{ route('admin.products.approve', $product->id) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button class="btn btn-sm btn-success">Approve</button>
                                    </form>
                                    <form action="{{ route('admin.products.reject', $product->id) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button class="btn btn-sm btn-secondary">Reject</button>
                                    </form>
                                @else
                                    <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this product?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">No products available.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <!-- ✅ Pagination -->
            <div class="mt-3 px-3">
                {{ $products->appends(request()->query())->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>
@endsection
