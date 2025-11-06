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
        if (!Schema::hasTable('otp_logs')) {
            Schema::create('otp_logs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('customer_id')->nullable();
                $table->string('email')->nullable();
                $table->string('mobile', 40)->nullable();
                $table->string('otp_code', 6);
                $table->enum('otp_type', ['email', 'sms', 'whatsapp']);
                $table->enum('purpose', ['registration', 'login', 'password_reset']);
                $table->timestamp('sent_at')->useCurrent();
                $table->timestamp('verified_at')->nullable();
                $table->timestamp('expires_at');
                $table->integer('attempts')->default(0)->comment('Number of verification attempts');
                $table->enum('status', ['pending', 'verified', 'expired', 'failed'])->default('pending');
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->timestamps();

                // Indexes
                $table->index('customer_id', 'idx_customer_id');
                $table->index('email', 'idx_email');
                $table->index('mobile', 'idx_mobile');
                $table->index('status', 'idx_status');
                $table->index('expires_at', 'idx_expires_at');

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
        Schema::dropIfExists('otp_logs');
    }
};
