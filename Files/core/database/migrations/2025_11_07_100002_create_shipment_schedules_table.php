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
        Schema::create('shipment_schedules', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., "First Week of Month", "Mid-Month", etc.
            $table->tinyInteger('day_of_month'); // 1-31
            $table->string('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('cutoff_days_before')->default(2); // Days before schedule to stop accepting
            $table->timestamps();

            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipment_schedules');
    }
};
