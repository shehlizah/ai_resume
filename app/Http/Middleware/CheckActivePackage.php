<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckActivePackage
{
    public function handle($request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // CHECK YOUR DATABASE STRUCTURE:
        // Option 1: user_packages table
        if (!$user->activePackage()->exists()) {
            return redirect()->route('packages')
                ->with('error', 'You need an active package to download resumes.');
        }

        // Option 2: package_id on users table
        // if (!$user->package_id || !$user->package_expires_at?->isFuture()) {
        //     return redirect()->route('user.packages.index')
        //         ->with('error', 'You need an active package.');
        // }

        return $next($request);
    }
}