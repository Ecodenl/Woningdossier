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
		        'order' => 0,
		        'execution_term_name' => [
		        	'nl' => 'Niet nodig',
		        ],
		        'term_years' => null,
	        ],
	        [
	        	'names' => [
	        		'nl' => 'Ja, tot 10 m2',
		        ],
	            'calculate_value' => 10,
		        'order' => 1,
		        'execution_term_name' => [
			        'nl' => 'Binnen 5 jaar',
		        ],
		        'term_years' => 5,
	        ],
	        [
	        	'names' => [
	        		'nl' => 'Ja, 10 m2 tot 25 m2',
		        ],
		        'calculate_value' => 25,
		        'order' => 2,
		        'execution_term_name' => [
			        'nl' => 'Binnen 5 jaar',
		        ],
		        'term_years' => 5,
	        ],
	        [
		        'names' => [
			        'nl' => 'Ja, 25 m2 tot 50 m2',
		        ],
		        'calculate_value' => 50,
		        'order' => 3,
		        'execution_term_name' => [
			        'nl' => 'Binnen 1 jaar',
		        ],
		        'term_years' => 0,
	        ],
	        [
		        'names' => [
			        'nl' => 'Ja, 50 m2 tot 80 m2',
		        ],
		        'calculate_value' => 80,
		        'order' => 4,
		        'execution_term_name' => [
			        'nl' => 'Binnen 1 jaar',
		        ],
		        'term_years' => 0,
	        ],
	        [
		        'names' => [
			        'nl' => 'Ja, meer dan 80 m2',
		        ],
		        'calculate_value' => 120,
		        'order' => 5,
		        'execution_term_name' => [
			        'nl' => 'Binnen 1 jaar',
		        ],
		        'term_years' => 0,
	        ],
        ];

        $termTranslations = [];
	    foreach ($surfaces as $surface) {
		    $uuid = \App\Helpers\Str::uuid();
		    foreach($surface['names'] as $locale => $name) {
			    \DB::table( 'translations' )->insert( [
				    'key'         => $uuid,
				    'language'    => $locale,
				    'translation' => $name,
			    ] );
		    }
		    foreach($surface['execution_term_name'] as $locale => $termName){
		    	if (!array_key_exists($termName, $termTranslations)){
		    		$termUuid = \App\Helpers\Str::uuid();
		    		\DB::table('translations')->insert([
		    			'key' => $termUuid,
					    'language' => $locale,
					    'translation' => $termName,
				    ]);
		    		$termTranslations[$termName] = $termUuid;
			    }
			    else {
		    		$termUuid = $termTranslations[$termName];
			    }
		    }

		    \DB::table('facade_surfaces')->insert([
			    'name' => $uuid,
			    'calculate_value' => $surface['calculate_value'],
			    'order' => $surface['order'],
			    'execution_term_name' => $termUuid,
			    'term_years' => $surface['term_years'],
		    ]);
	    }
    }
}
