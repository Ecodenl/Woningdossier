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
            ],
            [
                'names' => [
                    'nl' => '2 onder 1 kap',
                ],
                'order' => 101,
            ],
            [
                'names' => [
                    'nl' => 'Hoekwoning',
                ],
                'order' => 102,
            ],
            [
                'names' => [
                    'nl' => 'Tussenwoning',
                ],
                'order' => 103,
            ],
	        [
		        'names' => [
			        'nl' => 'Bovenwoning hoek',
		        ],
		        'order' => 104,
	        ],
	        [
		        'names' => [
			        'nl' => 'Bovenwoning tussen',
		        ],
		        'order' => 105,
	        ],
            [
                'names' => [
                    'nl' => 'Bovenwoning hoek',
                ],
                'order' => 106,
            ],
            [
                'names' => [
                    'nl' => 'Bovenwoning tussen',
                ],
                'order' => 107,
            ],
            [
                'names' => [
                    'nl' => 'Appartement tussen op een tussenverdieping',
                ],
                'order' => 108,
            ],
            [
                'names' => [
                    'nl' => 'Appartement hoek op een tussenverdieping',
                ],
                'order' => 109,
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
