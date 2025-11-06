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
        Schema::table('customers', function (Blueprint $table) {
            // Authentication fields
            if (!Schema::hasColumn('customers', 'password')) {
                $table->string('password')->nullable()->after('mobile');
            }
            if (!Schema::hasColumn('customers', 'username')) {
                $table->string('username', 40)->nullable()->unique()->after('lastname');
            }
            if (!Schema::hasColumn('customers', 'remember_token')) {
                $table->string('remember_token', 100)->nullable()->after('password');
            }

            // Verification timestamps
            if (!Schema::hasColumn('customers', 'email_verified_at')) {
                $table->timestamp('email_verified_at')->nullable()->after('email');
            }
            if (!Schema::hasColumn('customers', 'mobile_verified_at')) {
                $table->timestamp('mobile_verified_at')->nullable()->after('mobile');
            }

            // OTP fields
            if (!Schema::hasColumn('customers', 'otp_code')) {
                $table->string('otp_code', 6)->nullable()->after('password');
            }
            if (!Schema::hasColumn('customers', 'otp_expiry')) {
                $table->timestamp('otp_expiry')->nullable()->after('otp_code');
            }
            if (!Schema::hasColumn('customers', 'otp_type')) {
                $table->enum('otp_type', ['email', 'sms'])->nullable()->after('otp_expiry');
            }

            // Status and contact info
            if (!Schema::hasColumn('customers', 'status')) {
                $table->tinyInteger('status')->default(1)->comment('0=inactive, 1=active, 2=banned')->after('state');
            }
            if (!Schema::hasColumn('customers', 'country_code')) {
                $table->string('country_code', 10)->default('+966')->comment('KSA default +966')->after('mobile_verified_at');
            }
            if (!Schema::hasColumn('customers', 'postal_code')) {
                $table->string('postal_code', 20)->nullable()->after('state');
            }

            // Activity tracking
            if (!Schema::hasColumn('customers', 'last_login_at')) {
                $table->timestamp('last_login_at')->nullable()->after('status');
            }
            if (!Schema::hasColumn('customers', 'last_order_at')) {
                $table->timestamp('last_order_at')->nullable()->after('last_login_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn([
                'password',
                'username',
                'remember_token',
                'email_verified_at',
                'mobile_verified_at',
                'otp_code',
                'otp_expiry',
                'otp_type',
                'status',
                'country_code',
                'postal_code',
                'last_login_at',
                'last_order_at'
            ]);
        });
    }
};
