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
		        'order' => 0,
	        ],
	        [
		        'names' => [
			        'nl' => 'Hoekwoning, drie bouwlagen en plat dak',
				],
		        'order' => 1,
	        ],
	        [
		        'names' => [
			        'nl' => 'Benedenwoning zonder opkamer (tussenwoning)',
				],
		        'order' => 2,
	        ],
	        [
		        'names' => [
			        'nl' => 'Benedenwoning zonder opkamer (hoekwoning)',
				],
		        'order' => 3,
	        ],
	        [
		        'names' => [
			        'nl' => 'Hoekhuis, twee bouwlagen en nieuwe dakopbouw',
				],
		        'order' => 6,
	        ],
	        [
		        'names' => [
			        'nl' => 'Tussenwoning, twee bouwlagen en nieuwe dakopbouw',
				],
		        'order' => 7,
	        ],
	        [
		        'names' => [
			        'nl' => 'Tussenwoning, twee bouwlagen en plat dak',
				],
		        'order' => 8,
	        ],
	        [
		        'names' => [
			        'nl' => 'Arbeidershuis, twee bouwlagen (tussenwoning)',
				],
		        'order' => 9,
	        ],
	        [
		        'names' => [
			        'nl' => 'Jaren \'30 tussenwoning met hellend dak',
				],
		        'order' => 10,
	        ],
	        [
		        'names' => [
			        'nl' => 'Jaren \'30 hoekwoning met hellend dak',
				],
		        'order' => 11,
	        ],
	        [
		        'names' => [
			        'nl' => 'Tussenwoning, drie bouwlagen en hellend dak',
				],
		        'order' => 12,
	        ],
	        [
		        'names' => [
			        'nl' => 'Hoekwoning, drie bouwlagen en hellend dak',
				],
		        'order' => 13,
	        ],
	        [
		        'names' => [
			        'nl' => 'Bovenwoning zonder opkamer (tussenwoning)',
				],
		        'order' => 4,
	        ],
	        [
		        'names' => [
			        'nl' => 'Bovenwoning zonder opkamer (hoekwoning)',
				],
		        'order' => 5,
	        ],
        ];

        foreach($exampleBuildings as $exampleBuilding){
	        $uuid = \App\Helpers\Str::uuid();
	        foreach($exampleBuilding['names'] as $locale => $name) {
		        \DB::table( 'translations' )->insert( [
			        'key'         => $uuid,
			        'language'    => $locale,
			        'translation' => $name,
		        ] );
	        }

	        \DB::table('example_buildings')->insert([
		        'name' => $uuid,
		        'order' => $exampleBuilding['order'],
	        ]);
        }
    }
}
