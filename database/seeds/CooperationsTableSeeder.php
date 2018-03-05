<?php

use Illuminate\Database\Seeder;

class CooperationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	$cooperations = [
    		'Hoom', 'Cooperation A', 'Cooperation B',
	    ];

    	foreach($cooperations as $cooperation) {
		    DB::table( 'cooperations' )->insert([
		    	'name' => $cooperation,
			    'slug' => str_slug($cooperation),
		    ]);
	    }
    }
}
