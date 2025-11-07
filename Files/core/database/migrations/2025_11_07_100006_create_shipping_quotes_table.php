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
        Schema::create('shipping_quotes', function (Blueprint $table) {
            $table->id();
            $table->string('quote_number')->unique();
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            $table->foreignId('warehouse_holding_id')->nullable()->constrained('warehouse_holdings')->onDelete('set null');
            $table->foreignId('courier_configuration_id')->nullable()->constrained('courier_configurations')->onDelete('set null');
            $table->string('courier_name'); // Stored in case config is deleted

            // Shipment details
            $table->foreignId('origin_address_id')->nullable()->constrained('customer_addresses')->onDelete('set null');
            $table->foreignId('destination_address_id')->nullable()->constrained('customer_addresses')->onDelete('set null');
            $table->decimal('total_weight', 10, 2);
            $table->decimal('total_volume', 10, 2)->nullable();
            $table->decimal('declared_value', 10, 2)->default(0);
            $table->integer('package_count')->default(1);

            // Fee breakdown
            $table->decimal('base_fee', 10, 2)->default(0);
            $table->decimal('weight_fee', 10, 2)->default(0);
            $table->decimal('insurance_fee', 10, 2)->default(0);
            $table->decimal('handling_fee', 10, 2)->default(0);
            $table->decimal('customs_fee', 10, 2)->default(0);
            $table->decimal('fuel_surcharge', 10, 2)->default(0);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('total_fee', 10, 2);

            // Quote metadata
            $table->tinyInteger('quote_type')->default(1); // 1=Customer, 2=Employee
            $table->foreignId('calculated_by_staff_id')->nullable()->constrained('users')->onDelete('set null');
            $table->tinyInteger('status')->default(0); // 0=Draft, 1=Sent, 2=Accepted, 3=Expired
            $table->date('valid_until')->nullable();
            $table->text('notes')->nullable();
            $table->json('calculation_details')->nullable(); // Store API response or calculation breakdown

            $table->timestamps();

            $table->index(['customer_id', 'status']);
            $table->index('warehouse_holding_id');
            $table->index('valid_until');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipping_quotes');
    }
};
