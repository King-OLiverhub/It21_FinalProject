<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        
        // If no roles specified, allow access
        if (empty($roles)) {
            return $next($request);
        }

        // Check if user has any of the specified roles
        if ($user->hasAnyRole($roles)) {
            return $next($request);
        }

        // User doesn't have required role
        abort(403, 'Unauthorized access. You do not have the required role.');
    }
}