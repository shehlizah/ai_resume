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

            // If a module was just completed, immediately show the next step popup
            if (session()->has('module_completed')) {
                $completedModule = session()->pull('module_completed');

                // Persist lightweight progress markers in session for later checks
                if ($completedModule === 'job_search') {
                    session(['job_search_completed' => true]);
                }

                if ($completedModule === 'book_session') {
                    session(['book_session_completed' => true]);
                }

                $nextStep = $user->getNextStepPopup();

                if ($nextStep) {
                    session(['show_next_step_popup' => $nextStep]);
                    // Reset the checked flag so popup shows after redirect
                    session()->forget('popup_checked_this_session');
                }
            }
            // Check on any page load if we haven't checked this session yet
            else if (!session()->has('popup_checked_this_session')) {
                $nextStep = $user->getNextStepPopup();

                if ($nextStep) {
                    // Store the next step popup to show
                    session(['show_next_step_popup' => $nextStep]);
                }

                // Mark that we've checked for this session
                session(['popup_checked_this_session' => true]);
            }
        }

        return $next($request);
    }
}
