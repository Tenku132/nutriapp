@extends('layouts.app') {{-- or change to layouts.admin if needed --}}

@section('title', 'PayMongo Payments')

@section('content')
<div class="container mt-5">
    <h2 class="mb-4">PayMongo Transactions</h2>

    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Intent ID</th>
                <th>Method ID</th>
                <th>Amount</th>
                <th>Status</th>
                <th>User ID</th>
                <th>Created</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($payments as $payment)
                <tr>
                    <td>{{ $payment->id }}</td>
                    <td>{{ $payment->payment_intent_id }}</td>
                    <td>{{ $payment->payment_method_id }}</td>
                    <td>₱{{ number_format($payment->amount / 100, 2) }}</td>
                    <td>
                        <span class="badge bg-success">{{ ucfirst($payment->status) }}</span>
                    </td>
                    <td>{{ $payment->user_id ?? '—' }}</td>
                    <td>{{ $payment->created_at->format('Y-m-d H:i') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">No payments found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
  