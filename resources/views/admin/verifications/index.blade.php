@extends('layouts.admin')

@section('title', 'Farmer Verifications')

@section('content')
<div class="container-fluid">
    <h3 class="mb-4 fw-bold text-white">Farmer Verification Requests</h3>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @elseif(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card">
        <div class="card-body table-responsive">
            <table class="table table-hover table-rounded align-middle">
                <thead>
                    <tr>
                        <th>Farmer</th>
                        <th>Document</th>
                        <th>Status</th>
                        <th>Submitted</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($documents as $doc)
                        <tr>
                            <td>{{ $doc->farmer->name }}<br><small>{{ $doc->farmer->email }}</small></td>
                            <td>
                                <a href="{{ asset('storage/' . $doc->document_path) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                    View Document
                                </a>
                            </td>
                            <td>
                                @php
                                    $badgeClass = match($doc->status) {
                                        'pending' => 'bg-secondary',
                                        'approved' => 'bg-success',
                                        'rejected' => 'bg-danger',
                                        default => 'bg-secondary',
                                    };
                                @endphp
                                <span class="badge {{ $badgeClass }}">{{ ucfirst($doc->status) }}</span>

                                @if ($doc->status === 'rejected' && $doc->admin_note)
                                    <br><small class="text-danger fst-italic">Note: {{ $doc->admin_note }}</small>
                                @endif
                            </td>
                            <td>{{ $doc->created_at->format('M d, Y') }}</td>
                            <td>
                                @if ($doc->status === 'pending')
                                    <form action="{{ route('admin.verifications.approve', $doc->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button class="btn btn-sm btn-success">Approve</button>
                                    </form>

                                    <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $doc->id }}">
                                        Reject
                                    </button>
                                @else
                                    <em>No actions</em>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted">No verification requests found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

{{-- ✅ Move modals outside the loop --}}
@foreach ($documents as $doc)
    @if ($doc->status === 'pending')
    <div class="modal fade" id="rejectModal{{ $doc->id }}" tabindex="-1" aria-labelledby="rejectModalLabel{{ $doc->id }}" aria-hidden="true">
        <div class="modal-dialog"> {{-- Wider and centered --}}
            <form method="POST" action="{{ route('admin.verifications.reject', $doc->id) }}">
                @csrf
                @method('PATCH')
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="rejectModalLabel{{ $doc->id }}">Reject Verification</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <label for="admin_note{{ $doc->id }}" class="form-label">Rejection Reason</label>
                        <textarea name="admin_note" class="form-control" id="admin_note{{ $doc->id }}" rows="5" style="min-height: 120px;" required></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-danger w-100">Confirm Reject</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    @endif
@endforeach
