@extends('layouts.auth')

@section('title', 'Forgot Password')

@section('body-class', 'login-page')

@section('content')
<div class="nutri-login-container">
    <div class="nutri-login-box">
        <!-- Sidebar Branding -->
        <div class="nutri-login-sidebar">
            <img src="{{ asset('img/nutriteam-logo.png') }}" alt="NutriApp Logo">
            <h3>Reset Your Password</h3>
            <p>We'll send you a link to reset your admin account.</p>
        </div>

        <!-- Forgot Password Form -->
        <div class="nutri-login-form">
            <h4 class="mb-4">Forgot Password</h4>

            @if (session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('admin.password.email') }}">
                @csrf

                <div class="mb-3 nutri-form-group">
                    <label for="email" class="form-label">Enter your email address</label>
                    <input type="email" name="email" class="form-control" id="email" required autofocus>
                </div>

                <button type="submit" class="btn btn-nutri w-100">Send Password Reset Link</button>

                <div class="mt-3">
                    <a href="{{ route('admin.login') }}" class="text-muted">← Back to login</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
