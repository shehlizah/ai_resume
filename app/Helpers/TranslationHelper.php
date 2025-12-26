<?php

namespace App\Helpers;

use App\Services\GoogleTranslateService;

class TranslationHelper
{
    protected static $translator;

    public static function getInstance()
    {
        if (!self::$translator) {
            self::$translator = new GoogleTranslateService();
        }
        return self::$translator;
    }

    /**
     * Translate text when the active locale differs from the source content locale.
     */
    public static function trans($text)
    {
        $targetLocale = app()->getLocale();
        $sourceLocale = config('app.content_locale', 'en');

        // Nothing to do if the target matches the source language
        if (!$targetLocale || $targetLocale === $sourceLocale) {
            return $text;
        }

        // Only translate between supported locales for now
        if (!in_array($targetLocale, ['en', 'id'])) {
            return $text;
        }

        return self::getInstance()->translate($text, $targetLocale);
    }

    /**
     * Alias for trans() - shorter syntax
     */
    public static function t($text)
    {
        return self::trans($text);
    }
}
