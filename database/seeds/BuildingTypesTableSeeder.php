<?php

use Illuminate\Database\Seeder;
use App\Models\BuildingType;

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
	        [
	            'name' => 'Vrijstaande woning',
                'calculate_value' => 2
            ],
            [
                'name' => '2 onder 1 kap',
                'calculate_value' => 3,
            ],
            [
                'name' => 'Hoekwoning',
                'calculate_value' => 4,
            ],
            [
                'name' => 'Tussenwoning',
                'calculate_value' => 5,
            ],
            [
                'name' => 'Benedenwoning hoek',
                'calculate_value' => 6,
            ],
            [
                'name' => 'Benedenwoning tussen',
                'calculate_value' => 7,
            ],
            [
                'name' => 'Bovenwoning hoek',
                'calculate_value' => 8,
            ],
            [
                'name' => 'Bovenwoning tussen',
                'calculate_value' => 9,
            ],
            [
                'name' => 'Appartement tussen op een tussenverdieping',
                'calculate_value' => 10,
            ],
            [
                'name' => 'Appartement hoek op een tussenverdieping',
                'calculate_value' => 11,
            ],
        ];

	    foreach($buildingTypes as $buildingType){
	    	DB::table('building_types')->insert($buildingType);
	    }
    }
}
