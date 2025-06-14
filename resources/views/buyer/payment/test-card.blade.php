@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h3>Test Card Payment</h3>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('buyer.payment.testCard.process') }}">
        @csrf

        <div class="mb-3">
            <label>Card Number</label>
            <input type="text" name="card_number" class="form-control" placeholder="4343434343434345" required>
        </div>

        <div class="mb-3">
            <label>Expiration Month</label>
            <input type="text" name="exp_month" class="form-control" placeholder="12" required>
        </div>

        <div class="mb-3">
            <label>Expiration Year</label>
            <input type="text" name="exp_year" class="form-control" placeholder="2026" required>
        </div>

        <div class="mb-3">
            <label>CVC</label>
            <input type="text" name="cvc" class="form-control" placeholder="123" required>
        </div>

        <button type="submit" class="btn btn-primary">Simulate Payment</button>
    </form>
</div>
@endsection
