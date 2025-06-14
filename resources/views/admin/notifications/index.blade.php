@extends('layouts.admin')

@section('title', 'All Notifications')

@section('content')
<div class="container">
    <h3 class="fw-bold mb-4">Notification Center</h3>

    @if($notifications->isEmpty())
        <div class="alert alert-info">No notifications available.</div>
    @else
        <ul class="list-group mb-3">
            @foreach ($notifications as $notification)
                <li class="list-group-item d-flex justify-content-between align-items-start {{ is_null($notification->read_at) ? 'list-group-item-warning' : '' }}">
                    <div class="ms-2 me-auto">
                        <div class="fw-bold">
                            <i class="bi {{ $notification->data['icon'] ?? 'bi-info-circle' }}"></i>
                            {{ $notification->data['message'] }}
                        </div>
                        <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                    </div>
                    @if (is_null($notification->read_at))
                        <form method="POST" action="{{ route('admin.notifications.markAsRead', $notification->id) }}">
                            @csrf
                            <button class="btn btn-sm btn-outline-success">Mark as Read</button>
                        </form>
                    @endif
                </li>
            @endforeach
        </ul>

        <div class="d-flex justify-content-center">
            {{ $notifications->links() }}
        </div>
    @endif
</div>
@endsection
