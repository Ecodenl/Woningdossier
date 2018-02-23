<?php

use Illuminate\Database\Seeder;

class TitlesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
	    $prefixes = [
		    'Dhr',
		    'Mevr',
	    ];

	    foreach($prefixes as $prefix){
	    	\DB::table('titles')->insert(['name' => $prefix]);
	    }
    }
}
