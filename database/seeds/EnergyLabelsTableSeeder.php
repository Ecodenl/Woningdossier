<?php

use Illuminate\Database\Seeder;

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
		    'A+++',
		    'A++',
		    'A+',
		    'A',
		    'B',
		    'C',
		    'D',
		    'E',
		    'F',
	    ];

	    foreach($energyLabels as $energyLabel){
	    	\DB::table('energy_labels')->insert(
			    ['name' => $energyLabel]
		    );
	    }
    }
}
