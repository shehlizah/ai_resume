<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetLocale
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Check query parameter first (for language switching)
        if ($request->has('lang') && in_array($request->get('lang'), ['en', 'id'])) {
            $locale = $request->get('lang');
            session(['locale' => $locale]);
        } else {
            // Fall back to session -> cookie -> default
            $locale = session('locale') ??
                      $request->cookie('locale') ??
                      config('app.locale', 'id');
        }

        // Validate locale
        $supportedLocales = ['en', 'id'];
        if (!in_array($locale, $supportedLocales)) {
            $locale = config('app.locale', 'id');
        }

        // Set the locale
        app()->setLocale($locale);

        return $next($request);
    }
}
