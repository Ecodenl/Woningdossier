<?php

use Illuminate\Database\Seeder;

class OrganisationTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
	    $types = [
		    'Leverancier',
		    'Afnemer',
		    'Cooperatie',
	    ];

	    foreach($types as $type){
	    	\App\Models\OrganisationType::create(['name' => $type]);
	    }
    }
}
