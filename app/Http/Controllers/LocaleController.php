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
        app()->setLocale($locale);

        // Redirect back to referrer or home
        return redirect()->back()->with('locale_changed', __('messages.language') . ' changed to ' . ucfirst($locale));
    }
}
