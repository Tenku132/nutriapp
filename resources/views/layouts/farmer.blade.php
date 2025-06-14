<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Farmer Dashboard')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap + Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Custom Styles -->
    <link href="{{ asset('css/custom-farmer.css') }}" rel="stylesheet">
    @stack('styles')

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa;
            overflow-x: hidden;
        }

        .sidebar {
            width: 220px;
            min-height: 100vh;
            background-color: #12372A;
            color: #ffffff;
            position: fixed;
        }

        .sidebar .logo {
            padding: 20px 10px;
            text-align: center;
        }

        .sidebar .logo img {
            width: 50px;
            margin-bottom: 5px;
        }

        .sidebar .nav-link {
            color: #ffffff;
            padding: 12px 20px;
            font-weight: 500;
        }

        .sidebar .nav-link.active,
        .sidebar .nav-link:hover {
            background-color: #17631d;
            color: #fff;
        }

        main {
            margin-left: 220px;
            padding: 30px;
        }

        .card {
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        }

        footer {
            font-size: 0.85rem;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="d-flex">
        <!-- Sidebar -->
        <aside class="sidebar d-flex flex-column">
<div class="text-center py-3">
  <img src="{{ asset('img/nutriteam-logo.png') }}" alt="Nutri Logo" style="max-width: 120px; height: auto;">
  <div class="fw-bold text-white mt-2">Nutri Team</div>
</div>
            <nav class="nav flex-column">
                <a class="nav-link {{ request()->routeIs('farmer.dashboard') ? 'active' : '' }}" href="{{ route('farmer.dashboard') }}">
                    <i class="bi bi-speedometer2 me-2"></i> Dashboard
                </a>
                <a class="nav-link {{ request()->is('farmer/profile') ? 'active' : '' }}" href="{{ route('farmer.profile.show') }}">

                    <i class="bi bi-person me-2"></i> Profile
                </a>
                <a class="nav-link {{ request()->is('farmer/products*') ? 'active' : '' }}" href="{{ route('farmer.products.index') }}">
                    <i class="bi bi-box-seam me-2"></i> Products
                </a>
                <a class="nav-link {{ request()->is('farmer/orders*') ? 'active' : '' }}" href="{{ route('farmer.orders.index') }}">
                    <i class="bi bi-cart me-2"></i> Orders
                </a>


                <a class="nav-link {{ request()->is('farmer/messages*') ? 'active' : '' }}" href="{{ route('farmer.messages.inbox') }}">
                    <i class="bi bi-envelope-fill"></i> Messages
                </a>
                @php
    $unreadCount = auth()->user()->unreadNotifications->count();
@endphp
<a class="nav-link {{ request()->is('farmer/notifications*') ? 'active' : '' }}" href="{{ route('farmer.notifications.index') }}">
    <i class="bi bi-bell me-2 position-relative">
        @if($unreadCount > 0)
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                {{ $unreadCount }}
            </span>
        @endif
    </i>
    Notifications
</a>
                <a class="nav-link {{ request()->routeIs('farmer.settings') ? 'active' : '' }}" href="{{ route('farmer.settings') }}">
    <i class="bi bi-gear me-2"></i> Settings
</a>

                <a class="nav-link" href="#"><i class="bi bi-question-circle me-2"></i> Help</a>
                <form method="POST" action="{{ route('logout') }}" class="mt-auto">
                    @csrf
                    <button type="submit" class="btn btn-outline-light w-100 rounded-0 mt-3">
                        <i class="bi bi-box-arrow-right me-2"></i> Logout
                    </button>
                </form>
            </nav>
        </aside>

        <!-- Main Content -->
        <div class="flex-grow-1">
            <main>
                @if(session('success'))
  <div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
@endif
                @yield('content')
            </main>
        </div>
    </div>

    <!-- JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
