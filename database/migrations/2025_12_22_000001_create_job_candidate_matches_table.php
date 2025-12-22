<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_candidate_matches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // candidate
            $table->foreignId('user_resume_id')->nullable()->constrained()->onDelete('set null');
            $table->integer('match_score')->default(0); // 0-100
            $table->json('match_details')->nullable(); // skills matched, experience, etc.
            $table->text('ai_summary')->nullable(); // Optional AI-generated match summary
            $table->enum('status', ['pending', 'shortlisted', 'rejected', 'contacted'])->default('pending');
            $table->timestamp('matched_at');
            $table->timestamps();

            $table->index(['job_id', 'match_score']);
            $table->index(['job_id', 'status']);
            $table->unique(['job_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_candidate_matches');
    }
};
