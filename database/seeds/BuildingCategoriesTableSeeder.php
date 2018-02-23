<?php

use Illuminate\Database\Seeder;

class BuildingCategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
	    $categories = [
		    'Single-family houses of different types',
		    'Apartment blocks',
		    'Homes for elderly and disabled people',
		    'Residence for collective use',
		    'Mobile home',
		    'Holiday home',
		    'Offices',
		    'Educational buildings',
		    'Hospitals',
		    'Hotels and restaurants',
		    'Sports facilities',
		    'Wholesale and retail trade services buildings',
		    'ata centre',
		    'Industrial sites',
		    'Workshops',


	    ];

	    foreach ($categories as $category) {
		    \DB::table('building_categories')->insert([
				    ['name' => $category],
			    ]
		    );
	    }
    }
}
