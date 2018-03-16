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
		    'A',
		    'B',
		    'C',
		    'D',
		    'E',
		    'F',
	    ];

	    foreach($energyLabels as $energyLabel){
//	    	\DB::table('energy_labels')->insert(
//			    ['name' => $energyLabel]
//		    );

	    	EnergyLabel::create([
	    	    'name' => $energyLabel
            ]);
	    }
    }
}
