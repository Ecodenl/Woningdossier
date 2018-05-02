<?php

use Illuminate\Database\Seeder;
use App\Models\BuildingHeating;

class BuildingHeatingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $buildingHeatings = [
            [
                'names' => [
                	'nl' => 'Verwarmd, de meeste radiatoren staan aan',
	            ],
                'degree' => 18,
                'calculate_value' => 2,
            ],
            [
                'names' => [
                	'nl' => 'Matig verwarmd, de meeste radiatoren staan hoger dan * of een aantal radiatoren staan hoog',
                ],
                'degree' => 13,
                'calculate_value' => 3,
            ],
            [
                'names' => [
                	'nl' => 'Onverwarmd, de radiatoren staan op * of uit',
                ],
                'degree' => 10,
                'calculate_value' => 4,
            ],
            [
                'names' => [
                	'nl' => 'Niet van toepassing',
                ],
                'degree' => 18,
                'calculate_value' => 5,
            ],
        ];

	    foreach ($buildingHeatings as $buildingHeating) {
		    $uuid = \App\Helpers\Str::uuid();
		    foreach($buildingHeating['names'] as $locale => $name) {
			    \DB::table( 'translations' )->insert( [
				    'key'         => $uuid,
				    'language'    => $locale,
				    'translation' => $name,
			    ] );
		    }

		    \DB::table('building_heatings')->insert([
			    'name' => $uuid,
			    'degree' => $buildingHeating['degree'],
			    'calculate_value' => $buildingHeating['calculate_value'],
		    ]);
	    }
    }
}
