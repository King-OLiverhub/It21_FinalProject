<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminUserController extends Controller
{
    /**
     * Display a listing of registered users and their login activity logs.
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $role = $request->get('role');

        $usersQuery = User::query()->orderBy('created_at', 'desc');

        if ($search) {
            $usersQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($role) {
            $usersQuery->where('role', $role);
        }

        $users = $usersQuery->paginate(10)->withQueryString();

        // Get recent login / registration activity logs
        $activityLogs = UserActivityLog::with('user')
            ->orderBy('activity_at', 'desc')
            ->paginate(15, ['*'], 'activity_page')
            ->withQueryString();

        // Get stats for user overview cards
        $totalUsers = User::count();
        $activeUsers = User::where('is_active', true)->count();
        $adminCount = User::where('role', User::ROLE_ADMIN)->count();
        $analystCount = User::where('role', User::ROLE_SECURITY_ANALYST)->count();
        $netAdminCount = User::where('role', User::ROLE_NETWORK_ADMIN)->count();

        return view('admin.users', compact(
            'users', 'activityLogs', 'search', 'role',
            'totalUsers', 'activeUsers', 'adminCount', 'analystCount', 'netAdminCount'
        ));
    }

    /**
     * Toggle a user's active status (Activate / Deactivate).
     */
    public function toggleStatus($id)
    {
        $user = User::findOrFail($id);

        // Prevent self-deactivation
        if ($user->id === Auth::id()) {
            return back()->with('error', 'Security Guard: You cannot deactivate your own administrator account.');
        }

        $user->is_active = !$user->is_active;
        $user->save();

        // Log the administrative action
        UserActivityLog::create([
            'user_id' => Auth::id(),
            'activity_type' => 'Admin Action',
            'ip_address' => request()->ip(),
            'details' => ($user->is_active ? 'Activated' : 'Deactivated') . ' account for user: ' . $user->email,
            'activity_at' => now()
        ]);

        $statusStr = $user->is_active ? 'activated' : 'deactivated';
        return back()->with('success', "User account for {$user->name} has been successfully {$statusStr}.");
    }
}
