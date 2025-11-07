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
        Schema::create('courier_configurations', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., "Aramex", "DHL", "FedEx"
            $table->string('code')->unique(); // e.g., "aramex", "dhl"
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('api_endpoint')->nullable();
            $table->string('api_key')->nullable();
            $table->string('api_secret')->nullable();
            $table->string('account_number')->nullable();
            $table->json('additional_config')->nullable(); // For courier-specific settings
            $table->decimal('base_rate', 10, 2)->default(0);
            $table->decimal('per_kg_rate', 10, 2)->default(0);
            $table->decimal('insurance_percentage', 5, 2)->default(0); // % of declared value
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index('is_active');
            $table->index('sort_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courier_configurations');
    }
};
