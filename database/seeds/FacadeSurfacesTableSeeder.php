<?php

use Illuminate\Database\Seeder;

class FacadeSurfacesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $surfaces = [
        	[
        		'names' => [
        			'nl' => 'Nee',
		        ],
		        'calculate_value' => 0,
	        ],
	        [
	        	'names' => [
	        		'nl' => 'Ja, tot 10 m2',
		        ],
	            'calculate_value' => 10,
	        ],
	        [
	        	'names' => [
	        		'nl' => 'Ja, 10 m2 tot 25 m2',
		        ],
		        'calculate_value' => 25,
	        ],
	        [
		        'names' => [
			        'nl' => 'Ja, 25 m2 tot 50 m2',
		        ],
		        'calculate_value' => 50,
	        ],
	        [
		        'names' => [
			        'nl' => 'Ja, 50 m2 tot 80 m2',
		        ],
		        'calculate_value' => 80,
	        ],
	        [
		        'names' => [
			        'nl' => 'Ja, meer dan 80 m2',
		        ],
		        'calculate_value' => 120,
	        ],
        ];

	    foreach ($surfaces as $surface) {
		    $uuid = \App\Helpers\Str::uuid();
		    foreach($surface['names'] as $locale => $name) {
			    \DB::table( 'translations' )->insert( [
				    'key'         => $uuid,
				    'language'    => $locale,
				    'translation' => $name,
			    ] );
		    }

		    \DB::table('facade_surfaces')->insert([
			    'name' => $uuid,
			    'calculate_value' => $surface['calculate_value'],
		    ]);
	    }
    }
}
