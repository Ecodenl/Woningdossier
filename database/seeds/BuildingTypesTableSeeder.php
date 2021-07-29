<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BuildingTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $buildingTypes = [
            [
                'name' => [
                    'nl' => 'Vrijstaande woning',
                ],
                'calculate_value' => 2,
            ],
            [
                'name' => [
                    'nl' => '2 onder 1 kap',
                ],
                'calculate_value' => 3,
            ],
            [
                'name' => [
                    'nl' => 'Hoekwoning',
                ],
                'calculate_value' => 4,
            ],
            [
                'name' => [
                    'nl' => 'Tussenwoning',
                ],
                'calculate_value' => 5,
            ],
            [
                'name' => [
                    'nl' => 'Benedenwoning hoek',
                ],
                'calculate_value' => 6,
            ],
            [
                'name' => [
                    'nl' => 'Benedenwoning tussen',
                ],
                'calculate_value' => 7,
            ],
            [
                'name' => [
                    'nl' => 'Bovenwoning hoek',
                ],
                'calculate_value' => 8,
            ],
            [
                'name' => [
                    'nl' => 'Bovenwoning tussen',
                ],
                'calculate_value' => 9,
            ],
            [
                'name' => [
                    'nl' => 'Appartement tussen op een tussenverdieping',
                ],
                'calculate_value' => 10,
            ],
            [
                'name' => [
                    'nl' => 'Appartement hoek op een tussenverdieping',
                ],
                'calculate_value' => 11,
            ],
        ];

        foreach ($buildingTypes as $buildingType) {
            DB::table('building_types')->insert([
                'calculate_value' => $buildingType['calculate_value'],
                'name' => json_encode($buildingType['name']),
            ]);
        }
    }
}
