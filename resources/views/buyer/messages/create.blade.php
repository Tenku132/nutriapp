@extends('layouts.app')


@section('content')
<div class="container py-5" style="max-width: 600px;">
    <h4 class="mb-4 text-success fw-bold">Start New Conversation</h4>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('buyer.messages.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="receiver_id" class="form-label">Select Recipient</label>
            <select name="receiver_id" id="receiver_id" class="form-select" required>
                <option value="">-- Choose Buyer --</option>
                @foreach ($users as $buyer)
                    <option value="{{ $buyer->id }}">{{ $buyer->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="message" class="form-label">Message</label>
            <textarea name="message" id="message" class="form-control" rows="4" placeholder="Type your message..." required></textarea>
        </div>

        <button type="submit" class="btn btn-success">Send Message</button>
    </form>
</div>
@endsection
