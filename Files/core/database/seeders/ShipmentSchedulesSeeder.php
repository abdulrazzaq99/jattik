<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ShipmentSchedulesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $schedules = [
            [
                'name' => 'First Week Shipping',
                'day_of_month' => 5,
                'description' => 'Shipments scheduled for the 5th of each month',
                'is_active' => true,
                'cutoff_days_before' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Mid-Month Shipping',
                'day_of_month' => 15,
                'description' => 'Shipments scheduled for the 15th of each month',
                'is_active' => true,
                'cutoff_days_before' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'End of Month Shipping',
                'day_of_month' => 25,
                'description' => 'Shipments scheduled for the 25th of each month',
                'is_active' => true,
                'cutoff_days_before' => 3,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];

        DB::table('shipment_schedules')->insert($schedules);
    }
}
