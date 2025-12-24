<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use GuzzleHttp\Client;

class GoogleTranslateService
{
    protected $client;
    // Auto-detect source language to handle mixed English/Indonesian content
    protected $sourceLang = 'auto';
    protected $defaultTargetLang = 'en';

    public function __construct()
    {
        $this->client = new Client();
    }

    /**
     * Translate text using Google Translate API (free endpoint)
     */
    public function translate($text, $targetLang = null)
    {
        if (!$targetLang) {
            // Default to current app locale if not provided
            $targetLang = app()->getLocale() ?: $this->defaultTargetLang;
        }

        if (empty($text)) {
            return $text;
        }

        // Cache the translation for performance
        $cacheKey = 'google_translate_' . md5($text . '|' . $targetLang);

        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        try {
            // Using Google Translate free API endpoint
            $response = $this->client->get('https://translate.googleapis.com/translate_a/single', [
                'query' => [
                    'client' => 'gtx',
                    'sl' => $this->sourceLang,
                    'tl' => $targetLang,
                    'dt' => 't',
                    'q' => $text,
                ]
            ]);

            $result = json_decode($response->getBody(), true);

            if ($result && isset($result[0][0][0])) {
                $translated = $result[0][0][0];
                // Cache for 30 days
                Cache::put($cacheKey, $translated, now()->addDays(30));
                return $translated;
            }

            return $text;
        } catch (\Exception $e) {
            // If translation fails, return original text
            \Log::error('Google Translate Error: ' . $e->getMessage());
            return $text;
        }
    }

    /**
     * Translate multiple texts at once
     */
    public function translateArray(array $texts, $targetLang = null)
    {
        return array_map(fn($text) => $this->translate($text, $targetLang), $texts);
    }

    /**
     * Check if current locale is English
     */
    public static function isEnglish()
    {
        return app()->getLocale() === 'en';
    }

    /**
     * Get target language
     */
    public static function getTargetLang()
    {
        return app()->getLocale() === 'en' ? 'en' : 'id';
    }
}
