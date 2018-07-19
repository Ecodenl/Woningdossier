<?php

use Illuminate\Database\Seeder;
use App\Models\SolarWaterHeater;

class SolarWaterHeatersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
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

        foreach ($solarWaterHeaters as $solarWaterHeater ){
        	DB::table('solar_water_heaters')->insert($solarWaterHeater);
        }
    }
}
