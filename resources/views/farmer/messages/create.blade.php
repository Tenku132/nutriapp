@extends('layouts.farmer')

@section('title', 'New Message')

@section('content')
<div class="container mt-4">
    <h4 class="text-success fw-bold mb-4">Start New Conversation</h4>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('farmer.messages.store') }}" method="POST">
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
            <textarea name="message" id="message" class="form-control" rows="4" required></textarea>
        </div>

        <button type="submit" class="btn btn-success">Send Message</button>
    </form>
</div>
@endsection
