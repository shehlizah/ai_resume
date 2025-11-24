<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Template;

class TemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $templates = [
            [
                'name' => 'Professional',
                'description' => 'Clean and professional template perfect for corporate jobs',
                'html_content' => '<div class="resume-professional">{{content}}</div>',
                'css_content' => '.resume-professional { font-family: Arial, sans-serif; }',
                'preview_image' => null,
                'is_active' => true,
                'order' => 1,
            ],
            [
                'name' => 'Modern',
                'description' => 'Contemporary design with bold typography',
                'html_content' => '<div class="resume-modern">{{content}}</div>',
                'css_content' => '.resume-modern { font-family: "Helvetica Neue", sans-serif; }',
                'preview_image' => null,
                'is_active' => true,
                'order' => 2,
            ],
            [
                'name' => 'Creative',
                'description' => 'Stand out with this creative and colorful template',
                'html_content' => '<div class="resume-creative">{{content}}</div>',
                'css_content' => '.resume-creative { font-family: "Georgia", serif; color: #333; }',
                'preview_image' => null,
                'is_active' => true,
                'order' => 3,
            ],
            [
                'name' => 'Minimalist',
                'description' => 'Simple and elegant minimalist design',
                'html_content' => '<div class="resume-minimalist">{{content}}</div>',
                'css_content' => '.resume-minimalist { font-family: "Roboto", sans-serif; }',
                'preview_image' => null,
                'is_active' => true,
                'order' => 4,
            ],
        ];

        foreach ($templates as $template) {
            Template::create($template);
        }
    }
}