@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="text-center">
        <h2 class="text-success mb-3">Review Your Order</h2>
        <p>Please confirm your payment to proceed.</p>

        <div class="card p-4 mx-auto" style="max-width: 500px;">
            <h5>Total Amount: <span class="text-success fw-bold">₱{{ number_format($totalAmount, 2) }}</span></h5>

            <p class="mt-3">Note: This is a draft payment page. Integration with PayMongo coming soon.</p>

            <a href="{{ route('buyer.payment.success') }}" class="btn btn-success mt-3">Simulate Payment Success</a>
            <a href="{{ route('buyer.payment.failure') }}" class="btn btn-danger mt-3">Simulate Payment Failure</a>
        </div>
    </div>
</div>
@endsection
