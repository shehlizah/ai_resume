<?php

namespace App\Http\Middleware;

use App\Services\GoogleTranslateService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AutoTranslateResponse
{
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

        // Load HTML into DOM and walk text nodes
        $translatedHtml = $this->translateHtml($html);
        if ($translatedHtml) {
            $response->setContent($translatedHtml);
        }

        return $response;
    }

    protected function translateHtml(string $html): ?string
    {
        try {
            // Ensure proper UTF-8 handling
            $doc = new \DOMDocument('1.0', 'UTF-8');
            libxml_use_internal_errors(true);
            $doc->loadHTML('<?xml encoding="utf-8" ?>' . $html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
            libxml_clear_errors();

            $xpath = new \DOMXPath($doc);

            // Collect text nodes excluding certain tags
            $nodes = [];
            $this->collectTextNodes($doc->documentElement, $nodes);

            if (empty($nodes)) {
                return $html;
            }

            // Build unique list of texts to translate
            $texts = [];
            foreach ($nodes as $node) {
                $text = trim($node->nodeValue);
                if ($text !== '') {
                    $texts[$text] = true; // unique set
                }
            }

            if (empty($texts)) {
                return $html;
            }

            $uniqueTexts = array_keys($texts);

            /** @var GoogleTranslateService $translator */
            $translator = app(GoogleTranslateService::class);

            // Translate each unique text (cached for 30 days in the service)
            $map = [];
            foreach ($uniqueTexts as $original) {
                $map[$original] = $translator->translate($original, 'en');
            }

            // Replace node values using map
            foreach ($nodes as $node) {
                $orig = trim($node->nodeValue);
                if ($orig !== '' && isset($map[$orig])) {
                    // Preserve leading/trailing whitespace around the trimmed core
                    $leading = '';
                    $trailing = '';
                    if (preg_match('/^(\s*)/u', $node->nodeValue, $m1)) { $leading = $m1[1] ?? ''; }
                    if (preg_match('/(\s*)$/u', $node->nodeValue, $m2)) { $trailing = $m2[1] ?? ''; }
                    $node->nodeValue = $leading . $map[$orig] . $trailing;
                }
            }

            return $doc->saveHTML();
        } catch (\Throwable $e) {
            // On any failure, return original content without breaking the page
            return $html;
        }
    }

    protected function collectTextNodes(\DOMNode $node, array &$nodes): void
    {
        // Skip certain containers entirely
        if ($node->nodeType === XML_ELEMENT_NODE) {
            $tag = strtolower($node->nodeName);
            if (in_array($tag, ['script', 'style', 'code', 'pre', 'noscript'])) {
                return;
            }
        }

        foreach (iterator_to_array($node->childNodes ?? []) as $child) {
            if ($child->nodeType === XML_TEXT_NODE) {
                // Keep non-empty text nodes
                if (trim($child->nodeValue) !== '') {
                    $nodes[] = $child;
                }
            } else {
                $this->collectTextNodes($child, $nodes);
            }
        }
    }
}
