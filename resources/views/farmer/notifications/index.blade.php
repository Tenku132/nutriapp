@extends('layouts.farmer')

@section('title', 'Notifications')

@section('content')
<div class="container-fluid">
    {{-- ✅ Header + Mark All --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold text-success mb-0">🔔 Notifications</h3>
        <form action="{{ route('farmer.notifications.markAll') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-sm btn-outline-success">Mark All as Read</button>
        </form>
    </div>

    {{-- ✅ Filter Tabs --}}
    <div class="mb-4">
        <a href="{{ route('farmer.notifications.index') }}" class="btn btn-sm {{ request('filter') == null ? 'btn-success' : 'btn-outline-success' }}">All</a>
        <a href="{{ route('farmer.notifications.index', ['filter' => 'message']) }}" class="btn btn-sm {{ request('filter') == 'message' ? 'btn-primary' : 'btn-outline-primary' }}">Messages</a>
        <a href="{{ route('farmer.notifications.index', ['filter' => 'order']) }}" class="btn btn-sm {{ request('filter') == 'order' ? 'btn-success' : 'btn-outline-success' }}">Orders</a>
    </div>

    {{-- ✅ Notification List --}}
    @forelse ($notifications as $notification)
        @php
            $type = $notification->data['type'] ?? 'default';
            $link = $notification->data['link'] ?? '#';
        @endphp

        <a href="{{ $link }}" class="text-decoration-none text-dark">
            <div class="alert alert-light border mb-3 d-flex align-items-start">
                <div class="me-3">
                    @switch($type)
                        @case('message')
                            <i class="bi bi-chat-dots-fill text-primary fs-4"></i>
                            @break
                        @case('order')
                            <i class="bi bi-box-seam text-success fs-4"></i>
                            @break
                        @default
                            <i class="bi bi-bell-fill text-secondary fs-4"></i>
                    @endswitch
                </div>

                <div>
                    @if (isset($notification->data['message']))
                        <div>{!! $notification->data['message'] !!}</div>
                    @else
                        <strong>{{ $notification->data['title'] ?? 'Notification' }}</strong>
                        <div>{!! $notification->data['body'] ?? 'No content' !!}</div>
                    @endif
                    <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                </div>
            </div>
        </a>
    @empty
        <p class="text-muted">You have no notifications yet.</p>
    @endforelse

    {{-- ✅ Pagination --}}
    @if ($notifications->hasPages())
    <div class="mt-4 d-flex justify-content-center">
        {{ $notifications->withQueryString()->onEachSide(1)->links() }}

    </div>
    @endif

</div>
@endsection
