<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

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
            $targetLang = app()->getLocale() ?: $this->defaultTargetLang;
        }

        if (empty($text)) {
            return $text;
        }

        $cacheKey = 'google_translate_' . md5($text . '|' . $targetLang);

        if (Cache::has($cacheKey)) {
            $cached = Cache::get($cacheKey);
            \Log::debug('GoogleTranslate CACHE HIT', ['input' => substr($text, 0, 50), 'output' => substr($cached, 0, 50), 'lang' => $targetLang]);
            return $cached;
        }

        try {
            $url = 'https://translate.googleapis.com/translate_a/single';
            $query = http_build_query([
                'client' => 'gtx',
                'sl' => $this->sourceLang,
                'tl' => $targetLang,
                'dt' => 't',
                'q' => $text,
            ]);

            \Log::debug('GoogleTranslate API CALL', ['input' => substr($text, 0, 50), 'lang' => $targetLang, 'url' => substr($url . '?' . $query, 0, 100)]);

            $body = $this->httpGet($url . '?' . $query);
            \Log::debug('GoogleTranslate API RESPONSE', ['status' => strlen($body), 'preview' => substr($body, 0, 100)]);

            $result = json_decode($body, true);

            if ($result && isset($result[0][0][0])) {
                $translated = $result[0][0][0];
                \Log::info('GoogleTranslate SUCCESS', ['input' => substr($text, 0, 50), 'output' => substr($translated, 0, 50), 'lang' => $targetLang]);
                Cache::put($cacheKey, $translated, now()->addDays(30));
                return $translated;
            }

            \Log::warning('GoogleTranslate NO TRANSLATION', ['input' => substr($text, 0, 50), 'result_structure' => json_encode(array_keys($result ?? [])), 'lang' => $targetLang]);
            return $text;
        } catch (\Throwable $e) {
            \Log::error('Google Translate Error: ' . $e->getMessage(), ['input' => substr($text, 0, 50)]);
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

    /**
     * Simple HTTP GET using cURL or file_get_contents fallback
     */
    protected function httpGet(string $url): string
    {
        // Prefer cURL
        if (function_exists('curl_init')) {
            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_CONNECTTIMEOUT => 5,
                CURLOPT_TIMEOUT => 8,
                CURLOPT_HTTPHEADER => [
                    'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                ],
            ]);
            $res = curl_exec($ch);
            if ($res === false) {
                $err = curl_error($ch);
                curl_close($ch);
                throw new \RuntimeException('cURL error: ' . $err);
            }
            curl_close($ch);
            return (string)$res;
        }

        // Fallback to file_get_contents
        $ctx = stream_context_create([
            'http' => [
                'method' => 'GET',
                'timeout' => 8,
                'header' => "User-Agent: Mozilla/5.0\r\n",
            ],
        ]);
        $res = @file_get_contents($url, false, $ctx);
        if ($res === false) {
            throw new \RuntimeException('HTTP GET failed');
        }
        return (string)$res;
    }
}
