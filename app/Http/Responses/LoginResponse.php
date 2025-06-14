<?php

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Illuminate\Support\Facades\Auth;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {
        $user = Auth::user();

        $redirect = match ($user->role) {
            'farmer' => route('farmer.dashboard'),
            'buyer'  => route('buyer.dashboard'),
            default => '/dashboard', // fallback if needed
        };

        return redirect()->intended($redirect);
    }
}
