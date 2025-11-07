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
        if (!Schema::hasTable('coupon_usage')) {
            Schema::create('coupon_usage', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('coupon_id');
                $table->unsignedBigInteger('customer_id');
                $table->unsignedBigInteger('payment_id')->nullable();
                $table->decimal('discount_amount', 10, 2)->comment('Discount amount applied');
                $table->timestamp('used_at')->useCurrent();

                // Indexes
                $table->index('coupon_id', 'idx_usage_coupon');
                $table->index('customer_id', 'idx_usage_customer');
                $table->index(['customer_id', 'coupon_id'], 'idx_customer_coupon');

                // Foreign keys
                $table->foreign('coupon_id')->references('id')->on('coupons')->onDelete('cascade');
                $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
                $table->foreign('payment_id')->references('id')->on('payments')->onDelete('set null');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupon_usage');
    }
};
