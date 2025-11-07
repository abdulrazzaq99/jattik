<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations - WhatsApp Integration (FR-30)
     */
    public function up(): void
    {
        Schema::create('whatsapp_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->nullable()->constrained('customers')->onDelete('cascade');

            $table->string('phone_number')->index();
            $table->string('message_id')->nullable(); // WhatsApp message ID
            $table->string('conversation_id')->nullable()->index(); // Group related messages

            $table->string('direction'); // inbound, outbound
            $table->string('message_type'); // text, otp, order_update, faq_response, image
            $table->text('message_content');
            $table->json('metadata')->nullable(); // Button responses, quick replies, etc.

            // For OTPs
            $table->string('otp_code')->nullable();
            $table->timestamp('otp_expires_at')->nullable();
            $table->boolean('otp_verified')->default(false);

            // For order updates
            $table->foreignId('courier_info_id')->nullable()->constrained('courier_infos')->onDelete('cascade');
            $table->string('update_type')->nullable(); // status_change, tracking_update, etc.

            $table->string('status'); // sent, delivered, read, failed
            $table->string('whatsapp_status')->nullable(); // Raw WhatsApp status
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('read_at')->nullable();

            // Bot interaction
            $table->string('bot_intent')->nullable(); // track_shipment, faq, support, etc.
            $table->boolean('handled_by_bot')->default(true);
            $table->boolean('escalated_to_human')->default(false);

            $table->timestamps();

            $table->index(['phone_number', 'created_at']);
            $table->index('conversation_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whatsapp_messages');
    }
};
