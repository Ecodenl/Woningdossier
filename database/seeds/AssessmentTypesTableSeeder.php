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
	    $categories = [
		    [
			    'type' => 'EPB_ASSESS_CALC_DESIGN',
			    'names' => [
				    'en' => 'Calculated design',
			    ],
		    ],
		    [
			    'type' => 'EPB_ASSESS_CALC_ASBUILT',
			    'names' => [
				    'en' => 'Calculated built',
			    ],
		    ],
		    [
			    'type' => 'EPB_ASSESS_CALC_ACTUAL',
			    'names' => [
				    'en' => 'Calculated actual',
			    ],
		    ],
		    [
			    'type' => 'EPB_ASSESS_CALC_TAILORED',
			    'names' => [
				    'en' => 'Calculated tailored',
			    ],
		    ],
		    [
			    'type' => 'EPB_ASSESS_MEAS_ACTUAL',
			    'names' => [
				    'en' => 'Measured actual',
			    ],
		    ],
		    [
			    'type' => 'EPB_ASSESS_MEAS_CORR_CLIM',
			    'names' => [
				    'en' => 'Measured corrected climate',
			    ],
		    ],
		    [
			    'type' => 'EPB_ASSESS_MEAS_CORR_USE',
			    'names' => [
				    'en' => 'Measured corrected for use',
			    ],
		    ],
		    [
			    'type' => 'EPB_ASSESS_MEAS_STAND',
			    'names' => [
				    'en' => 'Measured standard',
			    ],
		    ],
	    ];

	    foreach ($categories as $category) {
		    $uuid = \App\Helpers\Str::uuid();
		    foreach($category['names'] as $locale => $name) {
			    \DB::table( 'translations' )->insert( [
				    'key'         => $uuid,
				    'language'    => $locale,
				    'translation' => $name,
			    ] );
		    }

		    \DB::table('assessment_types')->insert([
			    'type' => $category['type'],
			    'name' => $uuid,
		    ]);
	    }
    }
}
