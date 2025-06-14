<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Actions\AttemptToAuthenticate;
use Laravel\Fortify\Contracts\LoginViewResponse;
use Laravel\Fortify\Contracts\LoginResponse;
use Laravel\Fortify\Contracts\FailedLoginResponse;
use Laravel\Fortify\Contracts\LogoutResponse;
use Illuminate\Validation\ValidationException;

class AuthenticatedSessionController extends Controller
{
    public function create()
    {
        return app(LoginViewResponse::class);
    }
    public function store(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();

            // 🔒 Prevent banned/inactive users from logging in
            if ($user->isCurrentlyBanned() || $user->status === 'inactive') {
                Auth::logout();

                throw ValidationException::withMessages([
                    'email' => $user->isCurrentlyBanned()
                        ? 'Your account is banned. Please contact support.'
                        : 'Your account is inactive.',
                ]);
            }

            return match ($user->role) {
                'farmer' => redirect()->route('farmer.dashboard'),
                'buyer'  => redirect()->route('buyer.dashboard'),
                default => abort(403),
            };
        }


        throw ValidationException::withMessages([
            'email' => __('auth.failed'),
        ]);
    }

    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return app(LogoutResponse::class);
    }
}