<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations - Delivery Speed Options (FR-34)
     */
    public function up(): void
    {
        Schema::table('courier_infos', function (Blueprint $table) {
            $table->string('delivery_speed')->default('standard')->after('status'); // standard, express
            $table->decimal('speed_surcharge', 10, 2)->default(0)->after('delivery_speed');
            $table->integer('estimated_delivery_days')->nullable()->after('speed_surcharge');
            $table->date('estimated_delivery_date')->nullable()->after('estimated_delivery_days');
        });

        Schema::table('shipping_quotes', function (Blueprint $table) {
            $table->string('delivery_speed')->default('standard')->after('courier_configuration_id');
            $table->decimal('express_surcharge', 10, 2)->default(0)->after('delivery_speed');
        });

        Schema::table('warehouse_holdings', function (Blueprint $table) {
            $table->string('preferred_delivery_speed')->default('standard')->after('scheduled_ship_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courier_infos', function (Blueprint $table) {
            $table->dropColumn(['delivery_speed', 'speed_surcharge', 'estimated_delivery_days', 'estimated_delivery_date']);
        });

        Schema::table('shipping_quotes', function (Blueprint $table) {
            $table->dropColumn(['delivery_speed', 'express_surcharge']);
        });

        Schema::table('warehouse_holdings', function (Blueprint $table) {
            $table->dropColumn('preferred_delivery_speed');
        });
    }
};
