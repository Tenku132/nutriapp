@extends('layouts.farmer')

@section('title', 'Edit Product')

@section('content')
  <h2 class="fw-bold text-success mb-4">Edit Product</h2>
  <form action="{{ route('farmer.products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    @include('farmer.products._form', ['product' => $product])
    <button type="submit" class="btn btn-success mt-3">Update Product</button>
  </form>
@endsection
