<?php

use Illuminate\Database\Seeder;

class TaskTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
	    $types = [
		    'Bellen',
		    'Email',
		    'Offerte maken',
		    'Contact moment',
	    ];

	    foreach ($types as $type) {
		    \DB::table('task_types')->insert(
				    ['name' => $type]
		    );
	    }
    }
}
