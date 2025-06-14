<?php

namespace App\Http\Controllers\Farmer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FarmerNotificationController extends Controller
{
    // ✅ GET /farmer/notifications
    public function index(Request $request)
    {
        $filter = $request->query('filter');
        
        $notifications = Auth::user()
            ->notifications()
            ->when($filter, fn($q) => $q->where('data->type', $filter))
            ->latest()
            ->paginate(10);

        return view('farmer.notifications.index', compact('notifications'));
    }

    // ✅ POST /farmer/notifications/mark-all
public function markAll(Request $request)
{
    Auth::user()->unreadNotifications->markAsRead();

    return redirect()->route('farmer.notifications.index', $request->query())
        ->with('success', 'All notifications marked as read.');
}

}
