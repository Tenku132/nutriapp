@extends('layouts.app')

@section('title', 'Notifications')

@section('content')
<div class="container py-4">
    <h4 class="text-success fw-bold mb-4">Your Notifications</h4>

    {{-- Success message --}}
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Mark All as Read --}}
    @if (Auth::user()->unreadNotifications->count())
        <form action="{{ route('buyer.notifications.markAll') }}" method="POST" class="mb-3">
            @csrf
            <button type="submit" class="btn btn-sm btn-outline-success">
                Mark All as Read
            </button>
        </form>
    @endif

    {{-- Notification List --}}
    @if ($notifications->isEmpty())
        <div class="alert alert-secondary text-center">You have no notifications.</div>
    @else
        <ul class="list-group">
            @foreach ($notifications as $notification)
                <li class="list-group-item d-flex justify-content-between align-items-start">
                    <div class="ms-2 me-auto">
                        {!! $notification->data['message'] ?? 'Notification' !!}
                        <div class="text-muted small">{{ $notification->created_at->diffForHumans() }}</div>
                    </div>
                    @if (is_null($notification->read_at))
                        <span class="badge bg-success rounded-pill">New</span>
                    @endif
                </li>
            @endforeach
        </ul>

        <div class="mt-4">
            {{ $notifications->links() }}
        </div>
    @endif
</div>
@endsection
