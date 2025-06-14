<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticatedByRole
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.dashboard');
        }

        if (Auth::guard('web')->check()) {
            $role = Auth::guard('web')->user()->role;

            return match ($role) {
                'farmer' => redirect()->route('farmer.dashboard'),
                'buyer'  => redirect()->route('buyer.dashboard'),
                default => abort(403),
            };
        }

        return $next($request);
    }
}
