@extends('layouts.farmer')

@section('title', 'Payout Settings')

@push('styles')
<style>
    .profile-title {
        font-weight: bold;
        font-size: 1.5rem;
        color: #1e4d2b;
        margin-bottom: 1.5rem;
    }
    .sidebar-nav a {
        display: block;
        padding: 0.5rem 1rem;
        font-weight: bold;
        color: #1e4d2b;
        text-decoration: none;
        border-left: 4px solid transparent;
        margin-bottom: 0.5rem;
    }
    .sidebar-nav a.active,
    .sidebar-nav a:hover {
        background-color: #d4edda;
        border-left: 4px solid #1e4d2b;
        border-radius: 5px;
    }
</style>
@endpush

@section('content')
<div class="container">
    <h2 class="profile-title">PAYOUT SETTINGS</h2>

    <div class="row">
        {{-- ✅ Left sidebar --}}
        @include('farmer.partials.profile-sidebar')

        {{-- ✅ Right form area --}}
        <div class="col-md-9">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <form method="POST" action="{{ route('farmer.profile.updatePayout') }}">
                @csrf

                <p class="text-muted small mb-3">We use this info to send your earnings. Please double-check accuracy.</p>

                {{-- Primary Method --}}
                <div class="mb-3">
                    <label for="payout_method" class="form-label">Primary Payout Method</label>
                    <select class="form-select" name="payout_method" required>
                        <option value="GCash" {{ Auth::user()->payout_method == 'GCash' ? 'selected' : '' }}>GCash</option>
                        <option value="Bank" {{ Auth::user()->payout_method == 'Bank' ? 'selected' : '' }}>Bank Transfer</option>
                        <option value="Maya" {{ Auth::user()->payout_method == 'Maya' ? 'selected' : '' }}>Maya</option>
                    </select>
                </div>

                {{-- Primary Account --}}
                <div class="mb-3">
                    <label for="payout_account" class="form-label">Account Number / Name</label>
                    <input type="text" name="payout_account" class="form-control"
                        value="{{ Auth::user()->payout_account }}" required pattern="\d{11}" 
                        title="Enter a valid 11-digit GCash number or valid account format.">
                </div>

                {{-- Status --}}
                @if(Auth::user()->payout_verified)
                    <div class="mb-3">
                        <span class="badge bg-success">Verified</span>
                    </div>
                @else
                    <div class="mb-3">
                        <span class="badge bg-warning text-dark">Pending Verification</span>
                    </div>
                @endif

                <hr class="my-4">

                {{-- Secondary Method --}}
                <div class="mb-3">
                    <label for="payout_method_secondary" class="form-label">Secondary Payout Method <small class="text-muted">(optional)</small></label>
                    <select class="form-select" name="payout_method_secondary">
                        <option value="">None</option>
                        <option value="GCash" {{ Auth::user()->payout_method_secondary == 'GCash' ? 'selected' : '' }}>GCash</option>
                        <option value="Bank" {{ Auth::user()->payout_method_secondary == 'Bank' ? 'selected' : '' }}>Bank Transfer</option>
                        <option value="Maya" {{ Auth::user()->payout_method_secondary == 'Maya' ? 'selected' : '' }}>Maya</option>
                    </select>
                </div>

                {{-- Secondary Account --}}
                <div class="mb-3">
                    <label for="payout_account_secondary" class="form-label">Secondary Account Number / Name</label>
                    <input type="text" name="payout_account_secondary" class="form-control"
                        value="{{ Auth::user()->payout_account_secondary }}">
                </div>

                <button type="submit" class="btn btn-success">Save Changes</button>
            </form>
        </div>
    </div>
</div>
@endsection
