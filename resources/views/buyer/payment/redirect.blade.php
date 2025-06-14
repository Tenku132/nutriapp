@extends('layouts.app')

@section('content')
<div class="container text-center py-5">
    <h2>Proceed to Payment</h2>
    <p class="lead">Total Amount: <strong>₱{{ number_format($totalAmount, 2) }}</strong></p>
    <p>This is a mock payment gateway page. You can now proceed with payment simulation.</p>

    <div class="mt-4">
        <a href="{{ route('buyer.payment.success') }}" class="btn btn-success">Simulate Successful Payment</a>
        <a href="{{ route('buyer.payment.failure') }}" class="btn btn-danger ms-2">Simulate Failed Payment</a>
    </div>
</div>
@endsection