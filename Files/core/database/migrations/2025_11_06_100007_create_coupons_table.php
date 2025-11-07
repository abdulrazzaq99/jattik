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
        if (!Schema::hasTable('coupons')) {
            Schema::create('coupons', function (Blueprint $table) {
                $table->id();
                $table->string('code', 50)->unique()->comment('Unique coupon code');
                $table->string('name', 100)->comment('Coupon name/description');

                // Discount details
                $table->enum('type', ['percentage', 'fixed'])->default('percentage');
                $table->decimal('value', 10, 2)->comment('Percentage (e.g., 10 for 10%) or Fixed amount in SAR');
                $table->decimal('max_discount', 10, 2)->nullable()->comment('Maximum discount amount for percentage coupons');
                $table->decimal('min_purchase', 10, 2)->default(0.00)->comment('Minimum purchase amount required');

                // Applicability
                $table->enum('applicable_to', ['all', 'subscriptions', 'shipments', 'insurance'])->default('all');
                $table->json('applicable_plans')->nullable()->comment('Specific subscription plan IDs (null = all plans)');

                // Usage limits
                $table->integer('usage_limit')->nullable()->comment('Total times coupon can be used (null = unlimited)');
                $table->integer('usage_limit_per_customer')->default(1)->comment('Times each customer can use');
                $table->integer('total_used')->default(0)->comment('Total times coupon has been used');

                // Validity
                $table->timestamp('valid_from')->useCurrent();
                $table->timestamp('valid_until')->nullable();
                $table->tinyInteger('status')->default(1)->comment('0=inactive, 1=active');

                $table->timestamps();

                // Indexes
                $table->index('code', 'idx_coupon_code');
                $table->index('status', 'idx_coupon_status');
                $table->index('valid_until', 'idx_valid_until');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
