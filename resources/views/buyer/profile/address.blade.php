@extends('layouts.app')

@section('title', 'Update Address')

@section('content')
<div class="container py-4">
    <h4 class="fw-bold text-success mb-4">Update Address</h4>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('buyer.profile.address.update') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="address" class="form-label">Current Address</label>
            <textarea name="address" id="address" class="form-control @error('address') is-invalid @enderror" rows="3" required>{{ old('address', $user->address) }}</textarea>
            @error('address')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-success">Update Address</button>
    </form>
</div>
@endsection