@extends('layouts.admin')

@section('title', 'Manage Users')

@section('content')
<div class="container py-4">
    <h4 class="mb-4 text-white">Manage Users</h4>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @elseif (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <!-- Filters and Export -->
    <form method="GET" action="{{ route('admin.users.index') }}" class="row g-3 align-items-center mb-3">
        <div class="col-auto">
            <input type="text" name="search" class="form-control" placeholder="Search..." value="{{ request('search') }}">
        </div>
        <div class="col-auto">
            <select name="role" class="form-select">
                <option value="">All Roles</option>
                <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="farmer" {{ request('role') == 'farmer' ? 'selected' : '' }}>Farmer</option>
                <option value="buyer" {{ request('role') == 'buyer' ? 'selected' : '' }}>Buyer</option>
            </select>
        </div>
        <div class="col-auto">
            <select name="status" class="form-select">
                <option value="">All Statuses</option>
                <option value="banned" {{ request('status') == 'banned' ? 'selected' : '' }}>Banned</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-utility btn-search">Search</button>
            <a href="{{ route('admin.users.export', request()->query()) }}" class="btn btn-utility btn-export">
                <i class="bi bi-download me-1"></i> Export
            </a>
            <a href="{{ route('admin.users.create') }}" class="btn btn-utility btn-add-product">
                + Add New User
            </a>
        </div>
    </form>

    <!-- User Table -->
    <div class="card">
        <div class="card-body p-0 position-relative">
            <div class="table-responsive rounded-top position-relative" style="overflow: visible;">
                <table class="table table-borderless align-middle custom-product-table table-rounded mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($users as $user)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=random&color=fff&size=40"
                                             class="rounded-circle me-3" width="40" height="40" alt="Avatar">
                                        <div>
                                            <strong>{{ $user->name }}</strong>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    <span class="badge role-badge role-{{ $user->role }}">
                                        {{ ucfirst($user->role) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex flex-column gap-1 align-items-start">
                                        <form method="POST" action="{{ route('admin.users.toggleStatus', $user->id) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="badge rounded-pill border-0 {{ $user->status === 'active' ? 'bg-success' : 'bg-danger' }}">
                                                {{ ucfirst($user->status) }}
                                            </button>
                                        </form>

                                        @if ($user->is_banned)
                                            <span class="badge rounded-pill bg-danger">Banned</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="text-end d-flex gap-2 justify-content-end">
                                    <button class="btn btn-sm btn-outline-primary"
                                            data-bs-toggle="modal"
                                            data-bs-target="#viewUserModal"
                                            onclick="loadUserDetails({{ $user->id }})">
                                        <i class="bi bi-eye"></i>
                                    </button>

                                    <button class="btn btn-sm btn-outline-secondary"
                                            data-bs-toggle="modal"
                                            data-bs-target="#userActionsModal"
                                            onclick="openUserActionModal({{ $user->id }}, '{{ $user->role }}', '{{ $user->is_banned }}')">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">No users found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div> 
        </div>
    </div>
</div>
@endsection

@push('modals')
<!-- View User Modal -->
<div class="modal fade" id="viewUserModal" tabindex="-1" aria-labelledby="viewUserModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="viewUserModalLabel">User Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="user-details-content">
        <p class="text-muted">Loading...</p>
      </div>
    </div>
  </div>
</div>

<!-- User Actions Modal -->
<div class="modal fade" id="userActionsModal" tabindex="-1" aria-labelledby="userActionsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-md">
    <div class="modal-content">

      <!-- DELETE Form -->
      <form id="user-action-form" method="POST">
        @csrf
        @method('DELETE')
        <div class="modal-header">
          <h5 class="modal-title" id="userActionsModalLabel">User Actions</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body d-flex flex-column gap-2">
          <a id="editUserLink" class="btn btn-outline-primary w-100">
            <i class="fas fa-edit me-2"></i> Edit
          </a>
        </div>

        <div class="modal-footer">
          <button type="submit" class="btn btn-outline-danger w-100">
            <i class="fas fa-trash me-2"></i> Delete
          </button>
        </div>
      </form>

      <!-- Separate Ban/Unban Form -->
      <form id="banUserForm" method="POST" class="p-3 pt-0">
        @csrf
        <button type="submit" class="btn w-100" id="banUserButton">
          <i class="bi bi-slash-circle me-2"></i> Ban / Unban
        </button>
      </form>
    </div>
  </div>
</div>
@endpush


@push('scripts')
<script>
function loadUserDetails(userId) {
    const content = document.getElementById('user-details-content');
    content.innerHTML = '<p class="text-muted">Loading...</p>';

    fetch(`/admin/users/${userId}`)
        .then(response => response.text())
        .then(data => {
            content.innerHTML = data;
        })
        .catch(() => {
            content.innerHTML = '<p class="text-danger">Failed to load user details.</p>';
        });
}

function openUserActionModal(userId, role, isBanned) {
    const form = document.getElementById('user-action-form');
    form.action = `/admin/users/${userId}`; // for delete

    document.getElementById('editUserLink').href = `/admin/users/${userId}/edit`;

    const banForm = document.getElementById('banUserForm');
    const banButton = document.getElementById('banUserButton');

    if (isBanned === '1') {
        banForm.action = `/admin/users/${userId}/unban`;
        banButton.className = 'btn btn-outline-success w-100';
        banButton.innerHTML = '<i class="bi bi-check-circle me-2"></i> Lift Ban';
    } else {
        banForm.action = `/admin/users/${userId}/ban`;
        banButton.className = 'btn btn-outline-warning w-100';
        banButton.innerHTML = '<i class="bi bi-slash-circle me-2"></i> Issue Ban';
    }
}

</script>
@endpush
