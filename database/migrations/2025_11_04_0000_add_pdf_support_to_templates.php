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
            // Add PDF file column
            $table->string('pdf_file')->nullable()->after('css_file');
            
            // Add template type (html or pdf)
            $table->enum('template_type', ['html', 'pdf'])->default('html')->after('pdf_file');
            
            // Make HTML/CSS optional since we might use PDF
            $table->string('template_file')->nullable()->change();
            $table->string('css_file')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('templates', function (Blueprint $table) {
            $table->dropColumn(['pdf_file', 'template_type']);
        });
    }
};