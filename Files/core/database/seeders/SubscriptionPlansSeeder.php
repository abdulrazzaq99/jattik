<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubscriptionPlansSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Free Plan',
                'slug' => 'free',
                'type' => 'free',
                'price' => 0.00,
                'billing_period' => 'none',
                'billing_cycle' => 1,
                'features' => json_encode([
                    'Pay per shipment',
                    'Basic tracking',
                    'Standard delivery',
                    'Optional insurance purchase',
                ]),
                'includes_insurance' => false,
                'insurance_coverage' => null,
                'max_shipments_per_month' => null,
                'status' => 1,
                'sort_order' => 1,
                'description' => 'Perfect for occasional shippers. Pay only when you ship.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Monthly Premium',
                'slug' => 'monthly-premium',
                'type' => 'monthly',
                'price' => 99.00,
                'billing_period' => 'month',
                'billing_cycle' => 1,
                'features' => json_encode([
                    'Unlimited shipments',
                    'Free insurance on all shipments',
                    'Priority support',
                    'Advanced tracking',
                    'Discounted shipping rates',
                    'Auto-renewal',
                ]),
                'includes_insurance' => true,
                'insurance_coverage' => 5000.00,
                'max_shipments_per_month' => null,
                'status' => 1,
                'sort_order' => 2,
                'description' => 'Best for regular shippers. Includes free insurance up to 5,000 SAR on all shipments.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Yearly Premium',
                'slug' => 'yearly-premium',
                'type' => 'yearly',
                'price' => 999.00,
                'billing_period' => 'year',
                'billing_cycle' => 1,
                'features' => json_encode([
                    'Unlimited shipments',
                    'Free insurance on all shipments',
                    'Priority support',
                    'Advanced tracking',
                    'Discounted shipping rates',
                    'Auto-renewal',
                    '2 months free (save 200 SAR)',
                ]),
                'includes_insurance' => true,
                'insurance_coverage' => 5000.00,
                'max_shipments_per_month' => null,
                'status' => 1,
                'sort_order' => 3,
                'description' => 'Save 200 SAR with yearly billing. Includes all premium features plus free insurance up to 5,000 SAR.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($plans as $plan) {
            // Check if plan already exists
            $exists = DB::table('subscription_plans')
                ->where('slug', $plan['slug'])
                ->exists();

            if (!$exists) {
                DB::table('subscription_plans')->insert($plan);
            }
        }
    }
}
