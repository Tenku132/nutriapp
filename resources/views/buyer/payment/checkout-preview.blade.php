@extends('layouts.app')

@section('title', 'Checkout Preview')

@section('content')
<div class="container mt-5">
    <h2 class="mb-4">Checkout Preview</h2>

    @if(session('checkout_items'))
        <div class="card p-4 shadow-sm">
            <table class="table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Qty</th>
                        <th>Price</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach(session('checkout_items') as $item)
                        <tr>
                            <td>{{ $item['name'] }}</td>
                            <td>{{ $item['quantity'] }}</td>
                            <td>₱{{ number_format($item['price'], 2) }}</td>
                            <td>₱{{ number_format($item['total'], 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="d-flex justify-content-end mt-3">
                <h5>Total: <strong>₱{{ number_format($totalAmount, 2) }}</strong></h5>
            </div>

            <form method="POST" action="{{ route('buyer.checkout.createSession') }}" class="mt-4">
                @csrf
                <input type="hidden" name="name" value="{{ Auth::user()->name }}">
                <input type="hidden" name="email" value="{{ Auth::user()->email }}">
                <button type="submit" class="btn btn-success">Proceed to PayMongo (Mock)</button>
            </form>
        </div>
    @else
        <p>No items selected for checkout.</p>
    @endif
</div>
@endsection
