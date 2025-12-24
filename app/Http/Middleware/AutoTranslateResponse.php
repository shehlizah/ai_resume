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
        
        // Only translate when locale is English
        if ($locale !== 'en') {
            return $response->header('X-Translation-Skipped', 'not-english-locale-' . $locale);
        }

        try {
            // Get the content
            $html = $response->getContent();
            
            // Must be a string and have content
            if (!is_string($html) || trim($html) === '') {
                return $response->header('X-Translation-Skipped', 'empty-or-not-string');
            }

            // Must contain HTML
            if (strpos($html, '<') === false || strpos($html, '>') === false) {
                return $response->header('X-Translation-Skipped', 'no-html-tags');
            }

            // Check content type - but don't skip if it looks like HTML anyway
            $contentType = $response->headers->get('Content-Type');
            $isHtml = ($contentType && stripos($contentType, 'text/html') !== false) ||
                     (strpos($html, '<!DOCTYPE') !== false) ||
                     (strpos($html, '<html') !== false);

            if (!$isHtml) {
                return $response->header('X-Translation-Skipped', 'not-html-content-type');
            }

            // Translate the HTML
            \Log::info('AutoTranslate START', ['locale' => $locale, 'length' => strlen($html)]);
            $translatedHtml = $this->translateTextInHtml($html, 'en');
            
            if ($translatedHtml !== null && $translatedHtml !== $html) {
                $response->setContent($translatedHtml);
                $response->header('X-Translated', 'yes');
                \Log::info('AutoTranslate COMPLETED');
            } else {
                $response->header('X-Translated', 'no-changes');
                \Log::info('AutoTranslate completed but no changes');
            }
        } catch (\Throwable $e) {
            \Log::error('AutoTranslate ERROR', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            $response->header('X-Translate-Error', substr($e->getMessage(), 0, 100));
        }

        return $response;
    }

    protected function translateTextInHtml(string $html, string $target): ?string
    {
        \Log::info('translateTextInHtml START', ['html_length' => strlen($html), 'target' => $target]);

        // Step 1: Protect code blocks
        $protected = [];
        $pid = 0;
        
        foreach (['script', 'style', 'pre', 'code', 'textarea', 'noscript'] as $tag) {
            $pattern = '/<' . preg_quote($tag) . '[^>]*>.*?<\/' . preg_quote($tag) . '>/is';
            $html = preg_replace_callback($pattern, function ($m) use (&$protected, &$pid) {
                $key = "___PROT{$pid}___";
                $protected[$key] = $m[0];
                \Log::debug("Protected block $pid: " . strlen($m[0]) . ' bytes');
                $pid++;
                return $key;
            }, $html);
        }
        
        \Log::info('Protections applied', ['count' => count($protected)]);

        // Step 2: Split HTML into text and tag parts
        $parts = preg_split('/(>|<)/', $html, -1, PREG_SPLIT_DELIM_CAPTURE);
        if (!is_array($parts)) {
            \Log::warning('preg_split failed');
            return null;
        }

        \Log::info('HTML split', ['parts' => count($parts)]);

        $output = [];
        $inTag = false;
        $translatedCount = 0;

        for ($i = 0; $i < count($parts); $i++) {
            $part = $parts[$i];

            // Odd indices are delimiters
            if ($i % 2 === 1) {
                $output[] = $part;
                if ($part === '<') {
                    $inTag = true;
                } elseif ($part === '>') {
                    $inTag = false;
                }
            } else {
                // Even indices are content
                if ($inTag) {
                    // Tag content - don't translate
                    $output[] = $part;
                } else {
                    // Text content
                    $trimmed = trim($part);
                    
                    // Skip empty or protected
                    if ($trimmed === '' || strpos($part, '___PROT') !== false) {
                        $output[] = $part;
                    } else {
                        // Translate this text
                        $translatedCount++;
                        preg_match('/^(\s*)(.*?)(\s*)$/s', $part, $m);
                        $lead = $m[1] ?? '';
                        $text = $m[2] ?? $trimmed;
                        $trail = $m[3] ?? '';

                        if (trim($text)) {
                            \Log::debug("Translating segment $translatedCount", ['text' => substr($text, 0, 40)]);
                            $translated = $this->translator->translate($text, $target);
                            $output[] = $lead . $translated . $trail;
                            \Log::debug("Translated result", ['result' => substr($translated, 0, 40)]);
                        } else {
                            $output[] = $part;
                        }
                    }
                }
            }
        }

        $result = implode('', $output);

        // Step 3: Restore protected blocks
        foreach ($protected as $key => $val) {
            $result = str_replace($key, $val, $result);
        }

        \Log::info('translateTextInHtml COMPLETE', ['translated_segments' => $translatedCount, 'result_length' => strlen($result)]);
        return $result;
    }
}
