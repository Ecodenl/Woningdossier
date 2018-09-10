<?php

use Illuminate\Database\Seeder;

class KeyFigureConsumptionTapWatersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $items = [
            // standaard
            1 => [
                // one resident
                1 => [
                    'water_consumption' => 40,
                    'energy_consumption' => 130,
                ],
                2 => [
                    'water_consumption' => 58,
                    'energy_consumption' => 189,
                ],
                3 => [
                    'water_consumption' => 74,
                    'energy_consumption' => 241,
                ],
                4 => [
                    'water_consumption' => 93,
                    'energy_consumption' => 302,
                ],
                5 => [
                    'water_consumption' => 110,
                    'energy_consumption' => 358,
                ],
                6 => [
                    'water_consumption' => 127,
                    'energy_consumption' => 413,
                ],
                7 => [
                    'water_consumption' => 140,
                    'energy_consumption' => 455,
                ],
                8 => [
                    'water_consumption' => 153,
                    'energy_consumption' => 497,
                ],
            ],
            // comfort
            2 => [
                // one resident
                1 => [
                    'water_consumption' => 48,
                    'energy_consumption' => 156,
                ],
                2 => [
                    'water_consumption' => 71,
                    'energy_consumption' => 231,
                ],
                3 => [
                    'water_consumption' => 92,
                    'energy_consumption' => 299,
                ],
                4 => [
                    'water_consumption' => 112,
                    'energy_consumption' => 364,
                ],
                5 => [
                    'water_consumption' => 138,
                    'energy_consumption' => 449,
                ],
                6 => [
                    'water_consumption' => 158,
                    'energy_consumption' => 514,
                ],
                7 => [
                    'water_consumption' => 178,
                    'energy_consumption' => 579,
                ],
                8 => [
                    'water_consumption' => 196,
                    'energy_consumption' => 637,
                ],
            ],
            // comfort plus
            3 => [
                // one resident
                1 => [
                    'water_consumption' => 55,
                    'energy_consumption' => 179,
                ],
                2 => [
                    'water_consumption' => 83,
                    'energy_consumption' => 270,
                ],
                3 => [
                    'water_consumption' => 110,
                    'energy_consumption' => 358,
                ],
                4 => [
                    'water_consumption' => 140,
                    'energy_consumption' => 455,
                ],
                5 => [
                    'water_consumption' => 165,
                    'energy_consumption' => 536,
                ],
                6 => [
                    'water_consumption' => 190,
                    'energy_consumption' => 618,
                ],
                7 => [
                    'water_consumption' => 196,
                    'energy_consumption' => 637,
                ],
                8 => [
                    'water_consumption' => 240,
                    'energy_consumption' => 780,
                ],
            ],
        ];

        foreach ($items as $cltwCalcVal => $item) {
            $comfortLevelTapWater = \App\Models\ComfortLevelTapWater::where('calculate_value', $cltwCalcVal)->first();
            if ($comfortLevelTapWater instanceof \App\Models\ComfortLevelTapWater) {
                foreach ($item as $residentCount => $consumptions) {
                    \DB::table('key_figure_consumption_tap_waters')->insert([
                        'comfort_level_tap_water_id' => $comfortLevelTapWater->id,
                        'resident_count' => $residentCount,
                        'water_consumption' => $consumptions['water_consumption'],
                        'energy_consumption' => $consumptions['energy_consumption'],
                    ]);
                }
            }
        }
    }
}
