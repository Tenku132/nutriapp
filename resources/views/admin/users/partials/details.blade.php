<div class="card border-0 shadow-sm admin-user-profile p-3">
  <div class="d-flex align-items-center gap-3 mb-3">
    <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=random&color=fff&size=100"
         class="admin-avatar-img rounded-circle border" alt="Avatar">
    <div>
      <h4>{{ $user->name }}</h4>
      <p class="mb-1"><i class="bi bi-person-badge me-1"></i> Role: <strong>{{ ucfirst($user->role) }}</strong></p>
      <p class="mb-1"><i class="bi bi-envelope me-1"></i> Email: {{ $user->email }}</p>
      <p class="mb-0"><i class="bi bi-circle me-1"></i> Status: 
        <span class="badge rounded-pill {{ $user->status === 'active' ? 'bg-success' : 'bg-secondary' }}">
          {{ ucfirst($user->status) }}
        </span>
        @if ($user->is_banned)
          <span class="badge bg-danger rounded-pill">Banned</span>
        @endif
      </p>
    </div>
  </div>

  <dl class="row small mb-0">
    <dt class="col-sm-4">Joined</dt>
    <dd class="col-sm-8">{{ $user->created_at->format('F j, Y') }}</dd>
    <dt class="col-sm-4">Created At</dt>
    <dd class="col-sm-8">{{ $user->created_at->toDayDateTimeString() }}</dd>
  </dl>
</div>
