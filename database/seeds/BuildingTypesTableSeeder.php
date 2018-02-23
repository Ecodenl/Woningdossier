<?php

use Illuminate\Database\Seeder;

class BuildingTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
	    $buildingTypes = [
		    'Vrijstaand',
		    'Hoekwoning',
		    'Tussenwoning',
		    'Appartement',
		    'Appartement VVE',
		    'Gehele tussenwoning',
		    'Beneden woning meerdere verdiepingen',
	    ];

	    foreach($buildingTypes as $buildingType){
	    	\DB::table('building_types')->insert([
	    		'name' => $buildingType,
		    ]);
	    }
    }
}
