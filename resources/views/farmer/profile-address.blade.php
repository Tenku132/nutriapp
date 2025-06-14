@extends('layouts.farmer')

@section('title', 'Address Settings')

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
    <h2 class="profile-title">ADDRESS</h2>

    <div class="row">
        {{-- ✅ Sidebar --}}
        @include('farmer.partials.profile-sidebar')

        {{-- ✅ Address Form --}}
        <div class="col-md-9">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <form method="POST" action="{{ route('farmer.profile.updateAddress') }}">
                @csrf

                @php
                    $user = Auth::user();
                    $street = $user->street ?? '';
                    $barangay = $user->barangay ?? '';
                    $city = $user->city ?? '';
                    $province = $user->province ?? '';
                    $zip = $user->zip ?? '';
                @endphp


                <div class="mb-3">
                    <label class="form-label">Street</label>
                    <input type="text" name="street" class="form-control" value="{{ trim($street) }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Barangay</label>
                    <input type="text" name="barangay" class="form-control" value="{{ trim($barangay) }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">City</label>
                    <input type="text" name="city" class="form-control" value="{{ trim($city) }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Province</label>
                    <input type="text" name="province" class="form-control" value="{{ trim($province) }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">ZIP Code</label>
                    <input type="text" name="zip" class="form-control" value="{{ trim($zip) }}" required pattern="\d{4}" title="Enter a 4-digit ZIP code">
                </div>

                <button type="submit" class="btn btn-success">Save Address</button>
            </form>
        </div>
    </div>
</div>
@endsection
