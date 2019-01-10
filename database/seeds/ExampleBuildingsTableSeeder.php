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
            /*[
                'names' => [
                    'nl' => 'Er is geen passende voorbeeldwoning',
                ],
                'order' => 99,
            ],*/
            [
                'names' => [
                    'nl' => 'Tussenwoning, drie bouwlagen en plat dak',
                ],
                'order' => 100,
            ],
            [
                'names' => [
                    'nl' => 'Hoekwoning, drie bouwlagen en plat dak',
                ],
                'order' => 101,
            ],
            [
                'names' => [
                    'nl' => 'Benedenwoning zonder opkamer (tussenwoning)',
                ],
                'order' => 102,
            ],
            [
                'names' => [
                    'nl' => 'Benedenwoning zonder opkamer (hoekwoning)',
                ],
                'order' => 103,
            ],
            [
                'names' => [
                    'nl' => 'Hoekhuis, twee bouwlagen en nieuwe dakopbouw',
                ],
                'order' => 106,
            ],
            [
                'names' => [
                    'nl' => 'Tussenwoning, twee bouwlagen en nieuwe dakopbouw',
                ],
                'order' => 107,
            ],
            [
                'names' => [
                    'nl' => 'Tussenwoning, twee bouwlagen en plat dak',
                ],
                'order' => 108,
            ],
            [
                'names' => [
                    'nl' => 'Arbeidershuis, twee bouwlagen (tussenwoning)',
                ],
                'order' => 109,
            ],
            [
                'names' => [
                    'nl' => 'Jaren \'30 tussenwoning met hellend dak',
                ],
                'order' => 110,
            ],
            [
                'names' => [
                    'nl' => 'Jaren \'30 hoekwoning met hellend dak',
                ],
                'order' => 111,
            ],
            [
                'names' => [
                    'nl' => 'Tussenwoning, drie bouwlagen en hellend dak',
                ],
                'order' => 112,
            ],
            [
                'names' => [
                    'nl' => 'Hoekwoning, drie bouwlagen en hellend dak',
                ],
                'order' => 113,
            ],
            [
                'names' => [
                    'nl' => 'Bovenwoning zonder opkamer (tussenwoning)',
                ],
                'order' => 104,
            ],
            [
                'names' => [
                    'nl' => 'Bovenwoning zonder opkamer (hoekwoning)',
                ],
                'order' => 105,
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
            ]);
        }
    }
}
