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
        if (!Schema::hasTable('customer_subscriptions')) {
            Schema::create('customer_subscriptions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('customer_id');
                $table->unsignedBigInteger('subscription_plan_id');

                // Subscription details
                $table->enum('status', ['active', 'cancelled', 'expired', 'pending_payment'])->default('pending_payment');
                $table->timestamp('started_at')->nullable();
                $table->timestamp('expires_at')->nullable();
                $table->timestamp('next_billing_date')->nullable();

                // Auto-renewal
                $table->boolean('auto_renew')->default(true)->comment('Auto-renew subscription on expiry');
                $table->timestamp('cancelled_at')->nullable();
                $table->string('cancellation_reason')->nullable();

                // Payment reference
                $table->unsignedBigInteger('last_payment_id')->nullable()->comment('Reference to last successful payment');

                // Usage tracking
                $table->integer('shipments_this_period')->default(0)->comment('Track shipments in current billing period');
                $table->timestamp('period_started_at')->nullable();

                $table->timestamps();

                // Indexes
                $table->index('customer_id', 'idx_customer_id');
                $table->index('subscription_plan_id', 'idx_plan_id');
                $table->index('status', 'idx_subscription_status');
                $table->index('expires_at', 'idx_expires_at');
                $table->index('next_billing_date', 'idx_next_billing');

                // Foreign keys
                $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
                $table->foreign('subscription_plan_id')->references('id')->on('subscription_plans')->onDelete('restrict');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_subscriptions');
    }
};
