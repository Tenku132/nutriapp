<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;    
use Illuminate\Validation\ValidationException;


class AdminAuthenticatedSessionController extends Controller
{
    public function create()
    {
        return view('admin.auth.login');
    }

    public function store(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::guard('admin')->attempt($credentials, $request->boolean('remember'))) {
            $user = Auth::guard('admin')->user();

        if ($user->is_banned) {
            $message = $user->is_permanently_banned
                ? 'Your account has been permanently banned. Please contact support.'
                : 'Your account has been temporarily banned.';

            Auth::guard('admin')->logout();
            return back()->withErrors(['email' => $message]);
        }


        if ($user->status !== 'active') {
            Auth::guard('admin')->logout();
            return back()->withErrors([
                'email' => 'Your admin account is inactive. Please contact support to reactivate.',
            ]);
        }

        if (!$user || $user->role !== 'admin') {
            Auth::guard('admin')->logout();
            return redirect()->route('admin.login')->withErrors([
                'email' => 'Unauthorized. Only admins can login here.',
            ]);
        }

            if (!$user->hasVerifiedEmail()) {
                Auth::guard('admin')->logout();

                // Automatically resend verification email
                event(new Registered($user));

                return back()->withErrors([
                    'email' => 'Please verify your email before logging in. A new verification link has been sent.',
                ]);
            }

            $request->session()->regenerate();

            return redirect()->route('admin.dashboard');


        }

        throw ValidationException::withMessages([
            'email' => __('auth.failed'),
        ]);
    }

    public function destroy(Request $request)
    {
        Auth::guard('admin')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
