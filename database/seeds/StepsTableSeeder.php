<?php

use Illuminate\Database\Seeder;

class StepsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
	    $steps = [
	    	[
	    		'slug' => 'common-data',
			    'name' => 'Common data',
			    'order' => 0,
		    ],
		    [
			    'slug' => 'gevelisolatie',
			    'name' => 'Gevelisolatie',
			    'order' => 1,
		    ],
		    [
			    'slug' => 'isolerende-beglazing',
			    'name' => 'Isolerende beglazing',
			    'order' => 2,
		    ],
		    [
			    'slug' => 'vloerisolatie',
			    'name' => 'Vloerisolatie',
			    'order' => 3,
		    ],
		    [
			    'slug' => 'dakisolatie',
			    'name' => 'Dakisolatie',
			    'order' => 4,
		    ],
	    ];

	    DB::table('steps')->insert($steps);
    }
}
