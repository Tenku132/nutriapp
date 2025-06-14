@extends('layouts.farmer')

@section('title', 'Messages')

@push('styles')
<style>
    .chat-wrapper {
        display: flex;
        height: calc(100vh - 80px);
        background-color: #f9fff9;
        border-radius: 6px;
        overflow: hidden;
    }

    .conversation-sidebar {
        width: 320px;
        background-color: #fff;
        border-right: 1px solid #ccc;
        overflow-y: auto;
        padding: 1rem;
    }

    .conversation-sidebar h5 {
        font-weight: bold;
        margin-bottom: 1rem;
    }

    .conversation-preview {
        padding: 0.75rem;
        border-radius: 8px;
        margin-bottom: 0.5rem;
        background: #e7fce7;
        cursor: pointer;
    }

    .conversation-preview:hover {
        background: #d6f5d6;
    }

    .conversation-preview.active {
        background: #c1f0c1;
        font-weight: bold;
    }

    .chat-area {
        flex-grow: 1;
        display: flex;
        flex-direction: column;
        padding: 1.5rem;
        overflow-y: auto;
    }

    .sent, .received {
        display: flex;
        margin-bottom: 10px;
    }

    .sent {
        justify-content: flex-end;
    }

    .received {
        justify-content: flex-start;
    }

    .message-bubble {
        padding: 10px 16px;
        border-radius: 16px;
        max-width: 60%;
        font-weight: 500;
        word-wrap: break-word;
        box-shadow: 0 1px 2px rgba(0,0,0,0.1);
    }

    .sent .message-bubble {
        background-color: #c2f0c2;
        color: #000;
    }

    .received .message-bubble {
        background-color: #eaeaea;
        color: #333;
    }

    .timestamp {
        font-size: 0.75rem;
        color: #555;
        text-align: right;
        margin-top: 5px;
    }

    .send-form {
        border-top: 1px solid #ccc;
        padding-top: 1rem;
        margin-top: auto;
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-0">
    <div class="chat-wrapper">
        <!-- Sidebar -->
        <div class="conversation-sidebar">
            <h5>MESSAGES</h5>
            @foreach ($conversations ?? [] as $id => $msgs)
                @php
                    $sideUser = $msgs->first()->sender_id === auth()->id()
                        ? $msgs->first()->receiver
                        : $msgs->first()->sender;
                @endphp
                <a href="{{ route('farmer.messages.show', $sideUser->id) }}" class="text-decoration-none text-dark">
                    <div class="conversation-preview {{ $sideUser->id == $userId ? 'active' : '' }}">
                        <strong>{{ strtoupper($sideUser->name) }}</strong><br>
                        <small class="text-muted">{{ Str::limit($msgs->last()->message, 30) }}</small>
                    </div>
                </a>
            @endforeach
        </div>

        <!-- Chat area -->
        <div class="chat-area">
            @php
                $receiver = $messages->first()->sender_id === auth()->id()
                    ? $messages->first()->receiver
                    : $messages->first()->sender;
            @endphp

            <div class="fw-bold mb-3">Conversation with {{ $receiver->name }}</div>

            @foreach ($messages as $message)
                <div class="{{ $message->sender_id === auth()->id() ? 'sent' : 'received' }}">
                    <div class="message-bubble">
                        {{ $message->message }}
                        <div class="timestamp">{{ $message->created_at->format('g:i A') }}</div>
                    </div>
                </div>
            @endforeach

            <!-- Send form -->
            <form action="{{ route('farmer.messages.reply', $userId) }}" method="POST" class="d-flex gap-2 send-form">
                @csrf
                <input type="text" name="message" class="form-control" placeholder="Type your message..." required>
                <button type="submit" class="btn btn-success">Send</button>
            </form>
        </div>
    </div>
</div>
@endsection
