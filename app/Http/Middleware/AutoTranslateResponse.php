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
     * Translate HTML text nodes to the requested locale when it differs from the source content locale.
     */
    public function handle(Request $request, Closure $next)
    {
        /** @var Response $response */
        $response = $next($request);

        // Only process when locale is one of our supported languages
        $target = app()->getLocale();
        $supported = ['en', 'id'];
        if (!in_array($target, $supported)) {
            return $response;
        }

        // Skip when the requested locale matches the source content language
        $sourceLocale = config('app.content_locale', 'en');
        if ($target === $sourceLocale) {
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
        // Only translate inside <body> to avoid affecting head/meta/style assets
        $prefix = '';
        $bodyOpen = '';
        $bodyContent = $html;
        $bodyClose = '';
        $suffix = '';

        if (preg_match('/^(.*?)(<body[^>]*>)(.*)(<\/body>)(.*)$/is', $html, $m)) {
            $prefix = $m[1] ?? '';
            $bodyOpen = $m[2] ?? '';
            $bodyContent = $m[3] ?? '';
            $bodyClose = $m[4] ?? '';
            $suffix = $m[5] ?? '';
        }

        // Mask blocks we should not translate (script/style/pre/code/noscript)
        $placeholders = [];
        $maskTags = ['script', 'style', 'pre', 'code', 'noscript'];
        foreach ($maskTags as $tag) {
            $i = 0;
            $bodyContent = preg_replace_callback('/<' . $tag . '[^>]*>.*?<\/' . $tag . '>/is', function ($m) use (&$placeholders, $tag, &$i) {
                $key = "__MASK_" . strtoupper($tag) . "_" . ($i++);
                $placeholders[$key] = $m[0];
                return $key;
            }, $bodyContent);
        }

        $parts = preg_split('/<(.*?)>/s', $bodyContent, -1, PREG_SPLIT_DELIM_CAPTURE);
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

        // Reassemble
        return $prefix . $bodyOpen . $out . $bodyClose . $suffix;
    }

    protected function translateTextSegment(string $text, string $target): string
    {
        if (empty(trim($text))) {
            return $text;
        }

        // If text contains our mask placeholders, skip translating to avoid leaking markers
        if (strpos($text, '__MASK_') !== false) {
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
