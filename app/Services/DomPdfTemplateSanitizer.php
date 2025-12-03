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
{
    /**
     * Sanitize HTML - Remove/convert problematic elements
     */
    public function sanitizeHtml($html)
    {
        // Remove DOCTYPE if present
        $html = preg_replace('/<\!DOCTYPE[^>]*>/i', '', $html);

        // Remove html, head, body tags
        $html = preg_replace('/<\/?html[^>]*>/i', '', $html);
        $html = preg_replace('/<\/?head[^>]*>/i', '', $html);
        $html = preg_replace('/<\/?body[^>]*>/i', '', $html);

        // Remove style tags
        $html = preg_replace('/<style[^>]*>.*?<\/style>/is', '', $html);

        // Remove link tags
        $html = preg_replace('/<link[^>]*>/i', '', $html);

        // Remove script tags
        $html = preg_replace('/<script[^>]*>.*?<\/script>/is', '', $html);

        // Convert ALL tables to divs
        $html = $this->convertTablesToDivs($html);

        // Remove inline styles that cause width issues
        $html = $this->removeProblematicInlineStyles($html);

        return trim($html);
    }

    /**
     * Remove inline styles that cause width problems
     */
    private function removeProblematicInlineStyles($html)
    {
        // Remove max-width from inline styles
        $html = preg_replace('/max-width\s*:\s*[^;]+;?/i', '', $html);

        // Remove min-width from inline styles
        $html = preg_replace('/min-width\s*:\s*[^;]+;?/i', '', $html);

        // Remove width > 100% from inline styles
        $html = preg_replace('/width\s*:\s*[0-9]+[0-9]{3,}px;?/i', 'width: 100%;', $html);

        return $html;
    }

    /**
     * Convert tables to div-based structure
     */
    private function convertTablesToDivs($html)
    {
        // Replace table tags
        $html = preg_replace('/<table([^>]*)>/i', '<div class="pdf-table"$1>', $html);
        $html = preg_replace('/<\/table>/i', '</div>', $html);

        $html = preg_replace('/<tr([^>]*)>/i', '<div class="pdf-row"$1>', $html);
        $html = preg_replace('/<\/tr>/i', '</div>', $html);

        $html = preg_replace('/<td([^>]*)>/i', '<div class="pdf-cell"$1>', $html);
        $html = preg_replace('/<\/td>/i', '</div>', $html);

        $html = preg_replace('/<th([^>]*)>/i', '<div class="pdf-cell pdf-header-cell"$1>', $html);
        $html = preg_replace('/<\/th>/i', '</div>', $html);

        $html = preg_replace('/<thead([^>]*)>/i', '<div class="pdf-thead"$1>', $html);
        $html = preg_replace('/<\/thead>/i', '</div>', $html);

        $html = preg_replace('/<tbody([^>]*)>/i', '<div class="pdf-tbody"$1>', $html);
        $html = preg_replace('/<\/tbody>/i', '</div>', $html);

        $html = preg_replace('/<tfoot([^>]*)>/i', '<div class="pdf-tfoot"$1>', $html);
        $html = preg_replace('/<\/tfoot>/i', '</div>', $html);

        return $html;
    }

    /**
     * Sanitize CSS - Remove/convert problematic properties
     */
    public function sanitizeCss($css)
    {
        // Extract and store CSS variables for replacement
        $variables = [];
        if (preg_match('/:root\s*\{([^}]+)\}/s', $css, $match)) {
            preg_match_all('/--([\w-]+)\s*:\s*([^;]+);/i', $match[1], $varMatches, PREG_SET_ORDER);
            foreach ($varMatches as $varMatch) {
                $variables['--' . $varMatch[1]] = trim($varMatch[2]);
            }
        }

        // Replace var() with actual values
        $css = preg_replace_callback('/var\((--[\w-]+)(?:,\s*([^)]+))?\)/i', function($matches) use ($variables) {
            $varName = $matches[1];
            $fallback = $matches[2] ?? '#333333';
            return $variables[$varName] ?? $fallback;
        }, $css);

        // Remove @import
        $css = preg_replace('/@import[^;]+;/i', '', $css);

        // Remove :root block
        $css = preg_replace('/:root\s*\{[^}]+\}/s', '', $css);

        // Fix display properties
        $css = preg_replace('/display\s*:\s*flex\s*;?/i', 'display: block;', $css);
        $css = preg_replace('/display\s*:\s*inline-flex\s*;?/i', 'display: inline-block;', $css);
        $css = preg_replace('/display\s*:\s*grid\s*;?/i', 'display: block;', $css);
        $css = preg_replace('/display\s*:\s*table\s*;?/i', 'display: block;', $css);
        $css = preg_replace('/display\s*:\s*table-cell\s*;?/i', 'display: inline-block;', $css);
        $css = preg_replace('/display\s*:\s*table-row\s*;?/i', 'display: block;', $css);

        // Remove flexbox/grid properties
        $css = preg_replace('/flex[^:]*:[^;]+;?/i', '', $css);
        $css = preg_replace('/justify-content\s*:[^;]+;?/i', '', $css);
        $css = preg_replace('/align-items\s*:[^;]+;?/i', '', $css);
        $css = preg_replace('/align-self\s*:[^;]+;?/i', '', $css);
        $css = preg_replace('/grid[^:]*:[^;]+;?/i', '', $css);

        // Remove problematic properties
        $css = preg_replace('/transform\s*:[^;]+;?/i', '', $css);
        $css = preg_replace('/clip-path\s*:[^;]+;?/i', '', $css);
        $css = preg_replace('/position\s*:\s*fixed\s*;?/i', 'position: relative;', $css);
        $css = preg_replace('/position\s*:\s*sticky\s*;?/i', 'position: relative;', $css);
        $css = preg_replace('/box-shadow\s*:[^;]+;?/i', 'border: 1px solid #ddd;', $css);
        $css = preg_replace('/filter\s*:[^;]+;?/i', '', $css);
        $css = preg_replace('/backdrop-filter\s*:[^;]+;?/i', '', $css);
        $css = preg_replace('/mix-blend-mode\s*:[^;]+;?/i', '', $css);

        // Simplify gradients
        $css = preg_replace('/background\s*:\s*linear-gradient\([^)]+\)/i', 'background: #f0f0f0', $css);
        $css = preg_replace('/background-image\s*:\s*linear-gradient\([^)]+\)/i', '', $css);

        // Replace viewport units
        $css = preg_replace('/(\d+(?:\.\d+)?)\s*vw/i', '100%', $css);
        $css = preg_replace('/(\d+(?:\.\d+)?)\s*vh/i', 'auto', $css);

        // Replace calc()
        $css = preg_replace('/calc\([^)]+\)/i', 'auto', $css);

        // Remove media queries
        $css = preg_replace('/@media[^{]+\{([^{}]+|\{[^{}]+\})*\}/s', '', $css);

        // FIX: Remove problematic max-width constraints
        $css = preg_replace('/max-width\s*:\s*[0-9]{3,}px\s*;?/i', 'max-width: 100%;', $css);

        // FIX: Remove min-width constraints
        $css = preg_replace('/min-width\s*:\s*[^;]+;?/i', '', $css);

        // Remove ::before and ::after pseudo-elements with problematic content
        $css = preg_replace('/::?before\s*\{[^}]*content[^}]*\}/is', '', $css);
        $css = preg_replace('/::?after\s*\{[^}]*content[^}]*\}/is', '', $css);

        // Remove opacity and rgba colors (replace with solid colors)
        $css = preg_replace('/rgba?\([^)]+,\s*0?\.[0-9]+\)/i', '#f0f0f0', $css);

        // Remove transition and animation
        $css = preg_replace('/transition\s*:[^;]+;?/i', '', $css);
        $css = preg_replace('/animation\s*:[^;]+;?/i', '', $css);
        $css = preg_replace('/@keyframes[^{]+\{([^{}]+|\{[^{}]+\})*\}/s', '', $css);

        return $css;
    }

    /**
     * Build complete safe HTML document with WIDTH FIX
     */
    public function buildSafeDocument($html, $css, $data = [])
    {
        // Sanitize
        $html = $this->sanitizeHtml($html);
        $css = $this->sanitizeCss($css);

        // Fill placeholders
        foreach ($data as $key => $value) {
            $html = str_replace('{{' . $key . '}}', $value, $html);
        }

        // Build document with WIDTH-SAFE styles
        $document = "<!DOCTYPE html>
<html lang=\"en\">
<head>
    <meta charset=\"UTF-8\">
    <title>Resume</title>
    <style>
        /* CRITICAL WIDTH FIXES */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            max-width: 100% !important;
        }

        html, body {
            width: 100%;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            font-size: 11pt;
            width: 100%;
        }

        @page {
            margin: 12mm 15mm;
            size: A4 portrait;
        }

        /* Force full width on common containers */
        .resume, .resume-container, .resume-wrapper,
        .container, .main-content, .content {
            width: 100% !important;
            max-width: 100% !important;
            min-width: 0 !important;
            margin-left: 0 !important;
            margin-right: 0 !important;
        }

        /* Ensure sections use full width */
        .section, .content-section {
            width: 100% !important;
            max-width: 100% !important;
        }

        /* Fix header width */
        .header, .name-header, .header-content {
            width: 100% !important;
            max-width: 100% !important;
        }

        /* Fix contact-info grid to simple list */
        .contact-info {
            display: block !important;
            width: 100% !important;
        }

        .contact-item {
            display: block !important;
            margin-bottom: 4px;
        }

        /* Fix skills grid */
        .skills-grid, .skill-list {
            display: block !important;
            width: 100% !important;
        }

        .skill-item, .skill-list li {
            display: inline-block !important;
            margin: 0 8px 8px 0;
        }

        /* Fix job/education headers */
        .job-header, .degree-header {
            display: block !important;
            width: 100% !important;
            margin-bottom: 8px;
        }

        .job-title, .degree-name, .job-date, .education-date {
            display: block !important;
        }

        /* Prevent overflow */
        p, div, span, h1, h2, h3, h4, h5, h6 {
            word-wrap: break-word;
            overflow-wrap: break-word;
        }

        /* Safe table replacements */
        .pdf-table {
            width: 100% !important;
            display: block !important;
        }

        .pdf-row {
            width: 100% !important;
            display: block !important;
            overflow: hidden;
        }

        .pdf-cell {
            display: inline-block !important;
            vertical-align: top;
        }

        /* Force block display for table elements */
        table, .table {
            display: block !important;
            width: 100% !important;
        }

        tr, .table-row {
            display: block !important;
            width: 100% !important;
        }

        td, th, .table-cell {
            display: inline-block !important;
        }

        /* Template CSS (sanitized) */
        {$css}
    </style>
</head>
<body>
    <div style=\"width: 100%; max-width: 100%;\">
        {$html}
    </div>
</body>
</html>";

        return $document;
    }
}
