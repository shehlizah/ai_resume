<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employer_add_ons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('add_on_id')->constrained()->onDelete('cascade');
            $table->decimal('amount_paid', 8, 2);
            $table->string('payment_gateway')->nullable();
            $table->string('payment_id')->nullable();
            $table->string('status')->default('active');
            $table->timestamp('purchased_at');
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->unique(['employer_id', 'add_on_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employer_add_ons');
    }
};
