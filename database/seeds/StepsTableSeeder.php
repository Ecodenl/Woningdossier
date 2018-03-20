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
	    		'slug' => 'general-data',
			    'name' => 'General data',
			    'order' => 0,
		    ],
		    [
			    'slug' => 'wall-insulation',
			    'name' => 'Wall Insulation',
			    'order' => 1,
		    ],
		    [
			    'slug' => 'insulated-glazing',
			    'name' => 'Insulated Glazing',
			    'order' => 2,
		    ],
		    [
			    'slug' => 'floor-insulation',
			    'name' => 'Floor Insulation',
			    'order' => 3,
		    ],
		    [
			    'slug' => 'roof-insulation',
			    'name' => 'Roof Insulation',
			    'order' => 4,
		    ],
		    [
			    'slug' => 'high-efficiency-boiler',
			    'name' => 'High Efficiency Boiler',
			    'order' => 5,
		    ],
		    [
			    'slug' => 'heat-pump',
			    'name' => 'Heat Pump',
			    'order' => 6,
		    ],
		    [
			    'slug' => 'solar-panels',
			    'name' => 'Solar Panels',
			    'order' => 7,
		    ],
		    [
			    'slug' => 'heater',
			    'name' => 'Heater',
			    'order' => 8,
		    ],
	    ];

	    DB::table('steps')->insert($steps);
    }
}
