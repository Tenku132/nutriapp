@extends('layouts.admin')

@section('title', 'Admin Profile')

@section('content')
<div class="container py-4">
    <h3 class="mb-4 fw-bold text-white">Admin Profile</h3>

    <div class="card p-4 shadow-sm">
        <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf


            <div class="mb-3 text-center">
                <img src="{{ $admin->profile_picture ? asset('storage/' . $admin->profile_picture) : 'https://ui-avatars.com/api/?name=' . urlencode($admin->name) }}"
                     class="rounded-circle mb-2" width="100" height="100" style="object-fit: cover;" alt="Profile Picture">

                <div>
                    <label for="profile_picture" class="form-label fw-semibold">Change Profile Picture</label>
                    <input type="file" name="profile_picture" id="profile_picture" class="form-control @error('profile_picture') is-invalid @enderror">
                    @error('profile_picture')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mb-3">
                <label for="name" class="form-label fw-semibold">Full Name</label>
                <input type="text" name="name" id="name" value="{{ old('name', $admin->name) }}" class="form-control @error('name') is-invalid @enderror">
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="email" class="form-label fw-semibold">Email Address</label>
                <input type="email" name="email" id="email" value="{{ old('email', $admin->email) }}" class="form-control @error('email') is-invalid @enderror">
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex justify-content-between">
                <a href="{{ route('admin.password.change') }}" class="btn btn-outline-secondary">Change Password</a>
                <button type="submit" class="btn btn-success">Update Profile</button>
            </div>
        </form>
    </div>  
</div>
@endsection
