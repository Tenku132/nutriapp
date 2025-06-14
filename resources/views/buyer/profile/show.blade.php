<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Profile</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>

<!-- Header -->
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
            <div class="dropdown">
                <a class="dropdown-toggle text-dark text-decoration-none" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    {{ Auth::user()->name }}
                </a>
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

<!-- Profile Content -->
<div class="container py-4">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 mb-4">
            <div class="text-center bg-white p-3 rounded shadow-sm">
                @php
                    $initial = strtoupper(substr(Auth::user()->name, 0, 1));
                @endphp

                @if (Auth::user()->profile_photo_path)
                    <img src="{{ asset('storage/' . Auth::user()->profile_photo_path) }}"
                         class="rounded-circle mb-2"
                         width="100"
                         height="100"
                         alt="User Photo">
                @else
                    <div class="rounded-circle bg-secondary text-white d-flex justify-content-center align-items-center mb-2"
                         style="width:100px; height:100px; font-size:32px;">
                        {{ $initial }}
                    </div>
                @endif

                <h6 class="fw-bold mt-2 mb-3"><i class="bi bi-person-circle me-1"></i> My Account</h6>

                <ul class="nav flex-column gap-2 text-start">
                    <li class="nav-item">
                        <a href="{{ route('buyer.profile.show') }}"
                           class="nav-link {{ request()->is('buyer/profile') ? 'fw-bold text-primary' : 'text-dark' }}">
                            Profile
                        </a>
                    </li>
                    <li class="nav-item"><a href="#" class="nav-link text-dark">Payments</a></li>
<li class="list-group-item">
    <a href="{{ route('buyer.profile.address') }}" class="text-decoration-none text-dark d-block w-100">
        Address
    </a>
</li>
<li class="list-group-item">
    <a href="#" class="text-decoration-none text-dark">Change Password</a> {{-- we will update href later --}}
</li>
                    <li class="nav-item">
                        <a href="{{ route('buyer.orders.history') }}"
                           class="nav-link {{ request()->is('buyer/orders/history') ? 'fw-bold text-success' : 'text-dark' }}">
                            <i class="bi bi-clipboard-check me-1"></i> My Purchase
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Profile Form -->
        <div class="col-md-9">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <strong>My Profile</strong>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    <form method="POST" action="{{ route('buyer.profile.update') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="text" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}" readonly>
                        </div>


                        <div class="mb-3">
                            <label for="address" class="form-label">Address</label>
                            <input type="text" name="address" class="form-control" value="{{ old('address', $user->address) }}">
                        </div>

                        <button type="submit" class="btn btn-success">Save Changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
