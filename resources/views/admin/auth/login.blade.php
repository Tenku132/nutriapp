@extends('layouts.auth')

@section('title', 'Login')

@section('body-class', 'login-page')

@section('content')

<div class="nutri-login-container">
    <div class="nutri-login-box">
        <!-- Sidebar / Branding -->
        <div class="nutri-login-sidebar">
          <img src="{{ asset('img/nutriteam-logo.png') }}" alt="NutriApp Logo">
          <h3>Welcome to NutriHub</h3>
          <p>Manage your agricultural marketplace with confidence.</p>
        </div>

        <!-- Login Form -->
        <div class="nutri-login-form">
            <h4 class="mb-4">Sign in to your Admin account</h4>

            @if (session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('admin.login.store') }}">
                @csrf

                <div class="mb-3 nutri-form-group">
                    <label for="email" class="form-label">Email address</label>
                    <input type="email" name="email" class="form-control" id="email" required autofocus>
                </div>

                <div class="mb-3 nutri-form-group">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" id="password" required>
                </div>

                <div class="mb-3 d-flex justify-content-between align-items-center">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember">
                        <label class="form-check-label" for="remember">Remember me</label>
                    </div>
                    <a href="{{ route('admin.password.request') }}" class="text-muted">Forgot Password?</a>
                </div>

                <button type="submit" class="btn btn-nutri w-100">Sign In</button>
            </form>
        </div>
    </div>
</div>
@endsection
