<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('applicant_name');
            $table->string('applicant_email');
            $table->string('applicant_phone')->nullable();
            $table->string('resume_url')->nullable();
            $table->text('cover_letter')->nullable();
            $table->timestamps();

            $table->index('job_id');
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_applications');
    }
};
