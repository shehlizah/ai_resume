<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureUserRole
{
    /**
     * Handle an incoming request.
     * 
     * Usage in routes:
     * Route::middleware(['auth', 'role:user'])->group(...)
     */
    public function handle(Request $request, Closure $next, string $role = null)
    {
        if (!auth()->check()) {
            abort(401);
        }

        $user = auth()->user();

        if ($role && $user->role !== $role) {
            abort(403, "User role '{$user->role}' is not authorized to access '{$role}' resources.");
        }

        return $next($request);
    }
}
