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
        if (!Schema::hasTable('payments')) {
            Schema::create('payments', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('customer_id');

                // Polymorphic relationship (can be subscription, courier, insurance)
                $table->string('payable_type')->comment('App\Models\Subscription, CourierInfo, Insurance');
                $table->unsignedBigInteger('payable_id');

                // Payment details
                $table->string('payment_reference', 100)->unique()->comment('Unique payment reference');
                $table->decimal('amount', 10, 2);
                $table->string('currency', 3)->default('SAR');

                // Gateway details
                $table->string('payment_method', 50)->comment('stripe, paypal, etc.');
                $table->string('transaction_id')->nullable()->comment('Gateway transaction ID');
                $table->enum('status', ['pending', 'processing', 'completed', 'failed', 'refunded', 'cancelled'])->default('pending');

                // Gateway response
                $table->text('gateway_response')->nullable()->comment('JSON response from payment gateway');
                $table->text('failure_reason')->nullable();

                // Timestamps
                $table->timestamp('paid_at')->nullable();
                $table->timestamp('refunded_at')->nullable();
                $table->timestamps();

                // Indexes
                $table->index('customer_id', 'idx_payment_customer');
                $table->index(['payable_type', 'payable_id'], 'idx_payable');
                $table->index('status', 'idx_payment_status');
                $table->index('payment_reference', 'idx_payment_ref');
                $table->index('transaction_id', 'idx_transaction_id');

                // Foreign key
                $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
