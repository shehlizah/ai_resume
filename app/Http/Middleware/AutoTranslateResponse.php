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
        $response = $next($request);

        $locale = app()->getLocale();
        \Log::info('AutoTranslateResponse.handle called', ['locale' => $locale, 'path' => $request->path()]);

        if ($locale !== 'en') {
            \Log::info('AutoTranslateResponse skipped - not English', ['locale' => $locale]);
            return $response;
        }

        \Log::info('AutoTranslateResponse processing English locale');

        // Only process normal HTML responses
        $contentType = $response->headers->get('Content-Type');
        $isHtml = $contentType && stripos($contentType, 'text/html') !== false;

        $html = $response->getContent();
        if (!is_string($html) || trim($html) === '') {
            \Log::info('AutoTranslateResponse skipped - not string or empty');
            return $response;
        }

        if (!$isHtml) {
            // Heuristic: if content contains HTML tags, treat as HTML
            if (strpos($html, '<') === false || strpos($html, '>') === false) {
                \Log::info('AutoTranslateResponse skipped - no HTML tags');
                return $response;
            }
        }

        try {
            \Log::info('AutoTranslateResponse attempting translation', ['html_length' => strlen($html), 'content_type' => $contentType]);
            $translatedHtml = $this->translateTextInHtml($html, 'en');
            if ($translatedHtml !== null) {
                $response->setContent($translatedHtml);
                $response->headers->set('X-Translated', 'yes');
                $response->headers->set('X-Target-Locale', 'en');
                \Log::info('AutoTranslateResponse completed', ['result_length' => strlen($translatedHtml)]);
            }
        } catch (\Throwable $e) {
            \Log::error('AutoTranslateResponse error', ['message' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine()]);
        }

        return $response;
    }

    protected function translateTextInHtml(string $html, string $target): ?string
    {
        \Log::info('AutoTranslateResponse START', ['target' => $target, 'html_length' => strlen($html)]);

        // Step 1: Protect protected blocks (script, style, code, etc)
        $protected = [];
        $pid = 0;
        foreach (['script', 'style', 'pre', 'code', 'textarea', 'noscript'] as $tag) {
            $html = preg_replace_callback(
                '/<' . preg_quote($tag) . '[^>]*>.*?<\/' . preg_quote($tag) . '>/is',
                function ($m) use (&$protected, &$pid) {
                    $key = "___PROT{$pid}___";
                    $protected[$key] = $m[0];
                    $pid++;
                    return $key;
                },
                $html
            );
        }
        \Log::info('AutoTranslateResponse PROTECTED', ['blocks' => count($protected)]);

        // Step 2: Split by < and > using the delimiters as separators
        // This creates: [text, <, tag_content, >, text, <, tag_content, >, text, ...]
        // Odd indices (1,3,5...) are delimiters: < and >
        // Even indices (0,2,4...) are content between delimiters
        $parts = preg_split('/(>|<)/', $html, -1, PREG_SPLIT_DELIM_CAPTURE);
        \Log::info('AutoTranslateResponse SPLIT', ['total_parts' => count($parts)]);

        $output = [];
        $inTag = false;  // Tracks if we're currently inside < ... >
        $textCount = 0;

        for ($i = 0; $i < count($parts); $i++) {
            $part = $parts[$i];
            
            // Delimiters appear at odd indices
            if ($i % 2 === 1) {
                // This is a delimiter: < or >
                $output[] = $part;
                if ($part === '<') {
                    $inTag = true;
                } else {
                    $inTag = false;
                }
            } else {
                // This is content between delimiters
                if ($inTag) {
                    // This is tag content, don't translate
                    $output[] = $part;
                } else {
                    // This is text outside tags
                    if (trim($part) === '' || strpos($part, '___PROT') !== false) {
                        // Empty or protected, pass through
                        $output[] = $part;
                    } else {
                        // Translate this text segment
                        $textCount++;
                        preg_match('/^(\s*)(.*?)(\s*)$/s', $part, $m);
                        $lead = $m[1] ?? '';
                        $text = $m[2] ?? '';
                        $trail = $m[3] ?? '';
                        if (trim($text)) {
                            \Log::debug('AutoTranslateResponse TRANSLATE', ['text' => substr($text, 0, 50), 'length' => strlen($text)]);
                            $translated = $this->translator->translate($text, $target);
                            \Log::debug('AutoTranslateResponse RESULT', ['original' => substr($text, 0, 50), 'translated' => substr($translated, 0, 50), 'same' => $text === $translated]);
                            $output[] = $lead . $translated . $trail;
                        } else {
                            $output[] = $part;
                        }
                    }
                }
            }
        }

        \Log::info('AutoTranslateResponse TRANSLATED', ['text_chunks' => $textCount]);

        $result = implode('', $output);

        // Step 3: Restore protected blocks
        foreach ($protected as $key => $val) {
            $result = str_replace($key, $val, $result);
        }

        \Log::info('AutoTranslateResponse END', ['result_length' => strlen($result)]);
        return $result;
    }
}
