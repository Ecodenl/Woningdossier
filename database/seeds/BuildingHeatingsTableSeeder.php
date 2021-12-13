<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

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
                'name' => [
                    'nl' => 'Verwarmd',
                ],
                'degree' => 18,
                'calculate_value' => 2,
                'is_default' => false,
            ],
            [
                'name' => [
                    'nl' => 'Matig verwarmd',
                ],
                'degree' => 13,
                'calculate_value' => 3,
                'is_default' => false,
            ],
            [
                'name' => [
                    'nl' => 'Onverwarmd',
                ],
                'degree' => 10,
                'calculate_value' => 4,
                'is_default' => true,
            ],
            [
                'name' => [
                    'nl' => 'Niet van toepassing',
                ],
                'degree' => 18,
                'calculate_value' => 5,
                'is_default' => false,
            ],
        ];

        foreach ($buildingHeatings as $buildingHeating) {
            DB::table('building_heatings')->insert([
                'name' => json_encode($buildingHeating['name']),
                'degree' => $buildingHeating['degree'],
                'calculate_value' => $buildingHeating['calculate_value'],
                'is_default' => $buildingHeating['is_default'],
            ]);
        }
    }
}
