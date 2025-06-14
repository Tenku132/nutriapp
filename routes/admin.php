<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Notification;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

use App\Http\Controllers\Admin\Auth\AdminAuthenticatedSessionController;
use App\Http\Controllers\Admin\Auth\AdminRegisteredUserController;
use App\Http\Controllers\Admin\Auth\AdminForgotPasswordController;
use App\Http\Controllers\Admin\Auth\AdminResetPasswordController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminProductController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminOrderController;
use App\Http\Controllers\Admin\AdminLogController;
use App\Http\Controllers\Admin\AdminActivityLogController;
use App\Http\Controllers\Admin\AdminReportController;
use App\Http\Controllers\Admin\AdminProfileController;
use App\Http\Controllers\Admin\AdminReviewController;
use App\Http\Controllers\Admin\NotificationController;

// ========================================================================
// 🔐 Public Routes (No Auth Required) — Login, Register, Forgot Password
// ========================================================================
Route::prefix('admin')->group(function () {
    // Login & Logout
    Route::get('/login', [AdminAuthenticatedSessionController::class, 'create'])->name('admin.login');
    Route::post('/login', [AdminAuthenticatedSessionController::class, 'store'])->name('admin.login.store');
    Route::post('/logout', [AdminAuthenticatedSessionController::class, 'destroy'])->middleware('auth:admin')->name('admin.logout');

    // Registration
    Route::get('/register', [AdminRegisteredUserController::class, 'create'])->name('admin.register');
    Route::post('/register', [AdminRegisteredUserController::class, 'store'])->name('admin.register.store');

    // Forgot/Reset Password
    Route::get('/forgot-password', [AdminForgotPasswordController::class, 'showLinkRequestForm'])->name('admin.password.request');
    Route::post('/forgot-password', [AdminForgotPasswordController::class, 'sendResetLinkEmail'])->name('admin.password.email');
    Route::get('/reset-password/{token}', [AdminResetPasswordController::class, 'showResetForm'])->name('admin.password.reset');
    Route::post('/reset-password', [AdminResetPasswordController::class, 'reset'])->name('admin.password.update');

    // 🔒 Admin Email Verification Notice Page
    Route::get('/verify-email', function () {
        return view('admin.auth.verify-email');
    })->middleware('auth:admin')->name('admin.verification.notice');

    // ✅ Admin Email Verification Handler
    Route::get('/admin/verify-email/{id}/{hash}', function (Request $request, $id, $hash) {
        $admin = User::findOrFail($id);

        if ($admin->role !== 'admin') {
            abort(403, 'Unauthorized.');
        }

        if (! hash_equals((string) $hash, sha1($admin->getEmailForVerification()))) {
            abort(403, 'Invalid hash.');
        }

        if (! Auth::guard('admin')->check()) {
            Auth::guard('admin')->login($admin);
        }

        if ($admin->hasVerifiedEmail()) {
            return redirect()->route('admin.dashboard')->with('status', 'Already verified.');
        }

        $admin->markEmailAsVerified();
        event(new Verified($admin));

        return redirect()->route('admin.dashboard')->with('status', 'Email verified!');
    })->middleware(['signed'])->name('admin.verification.verify');

    // 🔁 Resend Email Verification
    Route::post('/email/verification-notification', function (Request $request) {
        $request->user('admin')->sendEmailVerificationNotification();
        return back()->with('status', 'Verification link sent!');
    })->middleware(['auth:admin', 'throttle:6,1'])->name('admin.verification.send');
});


// ========================================================================
// 🔒 Protected Admin Routes (Requires Authentication)
// ========================================================================
Route::middleware(['auth:admin', 'verified'])->prefix('admin')->name('admin.')->group(function () {

    // 🧭 Dashboard
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // 📦 Products
    Route::resource('products', AdminProductController::class);
    Route::get('/products-export', [AdminProductController::class, 'export'])->name('products.export');

    // 🛒 Orders
    Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('/export-sales', [AdminDashboardController::class, 'exportSales'])->name('export.sales');
    Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::patch('/orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.updateStatus');

    // 👥 Users
    Route::resource('users', AdminUserController::class)->except(['show'])->names('users');
    Route::get('/users/{user}', [AdminUserController::class, 'show'])->name('users.show');
    Route::patch('/users/{user}/toggle-status', [AdminUserController::class, 'toggleStatus'])->name('users.toggleStatus');
    Route::patch('/users/{user}/toggle-ban', [AdminUserController::class, 'toggleBan'])->name('users.toggleBan');
    Route::get('/users/{user}/ban', [AdminUserController::class, 'showBanForm'])->name('users.ban.form');
    Route::post('/users/{user}/ban', [AdminUserController::class, 'ban'])->name('users.ban');
    Route::post('/users/{user}/unban', [AdminUserController::class, 'unban'])->name('users.unban');
    Route::get('/users-export', [AdminUserController::class, 'export'])->name('users.export');

    // 🌟 Reviews & Ratings
    Route::get('/reviews', [AdminReviewController::class, 'index'])->name('reviews.index');

    // 📊 Sales Reports
    Route::get('/reports', [AdminReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export', [AdminReportController::class, 'export'])->name('reports.export');

    // 🗂 Logs
    Route::get('/logs', [AdminLogController::class, 'index'])->name('logs.index');
    Route::get('/audit-logs', [AdminUserController::class, 'auditLogs'])->name('audit.logs');
    Route::get('/activity-logs', [AdminActivityLogController::class, 'index'])->name('activity.logs');

    // 👤 Admin Profile
    Route::get('/profile', [AdminProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile', [AdminProfileController::class, 'update'])->name('profile.update');

    // 🔑 Password
    Route::get('/password/change', [AdminProfileController::class, 'changePasswordForm'])->name('password.change');
    Route::post('/password/update', [AdminProfileController::class, 'updatePassword'])->name('password.update');

    // 🔔 Notifications
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::post('/mark-all-read', [NotificationController::class, 'markAllRead'])->name('markAllRead');
        Route::post('/{id}/mark-read', [NotificationController::class, 'markAsRead'])->name('markAsRead');
        Route::delete('/clear', [NotificationController::class, 'clearAll'])->name('clearAll');
    });

    Route::get('/notifications/json', [NotificationController::class, 'fetch'])->name('notifications.fetch');
});
