<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->user();

        // Not authenticated
        if (!$user) {
            abort(401, 'Unauthorized.');
        }

        // No role on user record
        if (!$user->role) {
            abort(403, 'Unauthorized.');
        }

        if (!empty($roles) && !in_array($user->role, $roles)) {
            abort(403, 'Unauthorized.');
        }

        return $next($request);
    }
}
