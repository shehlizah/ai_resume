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
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type')->default('string'); // string, integer, boolean, json
            $table->string('group')->default('general'); // general, jobs, interviews, api
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Insert default settings
        DB::table('system_settings')->insert([
            [
                'key' => 'job_limit_free',
                'value' => '5',
                'type' => 'integer',
                'group' => 'jobs',
                'description' => 'Number of job results for free users',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'job_limit_premium',
                'value' => '8',
                'type' => 'integer',
                'group' => 'jobs',
                'description' => 'Number of job results for premium users',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'session_limit_enabled',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'jobs',
                'description' => 'Enable session-based limits for job searches',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'max_questions_per_session',
                'value' => '5',
                'type' => 'integer',
                'group' => 'interviews',
                'description' => 'Maximum questions per interview session',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'ai_enabled',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'interviews',
                'description' => 'Enable AI question generation',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'scoring_enabled',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'interviews',
                'description' => 'Enable answer scoring',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'feedback_enabled',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'interviews',
                'description' => 'Enable detailed feedback',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_settings');
    }
};
