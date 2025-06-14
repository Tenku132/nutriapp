<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * 👇 Used by Fortify & Laravel for post-login redirects
     */
    public const HOME = '/redirect-by-role';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        // ✅ Use correct guard for signed email verification URLs in admin
        if (request()->is('admin/*')) {
            URL::defaults(['guard' => 'admin']);
        }

        $this->configureRateLimiting();

        $this->routes(function () {
            // ✅ Buyer & Farmer Routes
            Route::middleware('web')
                ->group(base_path('routes/web.php'));

            // ✅ Admin Routes
            Route::middleware('web')
                ->group(base_path('routes/admin.php'));

            // ✅ API Routes
            Route::prefix('api')
                ->middleware('api')
                ->group(base_path('routes/api.php'));
        });
    }

    /**
     * Configure the rate limiters for the application.
     */
    protected function configureRateLimiting(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
    }
}
