<?php

use Illuminate\Database\Seeder;

class RegistrationStatusesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
	    $statussen = [
		    'In behandeling',
		    'Afgehandeld',
	    ];

	    foreach ($statussen as $status) {
		    \DB::table('registration_statuses')->insert(
				    [ 'name' => $status ]
		    );
	    }
    }
}
