<?php

namespace App\Http\Controllers\Admin\Auth;

use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class AdminForgotPasswordController extends Controller
{
    public function showLinkRequestForm()
    {
        return view('admin.auth.forgot-password');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);
    
        $status = $this->broker()->sendResetLink(
            $request->only('email')
        );

        Log::info('Admin password reset link sent', [
            'email' => $request->email,
            'timestamp' => now()->toDateTimeString(),
        ]);
    
        return $status === Password::RESET_LINK_SENT
            ? back()->with(['status' => __($status)])
            : back()->withErrors(['email' => __($status)]);
    }

    // 👇 This ensures the 'admins' broker is used
    public function broker()
    {
        return Password::broker('admins');
    }
}
