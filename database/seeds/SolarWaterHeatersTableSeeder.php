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
                'calculated_value' => 1,
            ],
            [
                'name' => 'Voor warm tapwater',
                'calculated_value' => 2,
            ],
            [
                'name' => 'Voor verwarming',
                'calculated_value' => 3,
            ],
            [
                'name' => 'Voor verwarming en warm tapwater',
                'calculated_value' => 4,
            ],
        ];

        foreach ($solarWaterHeaters as $solarWaterHeater ){
            SolarWaterHeater::create([
                'name' => $solarWaterHeater['name'],
                'calculated_value' => $solarWaterHeater['calculated_value']
            ]);
        }
    }
}
