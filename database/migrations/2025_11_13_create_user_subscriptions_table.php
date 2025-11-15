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
        Schema::create('user_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('subscription_plan_id')->constrained()->onDelete('cascade');
            $table->enum('billing_period', ['monthly', 'yearly'])->default('monthly');
            $table->enum('status', ['active', 'canceled', 'expired', 'pending'])->default('pending');
            $table->decimal('amount', 10, 2);
            $table->date('start_date');
            $table->date('end_date');
            $table->date('next_billing_date')->nullable();
            $table->boolean('auto_renew')->default(true);
            $table->enum('payment_gateway', ['stripe', 'paypal'])->nullable();
            $table->string('gateway_subscription_id')->nullable(); // Stripe/PayPal subscription ID
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_subscriptions');
    }
};