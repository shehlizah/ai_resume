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

        // Force Indonesian as the primary locale unless an explicit, allowed query param is provided
        $locale = 'id';

        if ($request->has('lang') && in_array($request->get('lang'), $supportedLocales)) {
            $locale = $request->get('lang');
            session(['locale' => $locale]);
            cookie()->queue(cookie('locale', $locale, 60 * 24 * 365));
        } else {
            // Clear any stored locale preference so pages default to Indonesian
            session()->forget('locale');
            cookie()->queue(cookie('locale', null, -60));
        }

        app()->setLocale($locale);

        return $next($request);
    }
}
