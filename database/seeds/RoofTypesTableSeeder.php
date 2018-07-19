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
        			//'nl' => 'Hellend dak met dakpannen',
			        'nl' => 'Hellend dak',
		        ],
		        'order' => 0,
		        'calculate_value' => 1,
	        ],
//	        [
//		        'names' => [
//			        'nl' => 'Hellend dak met bitumen',
//		        ],
//		        'order' => 1,
//		        'calculate_value' => 2,
//	        ],
	        [
		        'names' => [
			        'nl' => 'Plat dak',
		        ],
		        'order' => 2,
		        'calculate_value' => 3,
	        ],
//	        [
//		        'names' => [
//			        'nl' => 'Plat dak met zink',
//		        ],
//		        'order' => 3,
//		        'calculate_value' => 4,
//	        ],
	        [
		        'names' => [
			        'nl' => 'Geen dak',
		        ],
		        'order' => 4,
		        'calculate_value' => 5,
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
