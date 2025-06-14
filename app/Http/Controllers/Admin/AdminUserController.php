<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\AdminActivityLog;
use App\Models\AuditLog;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::query()
            ->when($request->search, fn($q, $search) =>
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('role', 'like', "%{$search}%")
            )
            ->when($request->role, fn($q, $role) => $q->where('role', $role))
            ->when($request->status, function ($q, $status) {
                if ($status === 'banned') {
                    $q->where('is_banned', true);
                } else {
                    $q->where('status', $status);
                }
            })
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('admin.users.index', compact('users'));
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role'  => 'required|in:admin,farmer,buyer',
        ]);

        $fields = ['name', 'email', 'role'];
        $hasChanges = false;

        foreach ($fields as $field) {
            if ($user->$field !== $request->$field) {
                AuditLog::create([
                    'user_id'   => $user->id,
                    'admin_id'  => Auth::id(),
                    'field'     => $field,
                    'old_value' => $user->$field,
                    'new_value' => $request->$field,
                ]);
                $hasChanges = true;
            }
        }

        if ($hasChanges) {
            $user->update($request->only($fields));
            $this->logAdminActivity('Updated User', 'Updated user: ' . $user->email);
        }

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        if (Auth::id() === $user->id) {
            return redirect()->back()->with('error', 'You cannot delete your own account.');
        }

        AuditLog::create([
            'user_id'   => $user->id,
            'admin_id'  => Auth::id(),
            'field'     => 'deleted',
            'old_value' => $user->email,
            'new_value' => 'Deleted',
        ]);

        $user->delete();

        $this->logAdminActivity('Deleted User', 'Deleted user: ' . $user->email);

        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
    }

    public function toggleStatus(User $user)
    {
        $oldStatus = $user->status;
        $user->status = $user->status === 'active' ? 'inactive' : 'active';
        $user->save();

        AuditLog::create([
            'user_id'   => $user->id,
            'admin_id'  => Auth::id(),
            'field'     => 'status',
            'old_value' => $oldStatus,
            'new_value' => $user->status,
        ]);

        $this->logAdminActivity('Toggled Status', 'Changed status for user: ' . $user->email);

        return redirect()->back()->with('success', 'User status updated.');
    }

    public function toggleBan(User $user)
    {
        $oldStatus = $user->is_banned ? 'banned' : 'not banned';

        // If unbanning, clear ban-related fields
        if ($user->is_banned) {
            $user->is_banned = false;
            $user->is_permanently_banned = false;
            $user->ban_reason = null;
            $user->banned_until = null;

            $logDescription = 'Unbanned user: ' . $user->email;
            $auditNewValue = 'unbanned';
        } else {
            $user->is_banned = true;

            // Set temporary default (optional)
            $user->is_permanently_banned = false;
            $user->banned_until = now()->addDays(7);
            $user->ban_reason = 'Banned by toggleBan';

            $logDescription = 'Banned user (via toggle): ' . $user->email;
            $auditNewValue = 'banned (7 days)';
        }

        $user->save();

        // Log audit
        AuditLog::create([
            'user_id'   => $user->id,
            'admin_id'  => Auth::id(),
            'field'     => 'is_banned',
            'old_value' => $oldStatus,
            'new_value' => $auditNewValue,
        ]);

        // Log activity
        $this->logAdminActivity('Toggled Ban', $logDescription);

        return back()->with('success', 'User ' . ($user->is_banned ? 'banned' : 'unbanned') . ' successfully.');
    }


    public function show(User $user)
    {
        return view('admin.users.partials.details', compact('user'));
    }


    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'role'     => 'required|in:admin,farmer,buyer',
            'status'   => 'required|in:active,inactive',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'role'     => $request->role,
            'status'   => $request->status,
            'password' => bcrypt($request->password),
        ]);

        event(new Registered($user));

        $this->logAdminActivity('Created User', 'Created user: ' . $user->email);

        return redirect()->route('admin.users.index')->with('success', 'User created and verification email sent.');
    }

    public function export(Request $request)
    {
        $users = User::query()
            ->when($request->search, fn($q, $search) =>
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('role', 'like', "%{$search}%")
            )
            ->when($request->role, fn($q, $role) => $q->where('role', $role))
            ->when($request->status, function ($q, $status) {
                if ($status === 'banned') {
                    $q->where('is_banned', true);
                } else {
                    $q->where('status', $status);
                }
            })
            ->orderBy('id', 'desc')
            ->get();

        $csvHeader = ['ID', 'Name', 'Email', 'Role', 'Status', 'Banned', 'Created At'];
        $filename = 'users_export_' . now()->format('Ymd_His') . '.csv';

        $csvContent = implode(',', $csvHeader) . "\n";
        foreach ($users as $user) {
            $csvContent .= implode(',', [
                $user->id,
                $user->name,
                $user->email,
                $user->role,
                $user->status,
                $user->is_banned ? 'Yes' : 'No',
                $user->created_at->format('Y-m-d H:i:s'),
            ]) . "\n";
        }

        $this->logAdminActivity('Exported Users', 'Exported user list');

        return Response::make($csvContent, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$filename}",
        ]);
    }

    public function auditLogs()
    {
        $logs = AuditLog::with('user', 'admin')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.logs.audit', compact('logs'));
    }

    protected function logAdminActivity($action, $description = null)
    {
        AdminActivityLog::create([
            'admin_id'    => Auth::id(),
            'action'      => $action,
            'description' => $description,
            'ip_address'  => request()->ip(),
            'user_agent'  => request()->userAgent(),
        ]);
    }

    public function showBanForm(User $user)
    {
        return view('admin.users.ban', compact('user'));
    }

    public function ban(Request $request, User $user)
    {
        $request->validate([
            'reason' => 'required|string|max:1000',
            'duration' => 'required|in:7,21,permanent',
        ]);

        $user->is_banned = true;
        $user->ban_reason = $request->reason;

        if ($request->duration === 'permanent') {
            $user->is_permanently_banned = true;
            $user->banned_until = null;
        } else {
            $user->is_permanently_banned = false;
            $user->banned_until = now()->addDays((int) $request->duration);
        }

        $user->save();

        // Create audit log
        AuditLog::create([
            'user_id'   => $user->id,
            'admin_id'  => Auth::id(),
            'field'     => 'is_banned',
            'old_value' => 'not banned',
            'new_value' => $request->duration === 'permanent'
                            ? 'permanent ban'
                            : 'temporary ban (' . $request->duration . ' days)',
        ]);

        // Create activity log
        $this->logAdminActivity('Banned User', 'Banned user: ' . $user->email . ' | Reason: ' . $request->reason);

        return redirect()->route('admin.users.index')->with('success', 'User has been banned.');
    }

    public function unban(User $user)
    {
    $user->is_banned = false;
    $user->is_permanently_banned = false;
    $user->banned_until = null;
    $user->ban_reason = null;
    $user->save();

    AuditLog::create([
        'user_id'   => $user->id,
        'admin_id'  => Auth::id(),
        'field'     => 'ban',
        'old_value' => 'banned',
        'new_value' => 'unbanned',
    ]);

    $this->logAdminActivity('Lifted Ban', 'Lifted ban for user: ' . $user->email);

    return redirect()->route('admin.users.index')->with('success', 'User unbanned successfully.');
    }



}
