<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CourierConfigurationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $configurations = [
            [
                'name' => 'Aramex',
                'code' => 'aramex',
                'description' => 'Aramex international shipping',
                'is_active' => true,
                'api_endpoint' => 'https://ws.aramex.net/ShippingAPI.V2/RateCalculator/Service_1_0.svc/json',
                'api_key' => null, // To be configured
                'api_secret' => null,
                'account_number' => null,
                'additional_config' => json_encode([
                    'service_type' => 'express',
                    'product_group' => 'EXP',
                ]),
                'base_rate' => 15.00,
                'per_kg_rate' => 8.50,
                'insurance_percentage' => 2.00,
                'sort_order' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'DHL Express',
                'code' => 'dhl',
                'description' => 'DHL Express worldwide shipping',
                'is_active' => true,
                'api_endpoint' => 'https://api.dhl.com/mydhlapi/rates',
                'api_key' => null, // To be configured
                'api_secret' => null,
                'account_number' => null,
                'additional_config' => json_encode([
                    'service_type' => 'express',
                ]),
                'base_rate' => 20.00,
                'per_kg_rate' => 10.00,
                'insurance_percentage' => 1.50,
                'sort_order' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'FedEx International',
                'code' => 'fedex',
                'description' => 'FedEx international shipping services',
                'is_active' => true,
                'api_endpoint' => null, // To be configured
                'api_key' => null,
                'api_secret' => null,
                'account_number' => null,
                'additional_config' => json_encode([
                    'service_type' => 'priority',
                ]),
                'base_rate' => 18.00,
                'per_kg_rate' => 9.00,
                'insurance_percentage' => 1.75,
                'sort_order' => 3,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'UPS Worldwide',
                'code' => 'ups',
                'description' => 'UPS worldwide express shipping',
                'is_active' => true,
                'api_endpoint' => null, // To be configured
                'api_key' => null,
                'api_secret' => null,
                'account_number' => null,
                'additional_config' => json_encode([
                    'service_type' => 'express',
                ]),
                'base_rate' => 19.00,
                'per_kg_rate' => 9.50,
                'insurance_percentage' => 1.80,
                'sort_order' => 4,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Local Courier',
                'code' => 'local',
                'description' => 'Local/domestic courier service',
                'is_active' => true,
                'api_endpoint' => null,
                'api_key' => null,
                'api_secret' => null,
                'account_number' => null,
                'additional_config' => json_encode([
                    'service_type' => 'standard',
                ]),
                'base_rate' => 5.00,
                'per_kg_rate' => 3.00,
                'insurance_percentage' => 1.00,
                'sort_order' => 5,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];

        DB::table('courier_configurations')->insert($configurations);
    }
}
