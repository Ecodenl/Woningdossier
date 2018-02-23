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
		    'Vloerisolatie',
		    'Gevelisolatie',
		    'Dakisolatie',
		    'Isolatieglas',
		    'Kierdichting',
		    'Ventilatie',
		    'Cv-ketel',
		    'Warmtepomp',
		    'Biomassa',
		    'Warmte afgifte',
		    'Zonnepanelen',
		    'Zonneboiler',
		    'PVT',
		    'Opslag',
		    'Overig',
	    ];

	    foreach ($measureCategories as $measureCategory) {
		    \DB::table('measure_categories')->insert(
		    	[ 'name' => $measureCategory ]
		    );
	    }
    }
}
