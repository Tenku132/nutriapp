@extends('layouts.app')

@section('content')
<div class="container text-center py-5">
    <h2 class="text-success">Payment Successful!</h2>
    <p class="lead">Thank you for your purchase. Your payment has been confirmed.</p>
    <a href="{{ route('buyer.orders.history') }}" class="btn btn-outline-success mt-3">View My Orders</a>
</div>
@endsection
