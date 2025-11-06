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
        if (!Schema::hasTable('virtual_addresses')) {
            Schema::create('virtual_addresses', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('customer_id');
                $table->string('address_code', 20)->unique()->comment('Unique virtual address code (e.g., VA-12345678)');
                $table->text('full_address')->comment('Complete formatted virtual address');
                $table->enum('status', ['active', 'inactive', 'cancelled'])->default('active');
                $table->timestamp('assigned_at')->useCurrent();
                $table->timestamp('cancelled_at')->nullable();
                $table->string('cancellation_reason')->nullable()->comment('Reason for cancellation (e.g., 1 year inactivity)');
                $table->timestamps();

                // Indexes
                $table->index('customer_id', 'idx_customer_id');
                $table->index('address_code', 'idx_address_code');
                $table->index('status', 'idx_status');

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
        Schema::dropIfExists('virtual_addresses');
    }
};
