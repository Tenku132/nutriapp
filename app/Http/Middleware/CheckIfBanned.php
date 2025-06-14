<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckIfBanned
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        if ($user && ($user->is_banned || ($user->banned_until && now()->lessThan($user->banned_until)))) {
            Auth::logout();
            return redirect()->route('login')->withErrors([
                'email' => 'Your account has been banned. Reason: ' . ($user->ban_reason ?? 'Not specified'),
            ]);
        }

        return $next($request);
    }
}

