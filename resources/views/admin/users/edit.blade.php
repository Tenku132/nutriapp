@extends('layouts.admin')

@section('title', 'Edit User')

@section('content')
<div class="container py-4 text-white"> {{-- ✅ Makes text readable on gradient bg --}}
    <h4 class="mb-4">Edit User</h4>

    <div class="card p-4 shadow-sm bg-white text-dark"> {{-- ✅ Card stays legible --}}
        <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="name" class="form-label">Name</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                </div>
                <div class="col-md-6">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                </div>
            </div>

            <div class="mb-4">
                <label for="role" class="form-label">Role</label>
                <select name="role" class="form-select" required>
                    <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="farmer" {{ $user->role === 'farmer' ? 'selected' : '' }}>Farmer</option>
                    <option value="buyer" {{ $user->role === 'buyer' ? 'selected' : '' }}>Buyer</option>
                </select>
            </div>

            <div class="d-flex justify-content-end">
                {{-- ✅ Styled Cancel Button --}}
                <a href="{{ route('admin.users.index') }}" class="btn btn-reset me-2">Cancel</a>

                {{-- ✅ Styled Update Button --}}
                <button type="submit" class="btn btn-add-product ">💾 Update</button>
            </div>
        </form>
    </div>
</div>
@endsection
