<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'Laravel'))</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
</head>
</head>
<body>

    <!-- Header/Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-success-subtle px-4">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold text-success" href="{{ route('home') }}">NUTRI APP</a>

            <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
                <ul class="navbar-nav mx-auto text-center">
                    <li class="nav-item mx-2"><a class="nav-link text-success" href="{{ route('home') }}">Home</a></li>
                    <li class="nav-item mx-2"><a class="nav-link text-success" href="{{ route('buyer.products.index') }}">Shop</a></li>
                    <li class="nav-item mx-2"><a class="nav-link text-success" href="#">About Us</a></li>
                    <li class="nav-item mx-2"><a class="nav-link text-success" href="#">Contact Us</a></li>
                </ul>
            </div>

            <div class="d-flex align-items-center">
                <i class="bi bi-bell fs-5 me-3 text-dark"></i>
                <a href="{{ route('messages.inbox') }}" class="text-dark me-3"><i class="bi bi-chat-dots fs-5"></i></a>
                <a href="{{ route('buyer.cart.index') }}" class="text-dark me-3"><i class="bi bi-cart fs-5"></i></a>

                @auth
                <div class="dropdown">
                    <a class="dropdown-toggle text-dark text-decoration-none" href="#" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        {{ Auth::user()->name }}
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item">Logout</button>
                            </form>
                        </li>
                    </ul>
                </div>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Page Content -->
    <main class="container my-4">
        {{ $slot }}
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
