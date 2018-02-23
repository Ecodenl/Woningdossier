<?php

use Illuminate\Database\Seeder;

class TaskPropertiesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
	    $properties = [
		    'Aantal',
		    'Advies',
		    'Conceptovereenkomst',
		    'Lid',
		    'Bedrijf',
		    'Naam bedrijf',
		    'Energiemaatschappij',
		    'Akkoord',
		    'Later bellen',
	    ];

	    foreach ($properties as $property) {
		    \DB::table('task_properties')->insert(
				    ['name' => $property]
		    );
	    }
    }
}
