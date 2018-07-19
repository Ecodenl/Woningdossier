<?php

use Illuminate\Database\Seeder;
use App\Models\BuildingType;

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
	            'names' => [
	            	'nl' => 'Vrijstaande woning',
	            ],
                'calculate_value' => 2
            ],
            [
                'names' => [
                	'nl' => '2 onder 1 kap',
                ],
                'calculate_value' => 3,
            ],
            [
                'names' => [
                	'nl' => 'Hoekwoning',
                ],
                'calculate_value' => 4,
            ],
            [
                'names' => [
                	'nl' => 'Tussenwoning',
                ],
                'calculate_value' => 5,
            ],
            [
                'names' => [
                	'nl' => 'Benedenwoning hoek',
                ],
                'calculate_value' => 6,
            ],
            [
                'names' => [
                	'nl' => 'Benedenwoning tussen',
                ],
                'calculate_value' => 7,
            ],
            [
                'names' => [
                	'nl' => 'Bovenwoning hoek',
                ],
                'calculate_value' => 8,
            ],
            [
                'names' => [
                	'nl' => 'Bovenwoning tussen',
                ],
                'calculate_value' => 9,
            ],
            [
                'names' => [
                	'nl' => 'Appartement tussen op een tussenverdieping',
                ],
                'calculate_value' => 10,
            ],
            [
                'names' => [
                	'nl' => 'Appartement hoek op een tussenverdieping',
                ],
                'calculate_value' => 11,
            ],
        ];

	    foreach($buildingTypes as $buildingType){
		    $uuid = \App\Helpers\Str::uuid();
		    foreach($buildingType['names'] as $locale => $name) {
			    \DB::table( 'translations' )->insert( [
				    'key'         => $uuid,
				    'language'    => $locale,
				    'translation' => $name,
			    ] );
		    }

		    \DB::table('building_types')->insert([
			    'calculate_value' => $buildingType['calculate_value'],
			    'name' => $uuid,
		    ]);
	    }
    }
}
