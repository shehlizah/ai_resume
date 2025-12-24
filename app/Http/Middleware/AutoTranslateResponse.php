<?php

namespace App\Http\Middleware;

use App\Services\GoogleTranslateService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AutoTranslateResponse
{
    protected GoogleTranslateService $translator;

    public function __construct(GoogleTranslateService $translator)
    {
        $this->translator = $translator;
    }

    /**
     * Translate HTML text nodes to English when locale is 'en'.
     */
    public function handle(Request $request, Closure $next)
    {
        /** @var Response $response */
        $response = $next($request);

        // Only translate when target locale is English to avoid design changes
        $target = app()->getLocale();
        if ($target !== 'en') {
            return $response;
        }

        // Only process normal HTML responses
        $contentType = $response->headers->get('Content-Type');
        // Process when HTML or when content looks like HTML even if header absent
        $isHtml = $contentType && stripos($contentType, 'text/html') !== false;

        $html = $response->getContent();
        if (!is_string($html) || trim($html) === '') {
            return $response;
        }

        if (!$isHtml) {
            // Heuristic: if content contains HTML tags, treat as HTML
            if (strpos($html, '<') === false || strpos($html, '>') === false) {
                return $response;
            }
        }

        try {
            $translatedHtml = $this->translateTextInHtml($html, $target);
            if ($translatedHtml !== null) {
                $response->setContent($translatedHtml);
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('AutoTranslateResponse: ' . $e->getMessage());
        }

        return $response;
    }

    protected function translateTextInHtml(string $html, string $target): ?string
    {
        // Mask blocks we should not translate (script/style/pre/code/noscript/textarea/comments)
        $placeholders = [];
        $makeToken = function (string $content): string {
            // Use hex token to avoid translation of words like STYLE -> GAYA
            return '__TKN_' . substr(md5($content), 0, 16) . '__';
        };

        $maskTags = ['script', 'style', 'pre', 'code', 'noscript', 'textarea'];
        foreach ($maskTags as $tag) {
            $html = preg_replace_callback('/<' . $tag . '[^>]*>.*?<\/' . $tag . '>/is', function ($m) use (&$placeholders, $makeToken) {
                $key = $makeToken($m[0]);
                $placeholders[$key] = $m[0];
                return $key;
            }, $html);
        }

        // Mask HTML comments
        $html = preg_replace_callback('/<!--.*?-->/is', function ($m) use (&$placeholders, $makeToken) {
            $key = $makeToken($m[0]);
            $placeholders[$key] = $m[0];
            return $key;
        }, $html);

        $parts = preg_split('/<(.*?)>/s', $html, -1, PREG_SPLIT_DELIM_CAPTURE);
        if (!is_array($parts)) {
            return null;
        }

        $result = [];
        foreach ($parts as $i => $part) {
            if ($i % 2 === 0) {
                // Even indexes: text between tags
                $result[$i] = $this->translateTextSegment($part, $target);
            } else {
                // Odd indexes: tags themselves
                $result[$i] = '<' . $part . '>';
            }
        }
        $out = implode('', $result);

        // Restore masked blocks
        foreach ($placeholders as $key => $value) {
            $out = str_replace($key, $value, $out);
        }

        return $out;
    }

    protected function translateTextSegment(string $text, string $target): string
    {
        if (empty(trim($text))) {
            return $text;
        }

        // Skip segments containing our non-translatable tokens
        if (strpos($text, '__TKN_') !== false) {
            return $text;
        }

        preg_match('/^(\s*)(.*?)(\s*)$/s', $text, $m);
        $leading = $m[1] ?? '';
        $core = $m[2] ?? $text;
        $trailing = $m[3] ?? '';

        if (empty(trim($core))) {
            return $text;
        }

        $translated = $this->translator->translate($core, $target);
        return $leading . $translated . $trailing;
    }
}
