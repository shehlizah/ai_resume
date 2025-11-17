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
        Schema::table('subscription_plans', function (Blueprint $table) {
            // Drop old columns if they exist
            if (Schema::hasColumn('subscription_plans', 'stripe_monthly_price_id')) {
                $table->dropColumn('stripe_monthly_price_id');
            }
            if (Schema::hasColumn('subscription_plans', 'stripe_yearly_price_id')) {
                $table->dropColumn('stripe_yearly_price_id');
            }

            // Add new stripe price ID column
            $table->string('stripe_price_id')->nullable()->comment('Stripe Price ID for this plan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subscription_plans', function (Blueprint $table) {
            $table->dropColumn('stripe_price_id');
        });
    }
};
