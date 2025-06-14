@extends('layouts.admin')

@section('title', 'User Details')

@section('content')
<div class="admin-user-profile container my-5 text-white"> {{-- ✅ Makes content readable --}}

    <h2 class="mb-4">User Details</h2>

    <div class="card p-4 shadow-sm bg-white text-dark"> {{-- ✅ Card remains readable and contrasting --}}
        <div class="row g-4 align-items-center">
            <div class="col-md-3 text-center">
                <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=0D8ABC&color=fff&size=128"
                     class="admin-avatar-img rounded-circle shadow-sm mb-2" alt="Avatar">
                <h5 class="mt-2 mb-0">{{ $user->name }}</h5>
                <small class="text-muted">Joined on {{ $user->created_at->format('F Y') }}</small>
            </div>

            <div class="col-md-9">
                <dl class="row mb-0">
                    <dt class="col-sm-3"><i class="bi bi-person-circle me-1"></i>Role</dt>
                    <dd class="col-sm-9">
                        <span class="role-badge role-{{ strtolower($user->role) }}">{{ ucfirst($user->role) }}</span>
                    </dd>

                    <dt class="col-sm-3"><i class="bi bi-envelope me-1"></i>Email</dt>
                    <dd class="col-sm-9">{{ $user->email }}</dd>

                    <dt class="col-sm-3"><i class="bi bi-info-circle me-1"></i>Status</dt>
                    <dd class="col-sm-9">
                        <span class="badge {{ $user->status === 'active' ? 'bg-success' : 'bg-danger' }}">
                            {{ ucfirst($user->status) }}
                        </span>
                    </dd>

                    <dt class="col-sm-3"><i class="bi bi-calendar me-1"></i>Created At</dt>
                    <dd class="col-sm-9">{{ $user->created_at->format('F j, Y') }}</dd>

                    @if ($user->role === 'farmer')
                        <dt class="col-sm-3"><i class="bi bi-geo-alt me-1"></i>Farm Location</dt>
                        <dd class="col-sm-9">{{ $user->farm_location ?? 'Not Provided' }}</dd>
                    @elseif ($user->role === 'buyer')
                        <dt class="col-sm-3"><i class="bi bi-buildings me-1"></i>Business</dt>
                        <dd class="col-sm-9">{{ $user->buyer_business ?? 'Not Provided' }}</dd>
                    @endif
                </dl>
            </div>
        </div>
    </div>

    @if ($user->is_banned)
        <div class="alert alert-danger mt-4">
            <h5 class="mb-2">🚫 Banned User</h5>

            <p><strong>Type:</strong>
                {{ $user->is_permanently_banned ? 'Permanent Ban' : 'Temporary Ban' }}
            </p>

            @if (!$user->is_permanently_banned && $user->banned_until)
                <p><strong>Banned Until:</strong>
                    {{ $user->banned_until->format('F j, Y h:i A') }}
                </p>
            @endif

            <p><strong>Reason:</strong> {{ $user->ban_reason ?? 'Not specified' }}</p>
        </div>
    @endif

    {{-- ✅ Modern styled button --}}
    <a href="{{ route('admin.users.index') }}" class="btn btn-reset mt-4">
        ← Back to List
    </a>
</div>
@endsection
