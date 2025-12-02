<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\Models\UserSubscription;

class CheckActivePackage
{
    public function handle($request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Check if user has lifetime access - bypass all checks
        if ($user->has_lifetime_access) {
            return $next($request);
        }

        // Check for active subscription
        $subscription = UserSubscription::where('user_id', $user->id)
            ->where('status', 'active')
            ->first();

        if ($subscription) {
            return $next($request);
        }

        // No access - redirect to pricing
        return redirect()->route('user.pricing')
            ->with('error', 'This feature requires a Pro subscription. Upgrade now to unlock all features!');
    }
}
