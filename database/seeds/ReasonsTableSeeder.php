<?php

use Illuminate\Database\Seeder;

class ReasonsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
	    $reasons = [
		    'Milieu',
		    'Comfort',
		    'Besparing',
	    ];

	    foreach($reasons as $reason){
			\App\Models\Reason::create(['name' => $reason]);
	    }
    }
}
