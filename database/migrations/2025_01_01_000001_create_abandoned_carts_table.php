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
        Schema::create('abandoned_carts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('type'); // 'signup', 'resume', 'pdf_preview'
            $table->string('status')->default('abandoned'); // 'abandoned', 'recovered', 'completed'
            $table->json('session_data')->nullable(); // Store form data, resume data, etc.
            $table->string('resume_id')->nullable(); // For resume-related abandonment
            $table->integer('recovery_email_sent_count')->default(0); // Track how many recovery emails sent
            $table->timestamp('first_recovery_email_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index(['user_id', 'type']);
            $table->index(['status', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('abandoned_carts');
    }
};
