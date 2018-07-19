<?php

use Illuminate\Database\Seeder;

class FacadePlasteredSurfacesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
	    $items = [
		    [
			    'names' => [
				    'nl' => 'Ja, tot 10 m2',
			    ],
			    'calculate_value' => 10,
			    'order' => 0,
		    ],
		    [
			    'names' => [
				    'nl' => 'Ja, 10 m2 tot 25 m2',
			    ],
			    'calculate_value' => 25,
			    'order' => 1,
		    ],
		    [
			    'names' => [
				    'nl' => 'Ja, 25 m2 tot 50 m2',
			    ],
			    'calculate_value' => 50,
			    'order' => 2,
		    ],
		    [
			    'names' => [
				    'nl' => 'Ja, 50 m2 tot 80 m2',
			    ],
			    'calculate_value' => 80,
			    'order' => 3,
		    ],
		    [
			    'names' => [
				    'nl' => 'Ja, meer dan 80 m2',
			    ],
			    'calculate_value' => 120,
			    'order' => 4,
		    ],
	    ];

	    foreach($items as $item){
		    $uuid = \App\Helpers\Str::uuid();

		    foreach($item['names'] as $locale => $name){
			    \DB::table('translations')->insert([
				    'key'         => $uuid,
				    'language'    => $locale,
				    'translation' => $name,
			    ]);
		    }

		    \DB::table('facade_plastered_surfaces')->insert([
			    'name' => $uuid,
			    'calculate_value' => $item['calculate_value'],
			    'order' => $item['order'],
		    ]);
	    }
    }
}
