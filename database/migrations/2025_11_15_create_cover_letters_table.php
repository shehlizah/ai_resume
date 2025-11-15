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
        Schema::create('cover_letters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->string('recipient_name');
            $table->string('company_name');
            $table->string('company_address');
            $table->longText('content');
            $table->string('pdf_url')->nullable();
            $table->boolean('is_deleted')->default(false);
            $table->timestamps();

            // Index for faster queries
            $table->index(['user_id', 'is_deleted']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cover_letters');
    }
};
