@extends('layouts.app') {{-- or your actual layout --}}

@section('title', 'Account Banned')

@section('content')
<div class="container py-5">
    <div class="alert alert-danger text-center">
        <h4>Your account has been banned.</h4>
        @if ($errors->any())
            <p>{{ $errors->first('email') }}</p>
        @else
            <p>Please contact support or an administrator for more details.</p>
        @endif
    </div>
</div>
@endsection
