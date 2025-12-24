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
        $supportedLocales = ['en', 'id'];

        // 1) Query parameter wins and persists (session + 1-year cookie)
        if ($request->has('lang') && in_array($request->get('lang'), $supportedLocales)) {
            $locale = $request->get('lang');
            session(['locale' => $locale]);
            // queue a long-lived cookie (minutes)
            cookie()->queue(cookie('locale', $locale, 60 * 24 * 365));
        } else {
            // 2) Fall back to session -> cookie
            $locale = session('locale') ?? $request->cookie('locale');

            // 3) If still not set, auto-detect from browser Accept-Language
            if (!$locale) {
                $preferred = $request->getPreferredLanguage($supportedLocales);
                $locale = $preferred ?: config('app.locale', 'id');
            }

            // 4) Final default
            if (!$locale) {
                $locale = config('app.locale', 'id');
            }
        }

        if (!in_array($locale, $supportedLocales)) {
            $locale = config('app.locale', 'id');
        }

        // Set the locale
        app()->setLocale($locale);

        return $next($request);
    }
}
