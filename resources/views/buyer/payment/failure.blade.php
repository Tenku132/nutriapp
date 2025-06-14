@extends('layouts.app')

@section('content')
<div class="container text-center py-5">
    <h2 class="text-danger">Payment Failed</h2>
    <p class="lead">There was a problem processing your payment. Please try again.</p>
    <a href="{{ route('buyer.cart.index') }}" class="btn btn-outline-danger mt-3">Return to Cart</a>
</div>
@endsection
