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
        Schema::table('payments', function (Blueprint $table) {
            if (!Schema::hasColumn('payments', 'coupon_id')) {
                $table->unsignedBigInteger('coupon_id')->nullable()->after('customer_id');
            }

            if (!Schema::hasColumn('payments', 'coupon_code')) {
                $table->string('coupon_code', 50)->nullable()->after('coupon_id');
            }

            if (!Schema::hasColumn('payments', 'original_amount')) {
                $table->decimal('original_amount', 10, 2)->nullable()->after('amount')->comment('Amount before discount');
            }

            if (!Schema::hasColumn('payments', 'discount_amount')) {
                $table->decimal('discount_amount', 10, 2)->default(0.00)->after('original_amount')->comment('Discount applied');
            }

            // Add index
            if (!Schema::hasColumn('payments', 'coupon_id')) {
                $table->index('coupon_id', 'idx_payment_coupon');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            if (Schema::hasColumn('payments', 'idx_payment_coupon')) {
                $table->dropIndex('idx_payment_coupon');
            }

            $columns = ['coupon_id', 'coupon_code', 'original_amount', 'discount_amount'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('payments', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
