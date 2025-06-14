@extends('layouts.admin')

@section('title', 'Product Details')

@section('content')
<div class="container py-4">
    <h2 class="mb-4">Product: {{ $product->name }}</h2>

    <div class="card">
        <div class="card-body">
            <p><strong>ID:</strong> {{ $product->id }}</p>
            <p><strong>Name:</strong> {{ $product->name }}</p>
            <p><strong>Price:</strong> ₱{{ number_format($product->price, 2) }}</p>
            <p><strong>Stock:</strong> {{ $product->stock }}</p>
            <p><strong>Category:</strong> {{ $product->category ?? '—' }}</p>
            <p><strong>Farmer:</strong> {{ $product->user->name ?? 'Unknown' }}</p>
            <p><strong>Created:</strong> {{ $product->created_at->format('F d, Y - h:i A') }}</p>
        </div>
    </div>
</div>
@endsection
