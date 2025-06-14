@extends('layouts.farmer')

@section('title', 'Edit Profile')

@section('content')
<div class="container py-4">
    <h2 class="mb-4 text-success fw-bold">Edit Profile</h2>

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

    <form action="{{ route('farmer.profile.update') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <!-- Personal Info -->
    <div class="mb-3">
        <label for="name" class="form-label">Username</label>
        <input type="text" class="form-control" id="name" name="name"
               value="{{ old('name', $farmer->name ?? '') }}" required>
    </div>

    <div class="mb-3">
        <label for="phone" class="form-label">Phone Number</label>
        <input type="text" class="form-control" id="phone" name="phone"
            value="{{ old('phone', $farmer->phone ?? '') }}">
    </div>

    <div class="mb-3">
        <label for="profile_photo" class="form-label">Profile Photo</label><br>
        <img src="{{ $farmer->profile_photo ? asset('storage/' . $farmer->profile_photo) : asset('images/default-avatar.png') }}"
             style="width: 100px; height: 100px; object-fit: cover;" class="mb-2">
        <input type="file" name="profile_photo" class="form-control">
    </div>

    <!-- Business Info -->
    <div class="mb-3">
        <label for="business_name" class="form-label">Business Name</label>
        <input type="text" class="form-control" id="business_name" name="business_name"
               value="{{ old('business_name', $farmer->business_name ?? '') }}">
    </div>



    <div class="mb-3">
        <label for="bio" class="form-label">Bio</label>
        <textarea class="form-control" id="bio" name="bio" rows="4">{{ old('bio', $farmer->bio ?? '') }}</textarea>
    </div>

    <div class="mb-3">
        <label for="business_photo" class="form-label">Business Photo</label><br>
        <img src="{{ $farmer->business_photo ? asset('storage/' . $farmer->business_photo) : asset('images/default-store.png') }}"
             style="width: 100px; height: 100px; object-fit: cover;" class="mb-2">
        <input type="file" name="business_photo" class="form-control">
    </div>

    <button type="submit" class="btn btn-success">Update Profile</button>
    <a href="{{ route('farmer.profile.show') }}" class="btn btn-secondary">Cancel</a>
</form>

</div>
@endsection
