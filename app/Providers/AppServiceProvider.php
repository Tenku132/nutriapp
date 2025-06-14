<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\Message;
use Illuminate\Pagination\Paginator;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        View::composer('*', function ($view) {
            if (Auth::check()) {
                $user = Auth::user();

                // 🔐 Force logout if account is inactive (even with active session)
                if ($user->status === 'inactive') {
                    Auth::logout();
                    session()->invalidate();
                    session()->regenerateToken();

                    redirect()->route('login')->withErrors([
                        'email' => 'Your account is inactive. Please contact support to reactivate.',
                    ])->send();
                    exit;
                }

                // ✅ Message Unread Count
                $unreadCount = Message::where('receiver_id', $user->id)
                    ->where('is_read', false)
                    ->count();
                $view->with('unreadCount', $unreadCount);

                // ✅ Admin Notifications
                if ($user->role === 'admin') {
                    $adminNotifications = $user->unreadNotifications()->take(5)->get();
                    $view->with('adminNotifications', $adminNotifications);
                }
            }
        
                Paginator::useBootstrapFive();
        });
    }

    
}
