<?php

use Illuminate\Database\Seeder;

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
                'names' => [
                    'nl' => 'Vrijstaande woning',
                ],
                'order' => 100,
                'building_type_id' => 1,
            ],
            [
                'names' => [
                    'nl' => '2 onder 1 kap',
                ],
                'order' => 101,
                'building_type_id' => 2,
            ],
            [
                'names' => [
                    'nl' => 'Hoekwoning',
                ],
                'order' => 102,
                'building_type_id' => 3,
            ],
            [
                'names' => [
                    'nl' => 'Tussenwoning',
                ],
                'order' => 103,
                'building_type_id' => 4,
            ],
            [
                'names' => [
                    'nl' => 'Bovenwoning hoek',
                ],
                'order' => 104,
                'building_type_id' => 5,
            ],
            [
                'names' => [
                    'nl' => 'Bovenwoning tussen',
                ],
                'order' => 105,
                'building_type_id' => 6,
            ],
            [
                'names' => [
                    'nl' => 'Bovenwoning hoek',
                ],
                'order' => 106,
                'building_type_id' => 7,
            ],
            [
                'names' => [
                    'nl' => 'Bovenwoning tussen',
                ],
                'order' => 107,
                'building_type_id' => 8,
            ],
            [
                'names' => [
                    'nl' => 'Appartement tussen op een tussenverdieping',
                ],
                'order' => 108,
                'building_type_id' => 9,
            ],
            [
                'names' => [
                    'nl' => 'Appartement hoek op een tussenverdieping',
                ],
                'order' => 109,
                'building_type_id' => 10,
            ],
        ];

        foreach ($exampleBuildings as $exampleBuilding) {
            $uuid = \App\Helpers\Str::uuid();
            foreach ($exampleBuilding['names'] as $locale => $name) {
                \DB::table('translations')->insert([
                    'key'         => $uuid,
                    'language'    => $locale,
                    'translation' => $name,
                ]);
            }

            \DB::table('example_buildings')->insert([
                'name' => $uuid,
                'order' => $exampleBuilding['order'],
                'building_type_id' => $exampleBuilding['building_type_id'],
            ]);
        }
    }
}
