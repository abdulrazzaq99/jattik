<?php

namespace Database\Seeders;

use App\Models\Coupon;
use Illuminate\Database\Seeder;

class CouponsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $coupons = [
            // Welcome discount for new subscribers
            [
                'code' => 'WELCOME10',
                'name' => 'Get 10% off on your first subscription',
                'type' => 'percentage',
                'value' => 10.00,
                'max_discount' => 50.00,
                'min_purchase' => 0.00,
                'applicable_to' => 'subscriptions',
                'applicable_plans' => [2, 3], // Monthly and Yearly premium
                'usage_limit' => 100,
                'usage_limit_per_customer' => 1,
                'valid_from' => now(),
                'valid_until' => now()->addMonths(3),
                'status' => 1,
            ],

            // Fixed discount on yearly subscription
            [
                'code' => 'YEARLY100',
                'name' => '100 SAR off on yearly premium subscription',
                'type' => 'fixed',
                'value' => 100.00,
                'max_discount' => null,
                'min_purchase' => 500.00,
                'applicable_to' => 'subscriptions',
                'applicable_plans' => [3], // Yearly premium only
                'usage_limit' => 50,
                'usage_limit_per_customer' => 1,
                'valid_from' => now(),
                'valid_until' => now()->addMonths(6),
                'status' => 1,
            ],

            // Shipment discount
            [
                'code' => 'SHIP15',
                'name' => '15% off on all shipments',
                'type' => 'percentage',
                'value' => 15.00,
                'max_discount' => 100.00,
                'min_purchase' => 50.00,
                'applicable_to' => 'shipments',
                'applicable_plans' => null,
                'usage_limit' => null, // Unlimited usage
                'usage_limit_per_customer' => 5,
                'valid_from' => now(),
                'valid_until' => now()->addYear(),
                'status' => 1,
            ],

            // Insurance discount
            [
                'code' => 'INSURE20',
                'name' => '20% off on insurance policies',
                'type' => 'percentage',
                'value' => 20.00,
                'max_discount' => 50.00,
                'min_purchase' => 0.00,
                'applicable_to' => 'insurance',
                'applicable_plans' => null,
                'usage_limit' => null,
                'usage_limit_per_customer' => 3,
                'valid_from' => now(),
                'valid_until' => now()->addMonths(12),
                'status' => 1,
            ],

            // Universal discount - applies to everything
            [
                'code' => 'SAVE25',
                'name' => 'Save 25 SAR on any purchase over 200 SAR',
                'type' => 'fixed',
                'value' => 25.00,
                'max_discount' => null,
                'min_purchase' => 200.00,
                'applicable_to' => 'all',
                'applicable_plans' => null,
                'usage_limit' => 200,
                'usage_limit_per_customer' => 2,
                'valid_from' => now(),
                'valid_until' => now()->addMonths(6),
                'status' => 1,
            ],

            // Premium subscriber exclusive
            [
                'code' => 'VIP50',
                'name' => '50 SAR off for premium subscribers',
                'type' => 'fixed',
                'value' => 50.00,
                'max_discount' => null,
                'min_purchase' => 100.00,
                'applicable_to' => 'all',
                'applicable_plans' => null,
                'usage_limit' => null,
                'usage_limit_per_customer' => 10,
                'valid_from' => now(),
                'valid_until' => now()->addYear(),
                'status' => 1,
            ],

            // Flash sale - short validity
            [
                'code' => 'FLASH30',
                'name' => '30% off - Limited time offer!',
                'type' => 'percentage',
                'value' => 30.00,
                'max_discount' => 150.00,
                'min_purchase' => 100.00,
                'applicable_to' => 'all',
                'applicable_plans' => null,
                'usage_limit' => 25,
                'usage_limit_per_customer' => 1,
                'valid_from' => now(),
                'valid_until' => now()->addDays(7),
                'status' => 1,
            ],

            // Expired coupon (for testing validation)
            [
                'code' => 'EXPIRED',
                'name' => 'This coupon has expired',
                'type' => 'percentage',
                'value' => 50.00,
                'max_discount' => 100.00,
                'min_purchase' => 0.00,
                'applicable_to' => 'all',
                'applicable_plans' => null,
                'usage_limit' => 10,
                'usage_limit_per_customer' => 1,
                'valid_from' => now()->subMonths(2),
                'valid_until' => now()->subMonth(),
                'status' => 0,
            ],

            // First month free trial conversion
            [
                'code' => 'FREEMONTH',
                'name' => 'Get your first month of premium subscription free',
                'type' => 'percentage',
                'value' => 100.00,
                'max_discount' => 99.00, // Monthly premium price
                'min_purchase' => 0.00,
                'applicable_to' => 'subscriptions',
                'applicable_plans' => [2], // Monthly premium only
                'usage_limit' => 50,
                'usage_limit_per_customer' => 1,
                'valid_from' => now(),
                'valid_until' => now()->addMonths(3),
                'status' => 1,
            ],

            // Black Friday special
            [
                'code' => 'BLACKFRIDAY',
                'name' => 'Black Friday - 40% off everything!',
                'type' => 'percentage',
                'value' => 40.00,
                'max_discount' => 400.00,
                'min_purchase' => 50.00,
                'applicable_to' => 'all',
                'applicable_plans' => null,
                'usage_limit' => null,
                'usage_limit_per_customer' => 3,
                'valid_from' => now(),
                'valid_until' => now()->addMonths(1),
                'status' => 1,
            ],
        ];

        foreach ($coupons as $couponData) {
            Coupon::create($couponData);
        }

        $this->command->info('✅ Sample coupons created successfully!');
        $this->command->info('');
        $this->command->info('Available Coupon Codes:');
        $this->command->table(
            ['Code', 'Type', 'Value', 'Applicable To', 'Status'],
            collect($coupons)->map(function ($coupon) {
                return [
                    $coupon['code'],
                    ucfirst($coupon['type']),
                    $coupon['type'] === 'percentage' ? $coupon['value'] . '%' : $coupon['value'] . ' SAR',
                    ucfirst(str_replace('_', ' ', $coupon['applicable_to'])),
                    $coupon['status'] === 1 ? '✅ Active' : '❌ Inactive',
                ];
            })
        );
    }
}
