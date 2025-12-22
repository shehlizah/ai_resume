<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('posted_jobs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('external_id')->nullable();
            $table->string('title');
            $table->string('company');
            $table->string('location');
            $table->string('type')->default('Full Time');
            $table->text('description')->nullable();
            $table->string('salary')->nullable();
            $table->json('tags')->nullable();
            $table->timestamp('posted_at');
            $table->string('source')->default('company'); // 'company' or 'external'
            $table->string('url')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('user_id');
            $table->index('posted_at');
            $table->index('is_active');
            $table->index('source');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('posted_jobs');
    }
};
