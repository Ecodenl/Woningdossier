<?php

use Illuminate\Database\Seeder;
use App\Models\EnergyLabel;

class EnergyLabelsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
	    $energyLabels = [
	    	[
		        'name' => 'A',
			    'country_code' => 'nl',
		    ],
		    [
		    	'name' => 'B',
			    'country_code' => 'nl',
		    ],
		    [
		    	'name' => 'C',
			    'country_code' => 'nl',
		    ],
		    [
		    	'name' => 'D',
			    'country_code' => 'nl',
		    ],
		    [
		    	'name' => 'E',
			    'country_code' => 'nl',
		    ],
		    [
		    	'name' => 'F',
			    'country_code' => 'nl',
		    ],
            [
            	'name' => 'G',
	            'country_code' => 'nl',
            ],
	    ];

	    foreach($energyLabels as $energyLabel){
	    	\DB::table('energy_labels')->insert(
			    $energyLabel
		    );
	    }
    }
}
