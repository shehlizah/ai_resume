<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PdfTemplatesSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('pdf_templates')->insert([
            [
                'title' => 'Professional Blue',
                'file_path' => 'storage/templates/professional_blue.pdf',
                'description' => 'Clean modern resume template with blue header.',
                'created_by' => 1,
                'created_at' => now(),
            ],
            [
                'title' => 'Minimal Classic',
                'file_path' => 'storage/templates/minimal_classic.pdf',
                'description' => 'Minimal black & white resume design.',
                'created_by' => 1,
                'created_at' => now(),
            ],
        ]);
    }
}
