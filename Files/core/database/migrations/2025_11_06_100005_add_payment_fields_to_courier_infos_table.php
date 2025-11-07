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
        Schema::table('courier_infos', function (Blueprint $table) {
            // Payment fields
            if (!Schema::hasColumn('courier_infos', 'payment_status')) {
                $table->enum('payment_status', ['pending', 'paid', 'refunded'])->default('pending')->after('status');
            }

            if (!Schema::hasColumn('courier_infos', 'shipment_cost')) {
                $table->decimal('shipment_cost', 10, 2)->default(0.00)->after('payment_status')->comment('Base shipment cost');
            }

            if (!Schema::hasColumn('courier_infos', 'insurance_cost')) {
                $table->decimal('insurance_cost', 10, 2)->default(0.00)->after('shipment_cost')->comment('Insurance premium cost');
            }

            if (!Schema::hasColumn('courier_infos', 'total_cost')) {
                $table->decimal('total_cost', 10, 2)->default(0.00)->after('insurance_cost')->comment('Total cost (shipment + insurance)');
            }

            // Insurance reference
            if (!Schema::hasColumn('courier_infos', 'insurance_policy_id')) {
                $table->unsignedBigInteger('insurance_policy_id')->nullable()->after('total_cost');
            }

            // Payment reference
            if (!Schema::hasColumn('courier_infos', 'payment_id')) {
                $table->unsignedBigInteger('payment_id')->nullable()->after('insurance_policy_id')->comment('Reference to payment record');
            }

            // Payment deadline (must pay before dispatch)
            if (!Schema::hasColumn('courier_infos', 'payment_deadline')) {
                $table->timestamp('payment_deadline')->nullable()->after('payment_id')->comment('Deadline to pay before dispatch');
            }

            if (!Schema::hasColumn('courier_infos', 'paid_at')) {
                $table->timestamp('paid_at')->nullable()->after('payment_deadline');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courier_infos', function (Blueprint $table) {
            if (Schema::hasColumn('courier_infos', 'payment_status')) {
                $table->dropColumn('payment_status');
            }
            if (Schema::hasColumn('courier_infos', 'shipment_cost')) {
                $table->dropColumn('shipment_cost');
            }
            if (Schema::hasColumn('courier_infos', 'insurance_cost')) {
                $table->dropColumn('insurance_cost');
            }
            if (Schema::hasColumn('courier_infos', 'total_cost')) {
                $table->dropColumn('total_cost');
            }
            if (Schema::hasColumn('courier_infos', 'insurance_policy_id')) {
                $table->dropColumn('insurance_policy_id');
            }
            if (Schema::hasColumn('courier_infos', 'payment_id')) {
                $table->dropColumn('payment_id');
            }
            if (Schema::hasColumn('courier_infos', 'payment_deadline')) {
                $table->dropColumn('payment_deadline');
            }
            if (Schema::hasColumn('courier_infos', 'paid_at')) {
                $table->dropColumn('paid_at');
            }
        });
    }
};
