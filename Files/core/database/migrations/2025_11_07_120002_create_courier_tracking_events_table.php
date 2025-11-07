<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations - Courier API Tracking Events (FR-24, FR-25)
     */
    public function up(): void
    {
        Schema::create('courier_tracking_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('courier_info_id')->constrained('courier_infos')->onDelete('cascade');
            $table->foreignId('courier_configuration_id')->nullable()->constrained('courier_configurations')->onDelete('set null');

            $table->string('tracking_number')->index();
            $table->string('carrier_name'); // Aramex, DHL, FedEx, UPS
            $table->string('event_type'); // picked_up, in_transit, out_for_delivery, delivered, exception
            $table->string('status_code')->nullable();
            $table->text('description');
            $table->string('location')->nullable();
            $table->dateTime('event_time');

            // Exception tracking (FR-25)
            $table->boolean('is_exception')->default(false);
            $table->string('exception_type')->nullable(); // delay, wrong_address, damaged, lost
            $table->text('exception_details')->nullable();
            $table->boolean('customer_notified')->default(false);

            // API response data
            $table->json('raw_data')->nullable();

            $table->timestamps();

            $table->index(['courier_info_id', 'event_time']);
            $table->index('is_exception');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courier_tracking_events');
    }
};
