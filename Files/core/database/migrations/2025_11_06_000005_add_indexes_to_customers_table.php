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
        // Get all existing indexes
        $indexes = DB::select("SHOW INDEX FROM customers WHERE Key_name != 'PRIMARY'");
        $existingIndexes = collect($indexes)->pluck('Key_name')->unique()->toArray();

        Schema::table('customers', function (Blueprint $table) use ($existingIndexes) {
            // Add performance indexes only if they don't exist
            if (!in_array('idx_email', $existingIndexes)) {
                $table->index('email', 'idx_email');
            }
            if (!in_array('idx_mobile', $existingIndexes)) {
                $table->index('mobile', 'idx_mobile');
            }
            if (!in_array('idx_username', $existingIndexes)) {
                $table->index('username', 'idx_username');
            }
            if (!in_array('idx_status', $existingIndexes)) {
                $table->index('status', 'idx_status');
            }
            if (!in_array('idx_last_order_at', $existingIndexes)) {
                $table->index('last_order_at', 'idx_last_order_at');
            }
        });

        // Update existing customers to set default status
        DB::table('customers')->whereNull('status')->update(['status' => 1]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropIndex('idx_email');
            $table->dropIndex('idx_mobile');
            $table->dropIndex('idx_username');
            $table->dropIndex('idx_status');
            $table->dropIndex('idx_last_order_at');
        });
    }
};
