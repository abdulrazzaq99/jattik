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
        if (!Schema::hasTable('subscription_plans')) {
            Schema::create('subscription_plans', function (Blueprint $table) {
                $table->id();
                $table->string('name', 100); // e.g., "Free Plan", "Monthly Premium", "Yearly Premium"
                $table->string('slug', 100)->unique(); // e.g., "free", "monthly-premium", "yearly-premium"
                $table->enum('type', ['free', 'monthly', 'yearly'])->default('free');
                $table->decimal('price', 10, 2)->default(0.00)->comment('Price in SAR');
                $table->enum('billing_period', ['none', 'month', 'year'])->default('none');
                $table->integer('billing_cycle')->default(1)->comment('1 month, 12 months, etc.');

                // Features (JSON format)
                $table->json('features')->nullable()->comment('Plan features: includes_insurance, max_shipments, etc.');

                // Insurance coverage
                $table->boolean('includes_insurance')->default(false)->comment('Premium plans include free insurance');
                $table->decimal('insurance_coverage', 10, 2)->nullable()->comment('Default insurance coverage amount');

                // Limits
                $table->integer('max_shipments_per_month')->nullable()->comment('null = unlimited');

                // Status
                $table->tinyInteger('status')->default(1)->comment('0=inactive, 1=active');
                $table->integer('sort_order')->default(0)->comment('Display order');

                $table->text('description')->nullable();
                $table->timestamps();

                // Indexes
                $table->index('type', 'idx_plan_type');
                $table->index('status', 'idx_plan_status');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_plans');
    }
};
