<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckIfInactive
{
    public function handle($request, Closure $next)
    {
        $user = Auth::user();

        if ($user && $user->status === 'inactive') {
            Auth::logout();

            return redirect()->route('login')->withErrors([
                'email' => 'Your account is inactive. Please contact support or wait for approval.',
            ]);
        }

        return $next($request);
    }
}

