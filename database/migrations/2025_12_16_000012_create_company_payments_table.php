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
        Schema::create('company_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('item_type'); // 'package' or 'addon'
            $table->string('item_slug');
            $table->string('item_name');
            $table->decimal('amount', 15, 2);
            $table->enum('payment_method', ['manual', 'stripe'])->default('manual');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');

            // Manual payment fields
            $table->string('payment_proof')->nullable();
            $table->string('bank_account_name')->nullable();
            $table->date('transfer_date')->nullable();

            // Stripe payment fields
            $table->string('stripe_session_id')->nullable();
            $table->string('stripe_payment_intent')->nullable();

            // Admin review
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('reviewed_at')->nullable();
            $table->text('admin_notes')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_payments');
    }
};
