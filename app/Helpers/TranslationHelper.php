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
     * Translate text (only if language is English)
     * Otherwise return original Indonesian text
     */
    public static function trans($text)
    {
        $translator = self::getInstance();
        
        // If current locale is English, translate from Indonesian to English
        if (app()->getLocale() === 'en') {
            return $translator->translate($text, 'en');
        }
        
        // Otherwise return as is (Indonesian is default)
        return $text;
    }

    /**
     * Alias for trans() - shorter syntax
     */
    public static function t($text)
    {
        return self::trans($text);
    }
}
