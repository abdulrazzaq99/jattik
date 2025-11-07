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
        Schema::table('customers', function (Blueprint $table) {
            // Active subscription reference
            if (!Schema::hasColumn('customers', 'active_subscription_id')) {
                $table->unsignedBigInteger('active_subscription_id')->nullable()->after('last_order_at')->comment('Reference to active subscription');
            }

            // Subscription type for quick access
            if (!Schema::hasColumn('customers', 'subscription_type')) {
                $table->enum('subscription_type', ['free', 'monthly', 'yearly'])->default('free')->after('active_subscription_id');
            }

            // Premium status for quick checks
            if (!Schema::hasColumn('customers', 'is_premium')) {
                $table->boolean('is_premium')->default(false)->after('subscription_type')->comment('True if customer has active premium subscription');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            if (Schema::hasColumn('customers', 'active_subscription_id')) {
                $table->dropColumn('active_subscription_id');
            }
            if (Schema::hasColumn('customers', 'subscription_type')) {
                $table->dropColumn('subscription_type');
            }
            if (Schema::hasColumn('customers', 'is_premium')) {
                $table->dropColumn('is_premium');
            }
        });
    }
};
