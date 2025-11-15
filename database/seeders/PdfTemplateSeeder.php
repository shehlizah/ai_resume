<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PdfTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $templates = [
            [
                'name' => 'Professional Resume (PDF)',
                'slug' => Str::slug('Professional Resume PDF'),
                'category' => 'professional',
                'description' => 'A clean and modern professional resume layout in PDF format.',
                'preview_image' => 'storage/previews/professional_resume.jpg',
                'pdf_file' => 'storage/templates/professional_resume.pdf',
                'template_type' => 'pdf',
                'is_premium' => false,
                'is_active' => true,
                'sort_order' => 1,
                'version' => '1.0',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Minimal Black & White CV (PDF)',
                'slug' => Str::slug('Minimal Black White CV PDF'),
                'category' => 'minimal',
                'description' => 'Minimalistic black and white CV for creative professionals.',
                'preview_image' => 'storage/previews/minimal_bw.jpg',
                'pdf_file' => 'storage/templates/minimal_bw.pdf',
                'template_type' => 'pdf',
                'is_premium' => true,
                'is_active' => true,
                'sort_order' => 2,
                'version' => '1.0',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        DB::table('templates')->insert($templates);
    }
}
