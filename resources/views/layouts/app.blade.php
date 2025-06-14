<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'Laravel'))</title>

    <!-- Fonts -->
    <!-- Google Font: Inter (Variable Font) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0..1,14..32,100..900&display=swap" rel="stylesheet">

    {{-- <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" /> --}}


    <!-- Bootstrap 5 CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">

    @livewireStyles
</head>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const notifButton = document.getElementById('notifDropdown');
        const notifDropdown = document.querySelector('#notifDropdown + .dropdown-menu');

        notifButton?.addEventListener('click', function () {
            if (notifDropdown.dataset.marked === 'true') return;

            fetch("{{ route('buyer.notifications.markAsReadAjax') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            }).then(response => {
                if (response.ok) {
                    notifDropdown.dataset.marked = 'true';
                    const badge = notifButton.querySelector('.badge');
                    if (badge) badge.remove();
                }
            });
        });
    });
</script>

<body class="font-sans antialiased">
    <x-banner />

    @if (Auth::check())
        @php
            $role = Auth::user()->role;
            $messageRoute = $role === 'buyer' ? route('buyer.messages.inbox') : route('farmer.messages.inbox');
        @endphp

        @if ($role === 'buyer')
        <nav class="bg-success bg-opacity-25 shadow-sm py-2">
            <div class="container d-flex justify-content-between align-items-center">
                <div class="fw-bold text-success fs-4">NUTRI APP</div>
                <ul class="nav d-none d-md-flex gap-4">
                    <li class="nav-item"><a href="{{ route('home') }}" class="nav-link text-success fw-semibold">Home</a></li>
                    <li class="nav-item"><a href="{{ route('buyer.products.index') }}" class="nav-link text-success fw-semibold">Shop</a></li>
<li class="nav-item">
    <a href="{{ url('/#about-us') }}" class="nav-link text-success fw-semibold">About Us</a>
</li>
                    <li class="nav-item">
    <a href="{{ url('/#contact-us') }}" class="nav-link text-success fw-semibold">Contact Us</a>
</li>
                </ul>
                <div class="d-flex align-items-center gap-3">
                    @php
    $notifications = Auth::user()->unreadNotifications->filter(function ($notif) {
        return ($notif->data['type'] ?? '') !== 'farmer_order';
    });
@endphp
                    <div class="dropdown position-relative">
                        <button class="btn position-relative" id="notifDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-bell fs-5 text-dark"></i>
                            @if($notifications->count())
    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
        {{ $notifications->count() }}
    </span>
@endif
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="notifDropdown" style="min-width: 320px;">
                            <li class="dropdown-header fw-semibold text-center">
                                {{ $notifications->count() }} New Notification
                            </li>
                            @forelse($notifications as $notification)
                                <li class="dropdown-item small text-wrap">
                                    <strong>{{ $notification->data['title'] ?? 'Rating Notification' }}</strong><br>
                                    <span>{!! $notification->data['message'] ?? 'No content' !!}</span><br>

                                    <span class="text-muted small">{{ $notification->created_at->diffForHumans() }}</span>
                                </li>
                            @empty
                                <li class="dropdown-item text-center text-muted small">No new notifications</li>
                            @endforelse
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-center fw-bold" href="{{ route('buyer.notifications.index') }}">See All Notifications</a></li>
                        </ul>
                    </div>

                    <a href="{{ $messageRoute }}" title="Messages"><i class="bi bi-chat-dots fs-5 text-dark"></i></a>
                    <a href="{{ route('buyer.cart.index') }}" title="Cart"><i class="bi bi-cart fs-5 text-dark"></i></a>

                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-success dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            {{ Auth::user()->name }}
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <li><a class="dropdown-item" href="{{ route('buyer.profile.show') }}">Profile</a></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item">Logout</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>

        @elseif ($role === 'farmer')
        <nav class="bg-success bg-opacity-25 shadow-sm py-2">
            <div class="container d-flex justify-content-between align-items-center">
                <div class="fw-bold text-success fs-4">NUTRI APP</div>
                <div class="d-none d-md-flex gap-4">
                    <a href="{{ route('home') }}" class="text-success text-decoration-none fw-semibold">Home</a>
                    <a href="{{ route('farmer.dashboard') }}" class="text-success text-decoration-none fw-semibold">My Products</a>
                    <a href="{{ route('farmer.orders.index') }}" class="text-success text-decoration-none fw-semibold">Orders</a>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <a href="{{ $messageRoute }}" title="Messages"><i class="bi bi-chat-dots fs-5 text-dark"></i></a>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-success dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            {{ Auth::user()->name }}
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <li><a class="dropdown-item" href="{{ route('farmer.dashboard') }}">Dashboard</a></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item">Logout</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>
        @endif
    @endif

    <div class="min-h-screen bg-gray-100">
        <main>
            {{ $slot ?? '' }}
            @yield('content')
        </main>
    </div>

    @stack('scripts')
    @livewireScripts

    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
