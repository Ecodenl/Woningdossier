<?php

use Illuminate\Database\Seeder;

class AssessmentTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
	    $types = [
		    'Calculated design',
		    'Calculated built',
		    'Calculated actual',
		    'Calculated tailored',
		    'Measured actual',
		    'Measured corrected climate',
		    'Measured corrected for use',
		    'Measured standard',
	    ];

	    foreach ($types as $type) {
		    \DB::table('assessment_types')->insert(
		    	['name' => $type]
		    );
	    }
    }
}
