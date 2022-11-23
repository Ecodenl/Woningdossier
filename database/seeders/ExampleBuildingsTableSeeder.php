<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ExampleBuildingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $exampleBuildings = [
            [
                'name' => [
                    'nl' => 'Vrijstaande woning',
                ],
                'order' => 100,
                'building_type_id' => 1,
            ],
            [
                'name' => [
                    'nl' => '2 onder 1 kap',
                ],
                'order' => 101,
                'building_type_id' => 2,
            ],
            [
                'name' => [
                    'nl' => 'Hoekwoning',
                ],
                'order' => 102,
                'building_type_id' => 3,
            ],
            [
                'name' => [
                    'nl' => 'Tussenwoning',
                ],
                'order' => 103,
                'building_type_id' => 4,
            ],
            [
                'name' => [
                    'nl' => 'Bovenwoning hoek',
                ],
                'order' => 104,
                'building_type_id' => 5,
            ],
            [
                'name' => [
                    'nl' => 'Bovenwoning tussen',
                ],
                'order' => 105,
                'building_type_id' => 6,
            ],
            [
                'name' => [
                    'nl' => 'Bovenwoning hoek',
                ],
                'order' => 106,
                'building_type_id' => 7,
            ],
            [
                'name' => [
                    'nl' => 'Bovenwoning tussen',
                ],
                'order' => 107,
                'building_type_id' => 8,
            ],
            [
                'name' => [
                    'nl' => 'Appartement tussen op een tussenverdieping',
                ],
                'order' => 108,
                'building_type_id' => 9,
            ],
            [
                'name' => [
                    'nl' => 'Appartement hoek op een tussenverdieping',
                ],
                'order' => 109,
                'building_type_id' => 10,
            ],
        ];

        foreach ($exampleBuildings as $exampleBuilding) {
            DB::table('example_buildings')->insert([
                'name' => json_encode($exampleBuilding['name']),
                'order' => $exampleBuilding['order'],
                'building_type_id' => $exampleBuilding['building_type_id'],
            ]);
        }
    }
}
