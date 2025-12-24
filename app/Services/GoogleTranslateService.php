<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GoogleTranslateService
{
    // Auto-detect source language to handle mixed English/Indonesian content
    protected $sourceLang = 'auto';
    protected $defaultTargetLang = 'en';

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
            // Using Google Translate free API endpoint with retry + UA for better reliability
            $response = Http::withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (compatible; ai-resume-bot/1.0)',
                ])
                ->timeout(10)
                ->retry(2, 500)
                ->get('https://translate.googleapis.com/translate_a/single', [
                    'client' => 'gtx',
                    'sl' => $this->sourceLang,
                    'tl' => $targetLang,
                    'dt' => 't',
                    'q' => $text,
                ]);

            if ($response->successful()) {
                $result = $response->json();
                $translated = $this->extractTranslation($result);
                if ($translated !== null) {
                    // Cache for 30 days
                    Cache::put($cacheKey, $translated, now()->addDays(30));
                    return $translated;
                }
            }

            Log::warning('Google Translate: unexpected response', [
                'status' => $response->status(),
                'body' => substr($response->body(), 0, 500),
            ]);
            return $text;
        } catch (\Throwable $e) {
            // If translation fails, return original text
            Log::error('Google Translate Error: ' . $e->getMessage());
            return $text;
        }
    }

    /**
     * Extract the translated string from Google free API structure.
     */
    protected function extractTranslation($result): ?string
    {
        if (!is_array($result) || !isset($result[0]) || !is_array($result[0])) {
            return null;
        }

        $translated = '';
        foreach ($result[0] as $chunk) {
            if (is_array($chunk) && isset($chunk[0])) {
                $translated .= $chunk[0];
            }
        }

        return $translated !== '' ? $translated : null;
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
