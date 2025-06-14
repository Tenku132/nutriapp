@extends('layouts.admin')

@section('title', 'Audit Logs')

@section('content')
<div class="container py-4">
    <h2 class="mb-4 text-white">Audit Logs</h2>

    <div class="card table-rounded">
        <div class="card-body p-0">
            <table class="table table-borderless align-middle text-sm admin-order-table table-rounded">
                <thead class="table-light">
                    <tr>
                        <th>User</th>
                        <th>Field</th>
                        <th>Old Value</th>
                        <th>New Value</th>
                        <th>Changed By (Admin)</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($logs as $log)
                        <tr>
                            <td>{{ $log->user?->name ?? 'N/A' }}</td>
                            <td>{{ ucfirst($log->field) }}</td>
                            <td>{{ $log->old_value }}</td>
                            <td>{{ $log->new_value }}</td>
                            <td>{{ $log->admin?->name ?? 'N/A' }}</td>
                            <td>{{ $log->created_at->format('d M Y, h:i A') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">No audit logs found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="d-flex justify-content-between align-items-center px-3 pb-3">
                <div class="text-muted small">
                    Showing {{ $logs->firstItem() }} to {{ $logs->lastItem() }} of {{ $logs->total() }} results
                </div>
                <div>
                    {{ $logs->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
