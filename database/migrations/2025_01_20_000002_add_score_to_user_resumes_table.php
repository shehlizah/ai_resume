<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_resumes', function (Blueprint $table) {
            if (!Schema::hasColumn('user_resumes', 'score')) {
                $table->integer('score')->nullable()->after('status')->comment('Resume quality score 0-100');
            }
        });
    }

    public function down(): void
    {
        Schema::table('user_resumes', function (Blueprint $table) {
            if (Schema::hasColumn('user_resumes', 'score')) {
                $table->dropColumn('score');
            }
        });
    }
};
