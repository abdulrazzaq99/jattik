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
        Schema::create('warehouse_packages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('warehouse_holding_id')->constrained('warehouse_holdings')->onDelete('cascade');
            $table->string('package_code')->unique();
            $table->string('description');
            $table->decimal('weight', 10, 2);
            $table->decimal('length', 10, 2)->nullable();
            $table->decimal('width', 10, 2)->nullable();
            $table->decimal('height', 10, 2)->nullable();
            $table->decimal('volume', 10, 2)->nullable(); // Calculated or manual
            $table->decimal('declared_value', 10, 2)->default(0);
            $table->string('category')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('warehouse_holding_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warehouse_packages');
    }
};
