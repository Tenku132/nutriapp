<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>@yield('title') | Admin Panel</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Google Font: Inter (Variable Font) -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0..1,14..32,100..900&display=swap" rel="stylesheet">

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <!-- AdminLTE -->
  <link rel="stylesheet" href="{{ asset('adminlte/css/adminlte.min.css') }}">
  <!-- Custom CSS -->
  <link rel="stylesheet" href="{{ asset('adminlte/css/custom-admin.css') }}">
</head>

<body class="admin-body-bg" style="min-height: 100vh;">

@auth
<div class="d-flex min-vh-100 px-3 pt-3 pb-2 gap-3">

  {{-- Rounded Sidebar Wrapper --}}
  <div class="sidebar-wrapper">
    @include('admin.partials.sidebar')
  </div>

  {{-- Main Content --}}
  <div class="flex-grow-1 d-flex flex-column">

    {{-- Topbar --}}
    <nav class="admin-topbar shadow-sm bg-white rounded-4 mt-2 mx-auto px-4 py-3" style="width: 100%; max-width: 98%;">
      <div class="d-flex align-items-center justify-content-between w-100">

        {{-- Sidebar Toggle --}}
        <button class="btn btn-sm btn-light border d-flex align-items-center justify-content-center me-3" id="sidebarToggle" style="width: 36px; height: 36px;">
          <i class="bi bi-list fs-5"></i>
        </button>

        {{-- Center Search --}}
        <div class="flex-grow-1 d-flex justify-content-center px-3">
          <form action="#" method="GET" class="position-relative" style="max-width: 420px; width: 100%;">
            <input type="text" name="query" class="form-control rounded-pill ps-4 pe-5" placeholder="Search" style="height: 44px;">
            <button type="submit" class="btn position-absolute top-0 end-0 mt-1 me-2 border-0 bg-transparent">
              <i class="bi bi-search fs-5 text-muted"></i>
            </button>
          </form>
        </div>

        {{-- Notification + Profile --}}
        <div class="d-flex align-items-center gap-3 ms-3" style="flex-shrink: 0; max-width: 280px;">
          {{-- Notification Dropdown --}}
          <div class="dropdown">
            <a id="notificationDropdown" class="nav-link position-relative" data-bs-toggle="dropdown" aria-expanded="false" style="cursor: pointer;">
              <i class="bi bi-bell fs-5 text-dark"></i>
              <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-warning text-dark" id="notifCount" style="display: none;">0</span>
            </a>
            <ul class="dropdown-menu dropdown-menu-end p-2" aria-labelledby="notificationDropdown" id="notifDropdownMenu" style="min-width: 300px;">
              <li class="dropdown-header fw-bold">Notifications</li>
              <li id="notifItems"><em class="text-muted small">Loading...</em></li>
              <li><hr class="dropdown-divider"></li>

              <li>
                <form id="markAllReadForm" method="POST" action="{{ route('admin.notifications.markAllRead') }}">
                  @csrf
                  <button class="dropdown-item text-primary text-center" type="submit">Mark all as read</button>
                </form>
              </li>

              <li>
                <button class="dropdown-item text-danger text-center" onclick="clearAllNotifications()">Clear all</button>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </nav>

    {{-- Page Content --}}
    <main class="p-4 flex-grow-1">
      @yield('content')
    </main>

  </div>
</div>
@endauth

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('adminlte/js/adminlte.min.js') }}"></script>

@yield('scripts')
@stack('scripts')

<script>
  document.getElementById('sidebarToggle').addEventListener('click', function () {
    document.querySelector('aside').classList.toggle('d-none');
  });

  document.addEventListener('DOMContentLoaded', function () {
    fetchNotifications();

    function fetchNotifications() {
      fetch("{{ route('admin.notifications.index') }}")
        .then(response => response.json())
        .then(data => {
          const notifItems = document.getElementById('notifItems');
          const notifCount = document.getElementById('notifCount');

          notifItems.innerHTML = '';

          if (data.length === 0) {
            notifItems.innerHTML = '<li><span class="dropdown-item text-muted">No notifications</span></li>';
            notifCount.style.display = 'none';
          } else {
            const unreadCount = data.filter(n => n.read_at === null).length;
            notifCount.textContent = unreadCount;
            notifCount.style.display = unreadCount > 0 ? 'inline-block' : 'none';

            data.forEach(notification => {
              const li = document.createElement('li');
              const createdAt = new Date(notification.created_at);

              li.innerHTML = `
                <a href="${notification.data.link || '#'}" class="dropdown-item" onclick="markAsRead('${notification.id}', this)">
                  <div class="d-flex align-items-start gap-2">
                    <i class="bi ${notification.data.icon || 'bi-info-circle'} fs-5 text-secondary mt-1"></i>
                    <div class="flex-grow-1">
                      <div class="d-flex justify-content-between align-items-start">
                        <span class="text-wrap" style="max-width: 220px;">${notification.data.message}</span>
                        ${notification.read_at ? '' : '<span class="badge bg-success ms-2">New</span>'}
                      </div>
                      <small class="text-muted d-block mt-1">${timeSince(createdAt)}</small>
                    </div>
                  </div>
                </a>
              `;
              notifItems.appendChild(li);
            });
          }
        });
    }

    function timeSince(date) {
      const seconds = Math.floor((new Date() - date) / 1000);
      const intervals = [
        { label: 'year', seconds: 31536000 },
        { label: 'month', seconds: 2592000 },
        { label: 'day', seconds: 86400 },
        { label: 'hour', seconds: 3600 },
        { label: 'minute', seconds: 60 },
        { label: 'second', seconds: 1 },
      ];

      for (const interval of intervals) {
        const count = Math.floor(seconds / interval.seconds);
        if (count > 0) {
          return `${count} ${interval.label}${count !== 1 ? 's' : ''} ago`;
        }
      }
      return 'just now';
    }

    window.markAsRead = function (id, el) {
      fetch(`/admin/notifications/${id}/mark-read`, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
          'Accept': 'application/json'
        }
      }).then(() => {
        el.querySelector('.badge')?.remove();
        fetchNotifications();
      });
    };

    window.clearAllNotifications = function () {
      if (!confirm("Are you sure you want to delete all notifications?")) return;

      fetch("{{ route('admin.notifications.clearAll') }}", {
        method: 'DELETE',
        headers: {
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
          'Content-Type': 'application/json'
        }
      }).then(res => res.json())
        .then(data => {
          if (data.status === 'cleared') {
            const notifItems = document.getElementById('notifItems');
            const notifCount = document.getElementById('notifCount');
            notifItems.innerHTML = '<li class="dropdown-item text-muted">No notifications</li>';
            notifCount.style.display = 'none';
          }
        });
    };
  });
</script>
@yield('scripts')
@stack('scripts')
@stack('modals')
</body>
</html>
