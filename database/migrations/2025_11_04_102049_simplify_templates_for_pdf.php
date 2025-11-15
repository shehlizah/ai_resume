<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('templates', function (Blueprint $table) {
            // Remove old HTML-related columns
            $table->dropColumn(['template_file', 'css_file', 'features']);

            // Ensure pdf columns exist (in case not)
            if (!Schema::hasColumn('templates', 'pdf_file')) {
                $table->string('pdf_file')->nullable()->after('preview_image');
            }

            if (!Schema::hasColumn('templates', 'template_type')) {
                $table->enum('template_type', ['pdf'])->default('pdf')->after('pdf_file');
            }
        });
    }

    public function down(): void
    {
        Schema::table('templates', function (Blueprint $table) {
            // Re-add the dropped columns if rollback happens
            $table->string('template_file')->nullable();
            $table->string('css_file')->nullable();
            $table->longText('features')->nullable();
            $table->dropColumn(['pdf_file', 'template_type']);
        });
    }
};
