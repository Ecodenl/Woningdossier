<?php

use Illuminate\Database\Seeder;

class BuildingHeatingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $buildingHeatings = [
            [
                'names' => [
                    'nl' => 'Verwarmd',
                ],
                'degree' => 18,
                'calculate_value' => 2,
                'is_default' => false,
            ],
            [
                'names' => [
                    'nl' => 'Matig verwarmd',
                ],
                'degree' => 13,
                'calculate_value' => 3,
                'is_default' => false,
            ],
            [
                'names' => [
                    'nl' => 'Onverwarmd',
                ],
                'degree' => 10,
                'calculate_value' => 4,
                'is_default' => true,
            ],
            [
                'names' => [
                    'nl' => 'Niet van toepassing',
                ],
                'degree' => 18,
                'calculate_value' => 5,
                'is_default' => false,
            ],
        ];

        foreach ($buildingHeatings as $buildingHeating) {
            $uuid = \App\Helpers\Str::uuid();
            foreach ($buildingHeating['names'] as $locale => $name) {
                \DB::table('translations')->insert([
                    'key'         => $uuid,
                    'language'    => $locale,
                    'translation' => $name,
                ]);
            }

            \DB::table('building_heatings')->insert([
                'name' => $uuid,
                'degree' => $buildingHeating['degree'],
                'calculate_value' => $buildingHeating['calculate_value'],
                'is_default' => $buildingHeating['is_default'],
            ]);
        }
    }
}
