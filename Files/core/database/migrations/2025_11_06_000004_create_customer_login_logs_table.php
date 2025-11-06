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
        if (!Schema::hasTable('customer_login_logs')) {
            Schema::create('customer_login_logs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('customer_id');
                $table->timestamp('login_at')->useCurrent();
                $table->timestamp('logout_at')->nullable();
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->enum('login_method', ['otp_email', 'otp_sms', 'otp_whatsapp', 'password']);
                $table->string('session_id')->nullable();
                $table->timestamps();

                // Indexes
                $table->index('customer_id', 'idx_customer_id');
                $table->index('login_at', 'idx_login_at');

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
        Schema::dropIfExists('customer_login_logs');
    }
};
