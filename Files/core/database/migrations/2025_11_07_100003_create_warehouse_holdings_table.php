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
        Schema::create('warehouse_holdings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            $table->foreignId('branch_id')->constrained('branches')->onDelete('cascade');
            $table->string('holding_code')->unique(); // Unique identifier for this holding
            $table->date('received_date');
            $table->date('scheduled_ship_date')->nullable();
            $table->date('actual_ship_date')->nullable();
            $table->date('max_holding_date'); // received_date + 90 days
            $table->tinyInteger('status')->default(0); // 0=Holding, 1=Ready, 2=Shipped, 3=Expired
            $table->decimal('total_weight', 10, 2)->default(0);
            $table->decimal('total_volume', 10, 2)->default(0);
            $table->integer('package_count')->default(0);
            $table->text('notes')->nullable();
            $table->foreignId('consolidated_by_staff_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('consolidated_at')->nullable();
            $table->timestamps();

            $table->index(['customer_id', 'status']);
            $table->index(['branch_id', 'status']);
            $table->index('scheduled_ship_date');
            $table->index('max_holding_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warehouse_holdings');
    }
};
