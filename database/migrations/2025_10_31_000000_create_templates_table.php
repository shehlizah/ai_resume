<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('category')->default('professional'); // professional, modern, creative, minimal
            $table->text('description')->nullable();
            $table->string('preview_image')->nullable(); // path to preview image
            $table->string('template_file')->nullable(); // path to HTML template file
            $table->string('css_file')->nullable(); // path to CSS file
            $table->boolean('is_premium')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->json('features')->nullable(); // ["ats-friendly", "single-page", "colorful"]
            $table->string('version')->default('1.0');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('templates');
    }
};