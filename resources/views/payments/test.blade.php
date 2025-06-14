@extends('layouts.app')

@section('content')
<div class="container py-5">
    <h3 class="mb-4">🧪 PayMongo Test Payment</h3>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">{{ implode(', ', $errors->all()) }}</div>
    @endif

    <form action="{{ route('buyer.payment.testCard.process') }}" method="POST">


        @csrf

        <div class="row g-4">
            <div class="col-md-6">
                <label>💰 Amount (PHP)</label>
                <input type="number" name="amount" class="form-control" value="100" required>

                <label class="mt-3">💳 Card Number</label>
                <input type="text" name="card_number" class="form-control" value="4343434343434345" required>

                <label class="mt-3">📅 Expiration Month</label>
                <input type="text" name="exp_month" class="form-control" value="12" required>

                <label class="mt-3">📅 Expiration Year</label>
                <input type="text" name="exp_year" class="form-control" value="" required>

                <label class="mt-3">🔒 CVC</label>
                <input type="text" name="cvc" class="form-control" value="123" required>

                <button class="btn btn-primary mt-4 w-100" type="submit">
                    Test Payment
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
