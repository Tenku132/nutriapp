<aside class="d-flex flex-column justify-content-between bg-white shadow-sm p-3" style="width: 240px; min-height: 100%; border-right: 1px solid #ccc;">

    
    {{-- Logo --}}
    <div class="text-center mb-4">
        <img src="{{ asset('img/nutriteam-logo.png') }}" alt="Nutri Team Logo" class="img-fluid" style="max-height: 100px;">
    </div>

    {{-- Optional Sidebar Search --}}
    {{-- <form method="GET" action="#" class="mb-3 d-none d-md-block">
        <input type="text" name="sidebar_search" class="form-control form-control-sm" placeholder="Search...">
    </form> --}}

    {{-- Nav Links --}}
    <ul class="nav nav-pills flex-column mb-auto">
        <li class="nav-item">
            <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : 'text-dark' }}">
                <i class="bi bi-speedometer2 me-2"></i> Dashboard
            </a>
        </li>
        <li>
            <a href="{{ route('admin.products.index') }}" class="nav-link {{ request()->routeIs('admin.products.*') ? 'active' : 'text-dark' }}">
                <i class="bi bi-boxes me-2"></i> Products
            </a>
        </li>
        <li>
            <a href="{{ route('admin.orders.index') }}" class="nav-link {{ request()->routeIs('admin.orders.*') ? 'active' : 'text-dark' }}">
                <i class="bi bi-bag-check me-2"></i> Orders
            </a>
        </li>
        <li>
            <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : 'text-dark' }}">
                <i class="bi bi-people me-2"></i> Users
            </a>
        </li>
        <li>
            <a href="{{ route('admin.reviews.index') }}" class="nav-link {{ request()->routeIs('admin.reviews.index') ? 'active' : 'text-dark' }}">
                <i class="bi bi-star me-2"></i> Reviews
            </a>
        </li>
        <li>
            <a href="{{ route('admin.reports.index') }}" class="nav-link {{ request()->routeIs('admin.reports.index') ? 'active' : 'text-dark' }}">
                <i class="bi bi-bar-chart-line me-2"></i> Reports
            </a>
        </li>
        <li>
            <a href="{{ route('admin.audit.logs') }}" class="nav-link {{ request()->routeIs('admin.audit.logs') ? 'active' : 'text-dark' }}">
                <i class="bi bi-clipboard-data me-2"></i> Audit Logs
            </a>
        </li>
        <li>
            <a href="{{ route('admin.activity.logs') }}" class="nav-link {{ request()->routeIs('admin.activity.logs') ? 'active' : 'text-dark' }}">
                <i class="bi bi-clock-history me-2"></i> Activity Logs
            </a>
        </li>
    </ul>

    {{-- Admin Footer Profile with Dropdown --}}
<div class="mt-auto pt-4 border-top text-center position-relative">
    <div class="dropdown">
        <a href="#" class="d-inline-flex align-items-center text-decoration-none dropdown-toggle" id="adminDropdown" data-bs-toggle="dropdown" aria-expanded="false">
            @php
                $admin = Auth::guard('admin')->user();
                $profileImage = $admin?->profile_picture;
            @endphp

            <img src="{{ $profileImage
                ? asset('storage/' . $profileImage)
                : 'https://ui-avatars.com/api/?name=' . urlencode($admin?->name ?? 'Admin') }}"
                alt="Admin Avatar"
                class="rounded-circle me-2"
                width="40"
                height="40">

            <div class="text-start">
                <p class="mb-0 fw-semibold text-dark">{{ $admin?->name ?? 'Admin' }}</p>
                <small class="text-muted">{{ $admin?->email ?? 'admin@example.com' }}</small>
            </div>
        </a>

        <ul class="dropdown-menu dropdown-menu-end shadow-sm mt-2" aria-labelledby="adminDropdown">
            <li>
                <a class="dropdown-item" href="{{ route('admin.profile.edit') }}">
                    <i class="bi bi-person-circle me-2"></i> Profile
                </a>
            </li>
            <li>
                <a class="dropdown-item" href="{{ route('admin.password.change') }}">
                    <i class="bi bi-key me-2"></i> Change Password
                </a>
            </li>
            <li><hr class="dropdown-divider"></li>
            <li>
                <form action="{{ route('admin.logout') }}" method="POST" class="d-inline">
                    @csrf
                    <button class="dropdown-item text-danger" type="submit">
                        <i class="bi bi-box-arrow-right me-2"></i> Logout
                    </button>
                </form>
            </li>
        </ul>
    </div>
</div>

</aside>
