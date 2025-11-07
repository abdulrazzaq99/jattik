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
            $table->foreignId('warehouse_holding_id')->nullable()->after('id')->constrained('warehouse_holdings')->onDelete('set null');
            $table->foreignId('shipment_schedule_id')->nullable()->after('warehouse_holding_id')->constrained('shipment_schedules')->onDelete('set null');
            $table->foreignId('shipping_quote_id')->nullable()->after('shipment_schedule_id')->constrained('shipping_quotes')->onDelete('set null');
            $table->date('scheduled_ship_date')->nullable()->after('shipping_quote_id');
            $table->date('original_ship_date')->nullable()->after('scheduled_ship_date'); // For tracking extensions
            $table->tinyInteger('address_locked')->default(0)->after('original_ship_date'); // 0=Can change, 1=Locked after dispatch
            $table->boolean('is_consolidated')->default(false)->after('address_locked');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courier_infos', function (Blueprint $table) {
            $table->dropForeign(['warehouse_holding_id']);
            $table->dropForeign(['shipment_schedule_id']);
            $table->dropForeign(['shipping_quote_id']);
            $table->dropColumn([
                'warehouse_holding_id',
                'shipment_schedule_id',
                'shipping_quote_id',
                'scheduled_ship_date',
                'original_ship_date',
                'address_locked',
                'is_consolidated'
            ]);
        });
    }
};
