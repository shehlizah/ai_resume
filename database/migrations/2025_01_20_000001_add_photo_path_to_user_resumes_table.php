<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_resumes', function (Blueprint $table) {
            if (!Schema::hasColumn('user_resumes', 'photo_path')) {
                $table->string('photo_path')->nullable()->after('generated_pdf_path');
            }
        });
    }

    public function down(): void
    {
        Schema::table('user_resumes', function (Blueprint $table) {
            if (Schema::hasColumn('user_resumes', 'photo_path')) {
                $table->dropColumn('photo_path');
            }
        });
    }
};
