@extends('layouts.admin')

@section('title', 'Verify Email')

@section('content')
<div class="container py-5">
    <div class="card shadow-sm">
        <div class="card-body text-center">
            <h4 class="mb-3">Verify Your Email Address</h4>

            @if (session('status') === 'verification-link-sent')
                <div class="alert alert-success" role="alert">
                    A new verification link has been sent to your email address.
                </div>
            @endif

            <p class="mb-3">Before proceeding, please check your email for a verification link.</p>
            <p>If you did not receive the email, click the button below to request another.</p>

            <form method="POST" action="{{ route('admin.verification.send') }}">
                @csrf
                <button type="submit" class="btn btn-primary">
                    Resend Verification Email
                </button>
            </form>

            <hr class="my-4">

            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit" class="btn btn-link text-danger">
                    Logout
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
