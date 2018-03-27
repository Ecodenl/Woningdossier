<?php

use Illuminate\Database\Seeder;

class RoofTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roofTypes = [
        	[
        		'names' => [
        			'nl' => 'Hellend dak met dakpannen',
		        ],
		        'order' => 0,
		        'calculate_value' => 1,
	        ],
	        [
		        'names' => [
			        'nl' => 'Hellend dak met bitumen',
		        ],
		        'order' => 1,
		        'calculate_value' => 1,
	        ],
	        [
		        'names' => [
			        'nl' => 'Platdak',
		        ],
		        'order' => 2,
		        'calculate_value' => 2,
	        ],
	        [
		        'names' => [
			        'nl' => 'Geen dak',
		        ],
		        'order' => 3,
		        'calculate_value' => 2,
	        ],
        ];

	    foreach ($roofTypes as $roofType) {
		    $uuid = \App\Helpers\Str::uuid();
		    foreach($roofType['names'] as $locale => $name) {
			    \DB::table( 'translations' )->insert( [
				    'key'         => $uuid,
				    'language'    => $locale,
				    'translation' => $name,
			    ] );
		    }

		    \DB::table('roof_types')->insert([
			    'calculate_value' => $roofType['calculate_value'],
			    'order' => $roofType['order'],
			    'name' => $uuid,
		    ]);
	    }
    }
}
