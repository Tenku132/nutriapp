@extends('layouts.farmer')

@section('title', 'Settings')

@push('styles')
<style>
    .tab-button {
        padding: 0.5rem 1rem;
        border: none;
        background: none;
        font-weight: bold;
        color: #17631d;
        cursor: pointer;
    }

    .tab-button.active {
        border-bottom: 2px solid #17631d;
    }

    .form-section {
        border: 1px solid #ccc;
        background: #f8f9fa;
        padding: 1.5rem;
        border-radius: 6px;
    }
</style>
@endpush

@section('content')
<h2 class="fw-bold text-success mb-4">SETTINGS</h2>

<ul class="nav nav-tabs mb-4" id="settingsTab" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="tab-button nav-link active" id="password-tab" data-bs-toggle="tab" data-bs-target="#password" type="button" role="tab">PASSWORD</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="tab-button nav-link" id="documents-tab" data-bs-toggle="tab" data-bs-target="#documents" type="button" role="tab">DOCUMENTS</button>
    </li>
</ul>

@if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="tab-content">
    <!-- Password Change -->
    <div class="tab-pane fade show active" id="password" role="tabpanel">
        <div class="form-section">
            <h4 class="mb-3">CHANGE PASSWORD</h4>
            <p class="text-muted">Password must be at least 6 characters and include numbers, letters, and special characters.</p>

            <form method="POST" action="{{ route('farmer.settings.updatePassword') }}#password">
                @csrf
                @method('PATCH')

                <div class="mb-3">
                    <label for="old_password" class="form-label">Old Password</label>
                    <input type="password" class="form-control" name="old_password" required>
                </div>

                <div class="mb-3">
                    <label for="new_password" class="form-label">New Password</label>
                    <input type="password" class="form-control" name="new_password" required>
                </div>

                <div class="mb-3">
                    <label for="new_password_confirmation" class="form-label">Re-type Password</label>
                    <input type="password" class="form-control" name="new_password_confirmation" required>
                </div>

                <button type="submit" class="btn btn-success">Update Password</button>
            </form>
        </div>
    </div>

    <!-- Document Upload -->
    <div class="tab-pane fade" id="documents" role="tabpanel">
        <div class="form-section">
            <h4 class="mb-3">DOCUMENT VERIFICATION</h4>
            @if(auth()->user()->is_verified)
                <p class="text-success">✅ Your account is verified.</p>
            @else
                <p class="text-danger">❌ Your account is not yet verified. Please upload a valid document (e.g., government-issued ID, farmer certificate).</p>

                <form method="POST" action="{{ route('farmer.settings.uploadDocuments') }}#documents" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="document" class="form-label">Upload Document</label>
                        <input type="file" name="document" class="form-control" accept="application/pdf,image/*" required>
                    </div>

                    <button type="submit" class="btn btn-primary">Submit Document</button>
                </form>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Activate tab from URL hash (e.g., #documents)
    const hash = window.location.hash;
    if (hash) {
        const tabTrigger = document.querySelector(`button[data-bs-target="${hash}"]`);
        if (tabTrigger) {
            new bootstrap.Tab(tabTrigger).show();
        }
    }

    // Optional: Change URL hash when tab is clicked
    document.querySelectorAll('.tab-button').forEach(button => {
        button.addEventListener('shown.bs.tab', function (event) {
            history.pushState(null, null, event.target.dataset.bsTarget);
        });
    });
</script>
@endpush
