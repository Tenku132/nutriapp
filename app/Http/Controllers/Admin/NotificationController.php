<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class NotificationController extends Controller
{
    public function markAllRead(Request $request)
    {
        auth()->user()->unreadNotifications->markAsRead();

        return back()->with('success', 'All notifications marked as read.');
    }

    // GET: Fetch recent notifications for dropdown
public function fetch()
{
    $admin = auth('admin')->user();

    if (!$admin) {
        return response()->json([], 401); // Not logged in
    }

    return response()->json($admin->notifications);
}


public function index()
{
    $admin = Auth::guard('admin')->user();

    if (!$admin) {
        return response()->json([], 401);
    }

    return response()->json($admin->notifications);
}

    // POST: Mark one notification as read by ID
    public function markAsRead($id)
    {
        $notification = auth()->user()
            ->notifications()
            ->where('id', $id)
            ->first();

        if ($notification && is_null($notification->read_at)) {
            $notification->markAsRead();
        }

        return response()->json(['status' => 'success']);
    }
    
    public function clearAll()
    {
        auth()->user()->notifications()->delete();

        return response()->json(['status' => 'cleared']);
    }


}
