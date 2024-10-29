<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class SolarWaterHeatersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $solarWaterHeaters = [
            [
                'name' => 'Geen',
                'calculate_value' => 1,
            ],
            [
                'name' => 'Voor warm tapwater',
                'calculate_value' => 2,
            ],
            [
                'name' => 'Voor verwarming',
                'calculate_value' => 3,
            ],
            [
                'name' => 'Voor verwarming en warm tapwater',
                'calculate_value' => 4,
            ],
        ];

        foreach ($solarWaterHeaters as $solarWaterHeater) {
            DB::table('solar_water_heaters')->insert($solarWaterHeater);
        }
    }
}
