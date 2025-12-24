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

        // Only process when current locale is English
        if (app()->getLocale() !== 'en') {
            return $response;
        }

        // Only process normal HTML responses
        $contentType = $response->headers->get('Content-Type');
        if (!$response instanceof Response || !$contentType || stripos($contentType, 'text/html') === false) {
            return $response;
        }

        $html = $response->getContent();
        if (!is_string($html) || trim($html) === '') {
            return $response;
        }

        try {
            $translatedHtml = $this->translateTextInHtml($html);
            if ($translatedHtml !== null) {
                $response->setContent($translatedHtml);
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('AutoTranslateResponse: ' . $e->getMessage());
        }

        return $response;
    }

    protected function translateTextInHtml(string $html): ?string
    {
        $parts = preg_split('/<(.*?)>/s', $html, -1, PREG_SPLIT_DELIM_CAPTURE);
        if (!is_array($parts)) {
            return null;
        }

        $result = [];
        foreach ($parts as $i => $part) {
            if ($i % 2 === 0) {
                // Even indexes: text between tags
                $result[$i] = $this->translateTextSegment($part);
            } else {
                // Odd indexes: tags themselves
                $result[$i] = '<' . $part . '>';
            }
        }

        return implode('', $result);
    }

    protected function translateTextSegment(string $text): string
    {
        if (empty(trim($text))) {
            return $text;
        }

        preg_match('/^(\s*)(.*?)(\s*)$/s', $text, $m);
        $leading = $m[1] ?? '';
        $core = $m[2] ?? $text;
        $trailing = $m[3] ?? '';

        if (empty(trim($core))) {
            return $text;
        }

        $translated = $this->translator->translate($core, 'en');
        return $leading . $translated . $trailing;
    }
}
