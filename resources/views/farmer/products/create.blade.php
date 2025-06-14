@extends('layouts.farmer')

@section('title', 'Add Product')

@section('content')
  <h2 class="fw-bold text-success mb-4">Add New Product</h2>
  <form action="{{ route('farmer.products.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    @include('farmer.products._form', ['product' => null])
    <button type="submit" class="btn btn-success mt-3">Save Product</button>
  </form>
@endsection
