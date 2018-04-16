<?php

use Illuminate\Database\Seeder;

class KeyFigureBoilerEfficienciesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
	    $service = \DB::table('services')->where('short', 'boiler')->first();

	    $items = [
	    	[
	    		'service_value_calculate_value' => 1, // conventioneel rendement ketel
			    'heating' => 70,
			    'wtw' => 70,
		    ],
		    [
			    'service_value_calculate_value' => 2, // verbeterd rendement ketel
			    'heating' => 86,
			    'wtw' => 75,
		    ],
		    [
			    'service_value_calculate_value' => 3, // HR100 ketel
			    'heating' => 91,
			    'wtw' => 80,
		    ],
		    [
			    'service_value_calculate_value' => 4, // HR104 ketel
			    'heating' => 94,
			    'wtw' => 85,
		    ],
		    [
			    'service_value_calculate_value' => 5, // HR107 ketel
			    'heating' => 97,
			    'wtw' => 89,
		    ],
	    ];

	    foreach($items as $item){
	    	$serviceValue = \DB::table('service_values')
		                       ->where('service_id', $service->id)
			                ->where('calculate_value', $item['service_value_calculate_value'])
			                ->first();

	    	\DB::table('key_figure_boiler_efficiencies')->insert([
	    		'service_value_id' => $serviceValue->id,
			    'heating' => $item['heating'],
			    'wtw' => $item['wtw'],
		    ]);
	    }

    }
}
