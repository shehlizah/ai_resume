<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PopupGuideMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only check for authenticated users
        if (auth()->check()) {
            $user = auth()->user();

            // Only check once per session to avoid showing popup repeatedly
            if (!session()->has('popup_checked_this_session')) {
                $nextStep = $user->getNextStepPopup();

                if ($nextStep) {
                    // Store the next step popup to show
                    session(['show_next_step_popup' => $nextStep]);
                }

                // Mark that we've checked for this session
                session(['popup_checked_this_session' => true]);
            }

            // If a module was just completed, show the next step popup
            if (session()->has('module_completed')) {
                $completedModule = session()->pull('module_completed');
                $nextStep = $user->getNextStepPopup();

                if ($nextStep) {
                    session(['show_next_step_popup' => $nextStep]);
                }
            }
        }

        return $next($request);
    }
}
