<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('cover_letters', function (Blueprint $table) {
            if (!Schema::hasColumn('cover_letters', 'user_name')) {
                $table->string('user_name')->after('user_id');
            }
            if (!Schema::hasColumn('cover_letters', 'user_email')) {
                $table->string('user_email')->after('user_name');
            }
            if (!Schema::hasColumn('cover_letters', 'user_phone')) {
                $table->string('user_phone')->after('user_email');
            }
            if (!Schema::hasColumn('cover_letters', 'user_address')) {
                $table->string('user_address')->after('user_phone');
            }
        });
    }

    public function down()
    {
        Schema::table('cover_letters', function (Blueprint $table) {
            $table->dropColumn(['user_name', 'user_email', 'user_phone', 'user_address']);
        });
    }
};