@extends('layouts.admin')

@section('title', 'Issue Ban to User')

@section('content')
<div class="container py-4">
    <h2 class="fw-bold mb-4" style="color: #ffffff; font-size: 2rem;">Ban User: {{ $user->name }}</h2>

    <form method="POST" action="{{ route('admin.users.ban', $user->id) }}">
        @csrf

        <div class="mb-3">
            <label for="reason" class="form-label text-white">Reason for Ban</label>
            <textarea name="reason" id="reason" class="form-control" rows="3" required placeholder="E.g. abusive language, spam, etc.">{{ old('reason') }}</textarea>
        </div>

        <div class="mb-3">
            <label for="duration" class="form-label text-white">Ban Duration</label>
            <select name="duration" id="duration" class="form-select" required>
                <option value="7">1 Week</option>
                <option value="21">3 Weeks</option>
                <option value="permanent">Permanent</option>
            </select>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-ban-user">Confirm Ban</button>
            <a href="{{ route('admin.users.index') }}" class="btn btn-reset">Cancel</a>
        </div>
    </form>
</div>
@endsection
