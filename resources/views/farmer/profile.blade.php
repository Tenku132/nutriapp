@extends('layouts.farmer')

@section('title', 'Profile')

@push('styles')
<style>
    .profile-section {
        background-color: #e6e6e6;
        padding: 2rem;
        border-radius: 10px;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
    }
    .profile-title {
        font-weight: bold;
        font-size: 1.5rem;
        color: #1e4d2b;
        margin-bottom: 1.5rem;
    }
    .profile-photo {
        width: 120px;
        height: 120px;
        object-fit: cover;
        border-radius: 8px;
        border: 2px solid #ccc;
    }
    .photo-wrapper {
        text-align: center;
        margin-bottom: 1rem;
    }
    .change-photo-link {
        font-size: 0.85rem;
        display: block;
        margin-top: 0.5rem;
        color: #1e4d2b;
        text-decoration: underline;
    }
    .label {
        font-weight: bold;
        color: #1e4d2b;
    }
    .edit-btn {
        float: right;
        background: #1e4d2b;
        color: white;
        padding: 0.3rem 0.8rem;
        border-radius: 4px;
        font-size: 0.85rem;
        text-decoration: none;
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
    <h2 class="profile-title">PROFILE</h2>
    <div class="row">
        @include('farmer.partials.profile-sidebar')

        <div class="col-md-9">
            <div class="profile-section">
                <a href="{{ route('farmer.profile.edit') }}" class="edit-btn">EDIT</a>

                <h4 class="mt-3 mb-3">PERSONAL INFORMATION</h4>
                <div class="row mb-4">
                    <div class="col-md-3 photo-wrapper">
                        <img src="{{ $farmer->profile_photo ? asset('storage/profile_photos/' . $farmer->profile_photo) : asset('images/default-avatar.png') }}" class="profile-photo" alt="Profile Photo">
                        <a href="{{ route('farmer.profile.edit') }}" class="change-photo-link">Change Photo</a>

                    </div>
                    <div class="col-md-9">
                        <p><span class="label">USERNAME:</span> {{ $farmer->name }}</p>
                        <p><span class="label">EMAIL:</span> {{ $farmer->email }}</p>
                        <p><span class="label">PHONE NUMBER:</span> {{ $farmer->phone }}</p>
                    </div>
                </div>

                <h4 class="mt-3 mb-3">BUSINESS INFORMATION</h4>
                <div class="row mb-4">
                    <div class="col-md-3 photo-wrapper">
                        <img src="{{ $farmer->business_photo ? asset('storage/business_photos/' . $farmer->business_photo) : asset('images/default-store.png') }}" class="profile-photo" alt="Business Photo">
                        <a href="{{ route('farmer.profile.edit') }}" class="change-photo-link">Change Logo</a>
                    </div>
                    <div class="col-md-9">
                        <p><span class="label">BUSINESS NAME:</span> {{ $farmer->business_name }}</p>
                        <p><span class="label">LOCATION:</span>
                            {{ implode(', ', array_filter([
                                $farmer->street ?? '',
                                $farmer->barangay ?? '',
                                $farmer->city ?? '',
                                $farmer->province ?? '',
                                $farmer->zip ?? '',
                            ])) }}
                        </p>
                        <p><span class="label">BIO:</span> {{ $farmer->bio }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
