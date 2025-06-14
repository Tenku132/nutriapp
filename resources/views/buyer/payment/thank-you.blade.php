@extends('layouts.app')

@section('title', 'Thank You')

@section('content')
<div class="container mt-5 text-center">
    <h2 class="mb-3 text-success">✅ Payment Successful!</h2>
    <p>Thank you for your order.</p>
    <p>Your reference ID: <strong>{{ $reference }}</strong></p>

    <a href="{{ route('buyer.orders.history') }}" class="btn btn-primary mt-4">View My Orders</a>
</div>
@endsection
