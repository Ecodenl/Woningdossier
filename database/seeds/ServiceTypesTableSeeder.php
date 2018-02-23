<?php

use Illuminate\Database\Seeder;

class ServiceTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
	    $types = [
		    'Heating',
		    'Cooling',
		    'Ventilation',
		    'Humidification',
		    'Dehumidification',
		    'Domestic hot water',
		    'Lighting',
		    'External lighting',
		    'Building automation and control',
		    'People transport',
		    'PV-wind',
		    'appliances',
		    'Others',

	    ];

	    foreach ($types as $type) {
		    \DB::table('service_types')->insert(
				    ['name' => $type]
		    );
	    }
    }
}
