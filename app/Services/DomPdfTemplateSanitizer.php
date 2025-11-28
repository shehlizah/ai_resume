<?php

namespace App\Services;

/**
 * DomPDF Template Sanitizer - WIDTH FIX VERSION
 * 
 * Automatically converts ANY template to be DomPDF-compatible
 * with proper width handling to prevent cramped layouts
 */
class DomPdfTemplateSanitizer
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
        // Remove @import
        $css = preg_replace('/@import[^;]+;/i', '', $css);
        
        // Remove CSS variables
        $css = preg_replace('/:root\s*\{[^}]+\}/s', '', $css);
        $css = preg_replace('/var\([^)]+\)/i', '#333333', $css);
        
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
        $css = preg_replace('/box-shadow\s*:[^;]+;?/i', '', $css);
        
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