<?php

use Illuminate\Database\Seeder;

class PersonTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
	    $types = [
		    'Particulier',
		    'Adviseur',
		    'Eigenaar bedrijf',
		    'ZZP\'er',
		    'Journalist',
		    'Wethouder',
		    'Kunstenaar',
		    'Collega cooperatie',
	    ];

	    foreach($types as $type){
	    	\App\Models\PersonType::create(['name' => $type]);
	    }
    }
}
