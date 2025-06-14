@extends('layouts.admin')

@section('title', 'Admin Activity Logs')

@section('content')
<div class="container py-4">
    <h4 class="mb-4 text-white">Admin Activity Logs</h4>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @elseif (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card table-rounded shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover table-borderless align-middle table-rounded admin-order-table">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Admin</th>
                        <th>Action</th>
                        <th>Description</th>
                        <th>IP</th>
                        <th>User Agent</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($logs as $log)
                        <tr>
                            <td>{{ $log->id }}</td>
                            <td><span class="badge bg-success">{{ $log->admin->name ?? 'N/A' }}</span></td>
                            <td><strong>{{ $log->action }}</strong></td>
                            <td>{{ $log->description }}</td>
                            <td>{{ $log->ip_address }}</td>
                            <td class="text-truncate" style="max-width: 200px;">{{ $log->user_agent }}</td>
                            <td>{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">No activity logs found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center px-3 pb-3">
                <div class="text-muted small mb-2 mb-md-0">
                    Showing {{ $logs->firstItem() ?? 0 }} to {{ $logs->lastItem() ?? 0 }} of {{ $logs->total() }} entries
                </div>
                <div>
                    {{ $logs->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
