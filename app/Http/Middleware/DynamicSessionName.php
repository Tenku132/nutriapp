<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class DynamicSessionName
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->is('admin') || $request->is('admin/*')) {
            Config::set('session.cookie', 'laravel_session_admin');
        } else {
            Config::set('session.cookie', 'laravel_session_web');
        }

        return $next($request);
    }
}
