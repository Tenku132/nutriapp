@extends('layouts.app')

@section('content')
<div class="container-fluid d-flex" style="height: 90vh;">
    <!-- Sidebar -->
    <div class="bg-white border-end" style="width: 300px; overflow-y: auto;">
<div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom">
    <h5 class="text-success fw-bold mb-0">MESSAGES</h5>
    <a href="{{ route('buyer.messages.create') }}" class="btn btn-sm btn-success">+ New</a>
</div>
        @foreach ($conversations as $receiverId => $messages)
            @php
                $receiver = $messages->first()->sender_id === auth()->id()
                    ? $messages->first()->receiver
                    : $messages->first()->sender;
            @endphp
            <a href="{{ route('buyer.messages.show', $receiver->id) }}" class="text-decoration-none text-dark">
                <div class="px-3 py-2 border-bottom">
                    <div class="fw-bold">{{ $receiver->name }}</div>
                    <small class="text-muted">{{ Str::limit($messages->last()->message, 30) }}</small>
                </div>
            </a>
        @endforeach
    </div>

    <!-- Main Chat Area -->
    <div class="flex-fill d-flex justify-content-center align-items-center text-muted">
        <p>Select a conversation to view messages</p>
    </div>
</div>
@endsection
