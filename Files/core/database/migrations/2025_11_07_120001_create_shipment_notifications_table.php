<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations - Shipment Notifications (FR-22, FR-23, FR-26, FR-27)
     */
    public function up(): void
    {
        Schema::create('shipment_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            $table->foreignId('courier_info_id')->nullable()->constrained('courier_infos')->onDelete('cascade');
            $table->foreignId('warehouse_holding_id')->nullable()->constrained('warehouse_holdings')->onDelete('cascade');

            $table->string('notification_type'); // facility_arrival, dispatched, tracking_link, fee_quote
            $table->string('title');
            $table->text('message');
            $table->json('metadata')->nullable(); // tracking_url, payment_link, etc.

            $table->boolean('sent_via_email')->default(false);
            $table->boolean('sent_via_sms')->default(false);
            $table->boolean('sent_via_whatsapp')->default(false);

            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamp('sent_at')->nullable();

            $table->timestamps();

            $table->index(['customer_id', 'is_read']);
            $table->index('notification_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipment_notifications');
    }
};
