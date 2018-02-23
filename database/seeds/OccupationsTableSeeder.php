<?php

use Illuminate\Database\Seeder;

class OccupationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
	    $occupations = [
		    'Directeur',
		    'Eigenaar',
		    'Verkoper',
		    'Administratief medewerker',
		    'Technisch medewerker',
		    'Financieel medewerker',
	    ];

	    foreach($occupations as $occupation) {
		    \App\Models\Occupation::create( [
			    'name' => $occupation,
		    ] );
	    }
    }
}
