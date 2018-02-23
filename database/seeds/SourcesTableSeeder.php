<?php

use Illuminate\Database\Seeder;

class SourcesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
	    $sources = [
		    'E-mail',
		    'Website',
		    'Evenement',
		    'Telefoon',
		    'Enquete',
	    ];

	    foreach($sources as $source){
	    	\App\Models\Source::create(['name' => $source]);
	    }
    }
}
