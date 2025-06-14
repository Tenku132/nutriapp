<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminActivityLog;
use Illuminate\Http\Request;

class AdminActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $logs = AdminActivityLog::with('admin') // assuming relationship to User model
            ->orderBy('created_at', 'desc')
            ->paginate(15);
            
            return view('admin.logs.activity', compact('logs'));

    }
}
