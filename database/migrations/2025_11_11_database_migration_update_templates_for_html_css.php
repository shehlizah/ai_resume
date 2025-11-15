<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('templates', function (Blueprint $table) {
            // Remove or rename the pdf_file column
            $table->dropColumn('pdf_file'); // If you want to remove it completely
            
            // Add columns for HTML and CSS content
            $table->longText('html_content')->nullable()->after('description');
            $table->longText('css_content')->nullable()->after('html_content');
            
            // Optional: Add a column to store the HTML/CSS as files (path)
            $table->string('html_file_path')->nullable()->after('css_content');
            $table->string('css_file_path')->nullable()->after('html_file_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('templates', function (Blueprint $table) {
            // Restore pdf_file column
            $table->string('pdf_file')->nullable();
            
            // Remove HTML/CSS columns
            $table->dropColumn(['html_content', 'css_content', 'html_file_path', 'css_file_path']);
        });
    }
};