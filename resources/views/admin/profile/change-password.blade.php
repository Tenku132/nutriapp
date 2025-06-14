@extends('layouts.admin')

@section('title', 'Change Password')

@section('content')
<div class="container mt-4">
    <h4 class="mb-4 text-white fw-bold">Change Password</h4>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.password.update') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="current_password" class="form-label text-white">Current Password</label>
            <input type="password" name="current_password" id="current_password" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="new_password" class="form-label text-white">New Password</label>
            <input type="password" name="new_password" id="new_password" class="form-control" required minlength="8">
        </div>

        <div class="mb-3">
            <label for="new_password_confirmation" class="form-label text-white">Confirm New Password</label>
            <input type="password" name="new_password_confirmation" id="new_password_confirmation" class="form-control" required minlength="8">
        </div>

        <div class="d-flex justify-content-end">
            <button type="submit" class="btn btn-success ">Update Password</button>
        </div>
    </form>
</div>
@endsection
