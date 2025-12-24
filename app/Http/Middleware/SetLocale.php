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
        // Get locale from session first, then cookie, then default
        $locale = session('locale') ?? 
                  $request->cookie('locale') ?? 
                  config('app.locale', 'en');

        // Validate locale
        $supportedLocales = ['en', 'id'];
        if (!in_array($locale, $supportedLocales)) {
            $locale = 'en';
        }

        // Set the locale
        app()->setLocale($locale);

        return $next($request);
    }
}
