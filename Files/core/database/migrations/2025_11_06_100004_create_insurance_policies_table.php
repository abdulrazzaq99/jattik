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
        if (!Schema::hasTable('insurance_policies')) {
            Schema::create('insurance_policies', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('courier_info_id');
                $table->unsignedBigInteger('customer_id');

                // Policy details
                $table->string('policy_number', 50)->unique()->comment('Unique insurance policy number');
                $table->decimal('coverage_amount', 10, 2)->comment('Maximum coverage in SAR');
                $table->decimal('premium_amount', 10, 2)->default(0.00)->comment('Premium paid (0 for free insurance)');

                // Free insurance for premium subscribers
                $table->boolean('is_free')->default(false)->comment('True if provided free with premium subscription');
                $table->unsignedBigInteger('subscription_id')->nullable()->comment('Reference to subscription if insurance is free');

                // Status
                $table->enum('status', ['active', 'claimed', 'expired', 'cancelled'])->default('active');

                // Claim details
                $table->decimal('claim_amount', 10, 2)->nullable()->comment('Amount claimed if policy was used');
                $table->timestamp('claimed_at')->nullable();
                $table->text('claim_notes')->nullable();

                // Validity
                $table->timestamp('purchased_at')->useCurrent();
                $table->timestamp('expires_at')->nullable();

                $table->timestamps();

                // Indexes
                $table->index('courier_info_id', 'idx_courier_info');
                $table->index('customer_id', 'idx_insurance_customer');
                $table->index('policy_number', 'idx_policy_number');
                $table->index('status', 'idx_insurance_status');

                // Foreign keys
                $table->foreign('courier_info_id')->references('id')->on('courier_infos')->onDelete('cascade');
                $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
                $table->foreign('subscription_id')->references('id')->on('customer_subscriptions')->onDelete('set null');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('insurance_policies');
    }
};
