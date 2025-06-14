@extends('layouts.auth')

@section('title', 'Reset Password')

@section('body-class', 'login-page')

@section('content')
<div class="nutri-login-container">
    <div class="nutri-login-box">
        <!-- Sidebar Branding -->
        <div class="nutri-login-sidebar">
            <img src="{{ asset('img/nutriteam-logo.png') }}" alt="NutriApp Logo">
            <h3>Welcome Back</h3>
            <p>Set a new password for your admin account.</p>
        </div>

        <!-- Reset Password Form -->
        <div class="nutri-login-form">
            <h4 class="mb-4">Reset Password</h4>

            @if ($errors->any())
                <div class="alert alert-danger">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('admin.password.update') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <div class="mb-3 nutri-form-group">
                    <label for="email" class="form-label">Email address</label>
                    <input type="email" name="email" class="form-control" id="email" value="{{ old('email', $email) }}" required autofocus>
                </div>

                <div class="mb-3 nutri-form-group">
                    <label for="password" class="form-label">New Password</label>
                    <input type="password" name="password" class="form-control" id="password" required>
                </div>

                <div class="mb-3 nutri-form-group">
                    <label for="password_confirmation" class="form-label">Confirm Password</label>
                    <input type="password" name="password_confirmation" class="form-control" id="password_confirmation" required>
                </div>

                <button type="submit" class="btn btn-nutri w-100">Reset Password</button>

                <div class="mt-3">
                    <a href="{{ route('admin.login') }}" class="text-muted">← Back to login</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
