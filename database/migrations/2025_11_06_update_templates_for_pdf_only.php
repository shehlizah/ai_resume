<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('templates', function (Blueprint $table) {

            // ✅ Drop old HTML columns only if they exist
            if (Schema::hasColumn('templates', 'template_file')) {
                $table->dropColumn('template_file');
            }

            if (Schema::hasColumn('templates', 'css_file')) {
                $table->dropColumn('css_file');
            }

            if (Schema::hasColumn('templates', 'features')) {
                $table->dropColumn('features');
            }

            // ✅ Add pdf_file if missing
            if (!Schema::hasColumn('templates', 'pdf_file')) {
                $table->string('pdf_file')->nullable()->after('preview_image');
            }

            // ✅ Add template_type if missing
            if (!Schema::hasColumn('templates', 'template_type')) {
                $table->enum('template_type', ['pdf'])
                      ->default('pdf')
                      ->after('pdf_file');
            }
        });
    }

    public function down(): void
    {
        Schema::table('templates', function (Blueprint $table) {

            // ✅ Recreate HTML columns on rollback
            if (!Schema::hasColumn('templates', 'template_file')) {
                $table->string('template_file')->nullable();
            }

            if (!Schema::hasColumn('templates', 'css_file')) {
                $table->string('css_file')->nullable();
            }

            if (!Schema::hasColumn('templates', 'features')) {
                $table->longText('features')->nullable();
            }

            // ✅ Drop new columns
            if (Schema::hasColumn('templates', 'pdf_file')) {
                $table->dropColumn('pdf_file');
            }

            if (Schema::hasColumn('templates', 'template_type')) {
                $table->dropColumn('template_type');
            }
        });
    }
};
