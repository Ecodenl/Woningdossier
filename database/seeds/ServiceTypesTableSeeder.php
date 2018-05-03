<?php

use Illuminate\Database\Seeder;

class ServiceTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
	    $types = [
	    	[
	    		'names' => [
	    			'en' => 'Heating',
			    ],
			    'iso' => 'M3',
		    ],
		    [
		    	'names' => [
		    		'en' => 'Cooling',
			    ],
			    'iso' => 'M4',
		    ],
		    [
		    	'names' => [
		    		'en' => 'Ventilation',
			    ],
			    'iso' => 'M5',
		    ],
		    [
		    	'names' => [
		    		'en' => 'Humidification',
				],
			    'iso' => 'M6',
		    ],
		    [
		    	'names' => [
		    		'en' => 'Dehumidification',
			    ],
			    'iso' => 'M7',
		    ],
		    [
		    	'names' => [
		    		'en' => 'Domestic hot water',
			    ],
			    'iso' => 'M8',
		    ],
		    [
		    	'names' => [
		    		'en' => 'Lighting',
			    ],
			    'iso' => 'M9',
		    ],
		    [
		    	'names' => [
		    		'en' => 'External lighting',
			    ],
			    'iso' => '',
		    ],
		    [
		    	'names' => [
		    		'en' => 'Building automation and control',
			    ],
			    'iso' => 'M10',
		    ],
		    [
		    	'names' => [
		    		'en' => 'People transport',
			    ],
			    'iso' => '',
		    ],
		    [
			    'names' => [
				    'en' => 'PV-wind',
			    ],
			    'iso' => 'M11',
		    ],
		    [
			    'names' => [
				    'en' => 'Appliances',
			    ],
			    'iso' => '',
		    ],
		    [
			    'names' => [
				    'en' => 'Others',
			    ],
			    'iso' => '',
		    ],

	    ];


	    foreach ($types as $type) {
		    $uuid = \App\Helpers\Str::uuid();
		    foreach($type['names'] as $locale => $name) {
			    \DB::table( 'translations' )->insert( [
				    'key'         => $uuid,
				    'language'    => $locale,
				    'translation' => $name,
			    ] );
		    }

		    \DB::table('service_types')->insert([
			    'iso' => $type['iso'],
			    'name' => $uuid,
		    ]);
	    }
    }
}
