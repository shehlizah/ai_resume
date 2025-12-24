<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\GoogleTranslateService;

class TranslationController extends Controller
{
    protected GoogleTranslateService $translator;

    public function __construct(GoogleTranslateService $translator)
    {
        $this->translator = $translator;
    }

    /**
     * Translate a single string or array of strings
     * 
     * Usage:
     * POST /api/translate
     * Body: { "text": "Selamat datang", "target": "en" }
     * 
     * Or batch:
     * Body: { "texts": ["Hello", "World"], "target": "en" }
     */
    public function translate(Request $request)
    {
        $locale = app()->getLocale();
        
        // Only translate to English
        if ($locale !== 'en') {
            return response()->json(['error' => 'Translation only available for English locale'], 400);
        }

        $validated = $request->validate([
            'text' => 'string|nullable',
            'texts' => 'array|nullable',
            'target' => 'string|in:en,id',
        ]);

        $target = $validated['target'] ?? 'en';
        $results = [];

        try {
            if (!empty($validated['text'])) {
                // Single text
                $translated = $this->translator->translate($validated['text'], $target);
                $results = [
                    'original' => $validated['text'],
                    'translated' => $translated,
                ];
            } elseif (!empty($validated['texts'])) {
                // Multiple texts
                $results = [];
                foreach ($validated['texts'] as $text) {
                    $translated = $this->translator->translate($text, $target);
                    $results[] = [
                        'original' => $text,
                        'translated' => $translated,
                    ];
                }
            }

            return response()->json([
                'success' => true,
                'locale' => $locale,
                'data' => $results,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
