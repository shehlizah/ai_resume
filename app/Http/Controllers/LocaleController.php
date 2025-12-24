<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LocaleController extends Controller
{
    public function setLocale($locale)
    {
        // Validate locale
        $supportedLocales = ['en', 'id'];
        if (!in_array($locale, $supportedLocales)) {
            $locale = 'en';
        }

        // Set locale in session
        session(['locale' => $locale]);

        // Also set in cookie for persistence
        cookie()->queue('locale', $locale, 60 * 24 * 365); // 1 year

        // Set locale immediately
        app()->setLocale($locale);

        // Redirect back to referrer or home
        return redirect()->back();
    }
}
