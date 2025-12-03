<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Template;
use Illuminate\Support\Facades\File;

class TemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing templates
        Template::truncate();

        $templateFiles = [
            [
                'name' => 'Modern Geometric',
                'slug' => 'modern-geometric',
                'category' => 'modern',
                'description' => 'Bold design with geometric elements and gradient accents. Perfect for creative professionals.',
                'file' => 'modern-geometric.html',
                'is_premium' => false,
                'is_active' => true,
            ],
            [
                'name' => 'Editorial Minimal',
                'slug' => 'editorial-minimal',
                'category' => 'minimal',
                'description' => 'Clean, magazine-inspired layout with elegant typography. Ideal for corporate and professional roles.',
                'file' => 'editorial-minimal.html',
                'is_premium' => false,
                'is_active' => true,
            ],
        ];

        foreach ($templateFiles as $templateData) {
            $filePath = resource_path('templates/' . $templateData['file']);

            if (!File::exists($filePath)) {
                $this->command->warn("Template file not found: {$filePath}");
                continue;
            }

            $fullHtml = File::get($filePath);

            // Extract HTML and CSS
            $extracted = $this->extractHtmlAndCss($fullHtml);

            Template::create([
                'name' => $templateData['name'],
                'slug' => $templateData['slug'],
                'category' => $templateData['category'],
                'description' => $templateData['description'],
                'html_content' => $extracted['html'],
                'css_content' => $extracted['css'],
                'preview_image' => null,
                'is_premium' => $templateData['is_premium'],
                'is_active' => $templateData['is_active'],
            ]);

            $this->command->info("✓ Created template: {$templateData['name']}");
        }
    }

    /**
     * Extract HTML body and CSS from a complete HTML file
     */
    private function extractHtmlAndCss($fullHtml)
    {
        $css = '';
        $html = $fullHtml;

        // Extract all <style> tags
        if (preg_match_all('/<style[^>]*>(.*?)<\/style>/is', $html, $matches)) {
            foreach ($matches[1] as $styleBlock) {
                $css .= trim($styleBlock) . "\n";
            }
            // Remove <style> tags from HTML
            $html = preg_replace('/<style[^>]*>.*?<\/style>/is', '', $html);
        }

        // Extract body content
        if (preg_match('/<body[^>]*>(.*?)<\/body>/is', $html, $bodyMatch)) {
            $html = trim($bodyMatch[1]);
        } else {
            // If no body tag, remove DOCTYPE, html, head tags
            $html = preg_replace('/<\!DOCTYPE[^>]*>/i', '', $html);
            $html = preg_replace('/<\/?html[^>]*>/i', '', $html);
            $html = preg_replace('/<head[^>]*>.*?<\/head>/is', '', $html);
            $html = preg_replace('/<\/?body[^>]*>/i', '', $html);
        }

        // Remove link tags, meta tags, title tags
        $html = preg_replace('/<link[^>]*>/i', '', $html);
        $html = preg_replace('/<meta[^>]*>/i', '', $html);
        $html = preg_replace('/<title[^>]*>.*?<\/title>/is', '', $html);

        return [
            'html' => trim($html),
            'css' => trim($css)
        ];
    }
}
