<?php

namespace App\Services;

/**
 * MINIMAL DomPDF Template Sanitizer
 *
 * Only fixes critical DomPDF incompatibilities while preserving template structure
 */
class DomPdfTemplateSanitizer
{
    /**
     * Build complete safe HTML document - MINIMAL APPROACH
     */
    public function buildSafeDocument($html, $css, $data = [])
    {
        // Fill placeholders FIRST (before any processing)
        foreach ($data as $key => $value) {
            $html = str_replace('{{' . $key . '}}', $value, $html);
        }

        // MINIMAL CSS fixes - only what breaks DomPDF
        $css = $this->minimalCssFixes($css);

        // Build complete document
        $document = "<!DOCTYPE html>
<html lang=\"en\">
<head>
    <meta charset=\"UTF-8\">
    <title>Resume</title>
    <style>
        /* Base resets */
        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, 'DejaVu Sans', sans-serif;
            line-height: 1.6;
            color: #333;
            font-size: 11pt;
            margin: 0;
            padding: 0;
        }

        @page {
            margin: 12mm;
            size: A4 portrait;
        }

        /* Force full width on resume container */
        .resume-container {
            max-width: 100% !important;
            width: 100% !important;
            margin: 0 !important;
            box-shadow: none !important;
        }

        /* Remove pseudo-element decorations that break */
        .header::before, .header::after,
        .experience-item::before, .education-item::before,
        .section-title::after, .contact-item::before {
            display: none !important;
        }

        /* Fix grid layouts */
        .contact-info {
            display: block !important;
        }

        .contact-item {
            display: block !important;
            margin-bottom: 5px;
        }

        .skills-grid {
            display: block !important;
        }

        .skill-item {
            display: inline-block !important;
            margin-right: 10px;
            margin-bottom: 8px;
        }

        /* Original template CSS (with minimal fixes) */
        {$css}
    </style>
</head>
<body>
    {$html}
</body>
</html>";

        return $document;
    }

    /**
     * Minimal CSS fixes - only critical DomPDF incompatibilities
     */
    private function minimalCssFixes($css)
    {
        // Replace CSS variables with actual values
        $variables = $this->extractCssVariables($css);
        $css = $this->replaceCssVariables($css, $variables);

        // Remove :root block
        $css = preg_replace('/:root\s*\{[^}]+\}/s', '', $css);

        // Fix critical display issues
        $css = preg_replace('/display\s*:\s*flex\s*;/i', 'display: block;', $css);
        $css = preg_replace('/display\s*:\s*grid\s*;/i', 'display: block;', $css);

        // Remove flexbox/grid properties
        $css = preg_replace('/\s*(flex-[^:]+|justify-content|align-items|gap|grid-[^:]+)\s*:[^;]+;/i', '', $css);

        // Remove unsupported properties
        $css = preg_replace('/\s*(clip-path|transform|filter|backdrop-filter)\s*:[^;]+;/i', '', $css);

        // Fix box-shadow (replace with border)
        $css = preg_replace('/box-shadow\s*:[^;]+;/i', '', $css);

        // Fix linear gradients (keep background color from gradient start)
        $css = preg_replace_callback('/background\s*:\s*linear-gradient\([^)]*?(#[0-9a-f]{3,6}|rgba?\([^)]+\))[^)]*\)/i', function($matches) {
            return 'background: ' . $this->extractFirstColor($matches[0]);
        }, $css);

        // Remove viewport units
        $css = preg_replace('/(\d+(?:\.\d+)?)\s*v[wh]/i', '100%', $css);

        // Fix max-width constraint on container
        $css = preg_replace('/\.resume-container\s*\{([^}]*?)max-width\s*:[^;]+;/is', '.resume-container { $1', $css);

        // Remove calc()
        $css = preg_replace('/calc\([^)]+\)/i', '100%', $css);

        // Remove transitions and animations
        $css = preg_replace('/\s*(transition|animation)\s*:[^;]+;/i', '', $css);

        // Remove media queries
        $css = preg_replace('/@media[^{]+\{([^{}]+|\{[^{}]+\})*\}/s', '', $css);

        return $css;
    }

    /**
     * Extract CSS variables from :root
     */
    private function extractCssVariables($css)
    {
        $variables = [];
        if (preg_match('/:root\s*\{([^}]+)\}/s', $css, $match)) {
            preg_match_all('/--([\w-]+)\s*:\s*([^;]+);/i', $match[1], $varMatches, PREG_SET_ORDER);
            foreach ($varMatches as $varMatch) {
                $variables['--' . $varMatch[1]] = trim($varMatch[2]);
            }
        }
        return $variables;
    }

    /**
     * Replace var() with actual values
     */
    private function replaceCssVariables($css, $variables)
    {
        return preg_replace_callback('/var\((--[\w-]+)(?:,\s*([^)]+))?\)/i', function($matches) use ($variables) {
            $varName = $matches[1];
            $fallback = $matches[2] ?? '#333333';
            return $variables[$varName] ?? $fallback;
        }, $css);
    }

    /**
     * Extract first color from gradient string
     */
    private function extractFirstColor($gradientString)
    {
        // Try to find first hex color
        if (preg_match('/#[0-9a-f]{3,6}/i', $gradientString, $match)) {
            return $match[0];
        }
        // Try to find first rgb/rgba color
        if (preg_match('/rgba?\([^)]+\)/i', $gradientString, $match)) {
            return $match[0];
        }
        // Fallback
        return '#f0f0f0';
    }

    // Keep these for backward compatibility but they won't be used
    public function sanitizeHtml($html) { return $html; }
    public function sanitizeCss($css) { return $this->minimalCssFixes($css); }
}
