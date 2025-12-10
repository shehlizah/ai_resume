<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->string('external_id')->unique();
            $table->string('title');
            $table->string('company');
            $table->string('location');
            $table->string('type')->default('Full Time');
            $table->text('description')->nullable();
            $table->string('salary')->nullable();
            $table->json('tags')->nullable();
            $table->timestamp('posted_at');
            $table->string('source');
            $table->string('url')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index('posted_at');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jobs');
    }
};
