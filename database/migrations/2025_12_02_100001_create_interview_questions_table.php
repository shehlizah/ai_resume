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
        Schema::create('interview_questions', function (Blueprint $table) {
            $table->id();
            $table->string('session_id');
            $table->integer('question_number');
            $table->text('question_text');
            $table->string('question_type')->nullable(); // behavioral, technical, situational, etc.
            $table->string('focus_area')->nullable(); // leadership, problem_solving, etc.
            $table->text('answer_text')->nullable();
            $table->decimal('score', 5, 2)->nullable();
            $table->json('feedback')->nullable(); // Store structured feedback (strengths, improvements, etc.)
            $table->timestamp('answered_at')->nullable();
            $table->timestamps();

            $table->foreign('session_id')
                ->references('session_id')
                ->on('interview_sessions')
                ->onDelete('cascade');

            $table->index(['session_id', 'question_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('interview_questions');
    }
};
