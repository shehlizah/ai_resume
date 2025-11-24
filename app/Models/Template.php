<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Template extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'category',
        'description',
        'html_content',
        'css_content',
        'html_file_path',
        'css_file_path',
        'preview_image',
        'is_premium',
        'is_active',
    ];

    protected $casts = [
        'is_premium' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Get the full HTML template with CSS embedded
     * Optimized for DOMPDF rendering with PDF-friendly CSS
     */
    public function getFullTemplate()
    {
        // CSS optimizations for DOMPDF (doesn't support modern CSS well)
        $pdfOptimizedCss = $this->css_content ?? '';

        // Replace problematic CSS properties that DOMPDF doesn't handle well
        // Convert display:flex to display:block for better PDF compatibility
        $pdfOptimizedCss = preg_replace('/display\s*:\s*flex\s*;/i', 'display: block;', $pdfOptimizedCss);

        // Convert grid layouts to table-like layouts
        $pdfOptimizedCss = preg_replace('/display\s*:\s*grid\s*;/i', 'display: block;', $pdfOptimizedCss);

        // Remove gap property (not supported in block layouts)
        $pdfOptimizedCss = preg_replace('/\s*gap\s*:\s*[^;]*;/i', '', $pdfOptimizedCss);

        // Ensure width:100% for sidebar and main content areas
        $pdfOptimizedCss = preg_replace('/flex\s*:\s*[^;]*;/i', 'width: 100%; display: block;', $pdfOptimizedCss);

        // Remove webkit prefixes that DOMPDF doesn't support well
        $pdfOptimizedCss = preg_replace('/-webkit-[^:]*:[^;]*;/i', '', $pdfOptimizedCss);

        // Remove moz prefixes
        $pdfOptimizedCss = preg_replace('/-moz-[^:]*:[^;]*;/i', '', $pdfOptimizedCss);

        $baseStyles = <<<'CSS'
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html { background: white; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 15mm;
            line-height: 1.6;
            color: #333;
            background: white;
        }

        /* PDF-specific adjustments */
        @page {
            margin: 15mm;
            size: A4 portrait;
        }

        /* Ensure sidebars and content areas stack vertically in PDF */
        .sidebar, .sidebar-left, .sidebar-right, [class*='sidebar'] {
            width: 100% !important;
            display: block !important;
            margin-bottom: 20px;
            page-break-inside: avoid;
        }

        .main-content, .content, [class*='content'] {
            width: 100% !important;
            display: block !important;
        }

        /* Prevent page breaks inside sections */
        .experience-item, .education-item, .skill-item, [class*='-item'] {
            page-break-inside: avoid;
            margin-bottom: 10px;
        }

        /* Container support */
        .container, .wrapper {
            width: 100% !important;
            max-width: 100% !important;
        }

        /* Ensure all text is visible */
        h1, h2, h3, h4, h5, h6 {
            margin-top: 10px;
            margin-bottom: 8px;
            color: inherit;
        }

        p, ul, ol {
            margin-bottom: 8px;
        }

        ul, ol {
            margin-left: 20px;
        }

        li {
            margin-bottom: 4px;
        }

        /* Links */
        a {
            color: #0066cc;
            text-decoration: none;
        }

        /* Tables */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f5f5f5;
            font-weight: bold;
        }
CSS;

        return "<!DOCTYPE html>
<html lang=\"en\">
<head>
    <meta charset=\"UTF-8\">
    <meta http-equiv=\"X-UA-Compatible\" content=\"ie=edge\">
    <title>{$this->name}</title>
    <style>
        {$baseStyles}

        {$pdfOptimizedCss}
    </style>
</head>
<body>
    {$this->html_content}
</body>
</html>";
    }

    /**
     * Replace placeholders with actual user data
     *
     * @param array $data Array of placeholder => value pairs
     * @return string Complete HTML with data filled in
     */
    public function renderWithData(array $data)
    {
        $html = $this->html_content;

        // Replace all placeholders
        foreach ($data as $key => $value) {
            $html = str_replace('{{' . $key . '}}', $value, $html);
        }

        return "<!DOCTYPE html>
<html lang=\"en\">
<head>
    <meta charset=\"UTF-8\">
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
    <title>{$this->name}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; padding: 20px; }
        {$this->css_content}
    </style>
</head>
<body>
    {$html}
</body>
</html>";
    }

    /**
     * Get all active templates
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get templates by category
     */
    public function scopeCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Get premium templates only
     */
    public function scopePremium($query)
    {
        return $query->where('is_premium', true);
    }

    /**
     * Get free templates only
     */
    public function scopeFree($query)
    {
        return $query->where('is_premium', false);
    }
}
