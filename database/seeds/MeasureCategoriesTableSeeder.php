<?php

use Illuminate\Database\Seeder;

class MeasureCategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
	    $measureCategories = [
	    	[
	    		'names' => [
	    			'nl' => 'Vloerisolatie',
			    ],
		    ],
		    [
			    'names' => [
				    'nl' => 'Gevelisolatie',
			    ],
		    ],
		    [
			    'names' => [
				    'nl' => 'Dakisolatie',
			    ],
		    ],
		    [
			    'names' => [
				    'nl' => 'Isolatieglas',
			    ],
		    ],
		    [
			    'names' => [
				    'nl' => 'Kierdichting',
			    ],
		    ],
		    [
			    'names' => [
				    'nl' => 'Ventilatie',
			    ],
		    ],
		    [
			    'names' => [
				    'nl' => 'Cv-ketel',
			    ],
		    ],
		    [
			    'names' => [
				    'nl' => 'Warmtepomp',
			    ],
		    ],
		    [
			    'names' => [
				    'nl' => 'Biomassa',
			    ],
		    ],
		    [
			    'names' => [
				    'nl' => 'Warmte afgifte',
			    ],
		    ],
		    [
			    'names' => [
				    'nl' => 'Zonnepanelen',
			    ],
		    ],
		    [
			    'names' => [
				    'nl' => 'Zonneboiler',
			    ],
		    ],
		    [
			    'names' => [
				    'nl' => 'PVT',
			    ],
		    ],
		    [
			    'names' => [
				    'nl' => 'Opslag',
			    ],
		    ],
		    [
			    'names' => [
				    'nl' => 'Overig',
			    ],
		    ],
	    ];

	    foreach ($measureCategories as $measureCategory) {
		    $uuid = \App\Helpers\Str::uuid();
		    foreach($measureCategory['names'] as $locale => $name) {
			    \DB::table( 'translations' )->insert( [
				    'key'         => $uuid,
				    'language'    => $locale,
				    'translation' => $name,
			    ] );
		    }

		    \DB::table('measure_categories')->insert([
			    'name' => $uuid,
		    ]);
	    }
    }
}
