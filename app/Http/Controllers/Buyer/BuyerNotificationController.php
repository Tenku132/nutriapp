<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class BuyerNotificationController extends Controller
{
    public function index()
    {
        $notifications = Auth::user()->notifications()->paginate(10);
        return view('buyer.notifications.index', compact('notifications'));
    }
    public function markAllAsReadAjax(Request $request)
{
    Auth::user()->unreadNotifications->markAsRead();

    return response()->json(['success' => true]);
}
}
