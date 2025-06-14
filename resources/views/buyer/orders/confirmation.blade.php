@extends('layouts.app')

@section('content')
<div class="container text-center py-5">
    <h2 class="text-success mb-4">🎉 Order Complete!</h2>
    <p class="lead">Thank you for your purchase. Your order has been placed successfully.</p>
    <a href="{{ route('buyer.orders.history') }}" class="btn btn-success mt-3">View My Orders</a>
</div>
@endsection
