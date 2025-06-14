@extends('layouts.app')

@section('content')
<style>
    .chat-wrapper {
        display: flex;
        height: 85vh;
        background-color: #f9fff9;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 0 8px rgba(0,0,0,0.05);
    }

    .sidebar {
        width: 280px;
        background: #fff;
        border-right: 1px solid #ccc;
        overflow-y: auto;
        padding: 1rem;
    }

    .sidebar h5 {
        font-weight: bold;
        margin-bottom: 1rem;
    }

    .sidebar .conversation-preview {
        padding: 0.75rem;
        border-radius: 8px;
        margin-bottom: 0.5rem;
        background: #f4f4f4;
        cursor: pointer;
        transition: background 0.3s;
    }

    .sidebar .conversation-preview:hover {
        background: #e0ffe0;
    }

    .chat-area {
        flex-grow: 1;
        padding: 1.5rem;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
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
        background-color: #d1f7d1;
        color: #1a1a1a;
        padding: 12px 16px;
        border-radius: 16px;
        max-width: 60%;
        word-wrap: break-word;
        position: relative;
        font-weight: 500;
        box-shadow: 0 1px 2px rgba(0,0,0,0.1);
    }

    .received .message-bubble {
        background-color: #eee;
        color: #333;
    }

    .timestamp {
        font-size: 0.75rem;
        color: #555;
        text-align: right;
        margin-top: 5px;
    }
</style>

<div class="container-fluid py-4">
    <div class="chat-wrapper">
        <!-- Sidebar with conversations -->
        <div class="sidebar">
            <h5>MESSAGES</h5>
            @foreach ($conversations ?? [] as $receiverId => $msgs)
                @php
                    $user = $msgs->first()->sender_id === auth()->id()
                        ? $msgs->first()->receiver
                        : $msgs->first()->sender;
                @endphp
                <a href="{{ route('buyer.messages.show', $user->id) }}" class="text-decoration-none text-dark">
                    <div class="conversation-preview">
                        <strong>{{ strtoupper($user->name) }}</strong><br>
                        <small class="text-muted">
                            {{ Str::limit($msgs->last()->message, 30) }}
                        </small>
                    </div>
                </a>
            @endforeach
        </div>

        <!-- Chat Conversation -->
        <div class="chat-area">
            @foreach ($messages as $message)
                <div class="{{ $message->sender_id === auth()->id() ? 'sent' : 'received' }}">
                    <div class="message-bubble">
                        {{ $message->message }}
                        <div class="timestamp">{{ $message->created_at->format('g:i A') }}</div>
                    </div>
                </div>
            @endforeach

            <!-- Reply Box -->
            <form action="{{ route('buyer.messages.reply', ['user' => $userId]) }}" method="POST" class="mt-auto d-flex align-items-center gap-2 pt-3 border-top">
                @csrf
                <input type="text" name="message" class="form-control shadow-sm" placeholder="Type your message..." required>
                <button type="submit" class="btn btn-success">Send</button>
            </form>
        </div>
    </div>
</div>
@endsection
