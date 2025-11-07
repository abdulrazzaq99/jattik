<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations - Ratings & Feedback (FR-33)
     */
    public function up(): void
    {
        Schema::create('shipment_ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            $table->foreignId('courier_info_id')->constrained('courier_infos')->onDelete('cascade');

            // Rating dimensions
            $table->tinyInteger('overall_rating')->unsigned(); // 1-5
            $table->tinyInteger('speed_rating')->nullable(); // 1-5
            $table->tinyInteger('packaging_rating')->nullable(); // 1-5
            $table->tinyInteger('communication_rating')->nullable(); // 1-5
            $table->tinyInteger('value_rating')->nullable(); // 1-5

            $table->text('comment')->nullable();
            $table->json('tags')->nullable(); // ['fast', 'reliable', 'professional', etc.]

            $table->boolean('would_recommend')->default(true);
            $table->boolean('is_public')->default(true);
            $table->boolean('is_verified')->default(true); // Only delivered shipments can rate

            // Admin moderation
            $table->boolean('is_approved')->default(true);
            $table->text('admin_notes')->nullable();

            $table->timestamps();

            $table->unique(['customer_id', 'courier_info_id']); // One rating per shipment
            $table->index('overall_rating');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipment_ratings');
    }
};
