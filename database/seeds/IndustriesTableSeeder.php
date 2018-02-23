<?php

use Illuminate\Database\Seeder;

class IndustriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
	    $industries = [
		    'ICT',
		    'Pharmacie',
		    'Verpakking',
		    'Auto',
		    'Bouw',
		    'Cultuur',
		    'Dienstverlening',
		    'Elektro',
		    'Food',
		    'Groothandel',
		    'Handel',
		    'Industrie',
		    'Kantoor',
		    'Landbouw',
		    'Logistiek',
		    'Metaal',
		    'Non-profit',
		    'Sport',
		    'Techniek',
		    'Verhuur',
		    'Winkel',
	    ];

	    foreach($industries as $industry){
	    	\App\Models\Industry::create(['name' => $industry]);
	    }
    }
}
