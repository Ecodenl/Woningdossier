<?php

use Illuminate\Database\Seeder;

class MeasureApplicationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	/*
    	$table->enum('measure_type', ['energy_saving', 'maintenance']);
	    $table->uuid('measure_name');
	    $table->enum('application', ['place', 'replace', 'remove']);
	    $table->double('costs', 8, 2);
	    $table->uuid('cost_unit');
	    $table->double('minimal_costs', 8, 2);
	    $table->integer('maintenance_interval');
	    $table->uuid('maintenance_unit');
    	*/


        $measureApplications = [
        	// Energiebesparende maatregelen
            [
            	'measure_type' => 'energy_saving',
                'measure_names' => [
                	'nl' => 'Vloerisolatie',
                ],
	            'application' => 'place',
	            'costs' => 35, // euro
	            'cost_unit' => [
	            	'nl' => 'per m2',
	            ],
	            'minimal_costs' => 550, // euro
	            'maintenance_interval' => 100,
	            'maintenance_unit' => [
	            	'nl' => 'per jaar',
	            ],
            ],
	        [
		        'measure_type' => 'energy_saving',
		        'measure_names' => [
			        'nl' => 'Bodemisolatie',
		        ],
		        'application' => 'place',
		        'costs' => 25, // euro
		        'cost_unit' => [
			        'nl' => 'per m2',
		        ],
		        'minimal_costs' => 400, // euro
		        'maintenance_interval' => 40,
		        'maintenance_unit' => [
			        'nl' => 'per jaar',
		        ],
	        ],
	        [
		        'measure_type' => 'energy_saving',
		        'measure_names' => [
			        'nl' => 'Er is nader onderzoek nodig of de vloer geïsoleerd kan worden',
		        ],
		        'application' => 'place',
		        'costs' => 25, // euro
		        'cost_unit' => [
			        'nl' => 'per m2',
		        ],
		        'minimal_costs' => 400, // euro
		        'maintenance_interval' => 40,
		        'maintenance_unit' => [
			        'nl' => 'per jaar',
		        ],
	        ],
	        [
		        'measure_type' => 'energy_saving',
		        'measure_names' => [
			        'nl' => 'Spouwmuurisolatie',
		        ],
		        'application' => 'place',
		        'costs' => 19, // euro
		        'cost_unit' => [
			        'nl' => 'per m2',
		        ],
		        'minimal_costs' => 650, // euro
		        'maintenance_interval' => 100,
		        'maintenance_unit' => [
			        'nl' => 'per jaar',
		        ],
	        ],
	        [
		        'measure_type' => 'energy_saving',
		        'measure_names' => [
			        'nl' => 'Binnengevelisolatie',
		        ],
		        'application' => 'place',
		        'costs' => 90, // euro
		        'cost_unit' => [
			        'nl' => 'per m2',
		        ],
		        'minimal_costs' => 450, // euro
		        'maintenance_interval' => 100,
		        'maintenance_unit' => [
			        'nl' => 'per jaar',
		        ],
	        ],
	        [
		        'measure_type' => 'energy_saving',
		        'measure_names' => [
			        'nl' => 'Er is nader onderzoek nodig hoe de gevel het beste geïsoleerd kan worden',
		        ],
		        'application' => 'place',
		        'costs' => 19, // euro
		        'cost_unit' => [
			        'nl' => 'per m2',
		        ],
		        'minimal_costs' => 650, // euro
		        'maintenance_interval' => 100,
		        'maintenance_unit' => [
			        'nl' => 'jaar',
		        ],
	        ],
	        [ // stap: isolerende beglazing
		        'measure_type' => 'energy_saving',
		        'measure_names' => [
			        'nl' => 'Glas in lood vervangen',
		        ],
		        'application' => 'replace',
		        'costs' => 150, // euro
		        'cost_unit' => [
			        'nl' => 'per m2',
		        ],
		        'minimal_costs' => 0, // euro
		        'maintenance_interval' => 40,
		        'maintenance_unit' => [
			        'nl' => 'jaar',
		        ],
	        ],
	        [ // stap: isolerende beglazing
		        'measure_type' => 'energy_saving',
		        'measure_names' => [
			        'nl' => 'Plaatsen van HR++ glas (alleen het glas)',
		        ],
		        'application' => 'place',
		        'costs' => 144, // euro
		        'cost_unit' => [
			        'nl' => 'per m2',
		        ],
		        'minimal_costs' => 0, // euro
		        'maintenance_interval' => 40,
		        'maintenance_unit' => [
			        'nl' => 'jaar',
		        ],
	        ],
	        [ // stap: isolerende beglazing
		        'measure_type' => 'energy_saving',
		        'measure_names' => [
			        'nl' => 'Plaatsen van HR++ glas (inclusief kozijn)',
		        ],
		        'application' => 'place',
		        'costs' => 550, // euro
		        'cost_unit' => [
			        'nl' => 'per m2',
		        ],
		        'minimal_costs' => 0, // euro
		        'maintenance_interval' => 40,
		        'maintenance_unit' => [
			        'nl' => 'jaar',
		        ],
	        ],
	        [ // stap: isolerende beglazing
		        'measure_type' => 'energy_saving',
		        'measure_names' => [
			        'nl' => 'Plaatsen van drievoudige HR beglazing (inclusief kozijn)',
		        ],
		        'application' => 'place',
		        'costs' => 700, // euro
		        'cost_unit' => [
			        'nl' => 'per m2',
		        ],
		        'minimal_costs' => 0, // euro
		        'maintenance_interval' => 40,
		        'maintenance_unit' => [
			        'nl' => 'jaar',
		        ],
	        ],
	        [
		        'measure_type' => 'energy_saving',
		        'measure_names' => [
			        'nl' => 'Kierdichting verbeteren',
		        ],
		        'application' => 'place',
		        'costs' => 400, // euro
		        'cost_unit' => [
			        'nl' => 'per stuk',
		        ],
		        'minimal_costs' => 0, // euro
		        'maintenance_interval' => 15,
		        'maintenance_unit' => [
			        'nl' => 'jaar',
		        ],
	        ],
	        [
		        'measure_type' => 'energy_saving',
		        'measure_names' => [
			        'nl' => 'Isolatie hellend dak van binnen uit',
		        ],
		        'application' => 'place',
		        'costs' => 90, // euro
		        'cost_unit' => [
			        'nl' => 'per m2',
		        ],
		        'minimal_costs' => 650, // euro
		        'maintenance_interval' => 100,
		        'maintenance_unit' => [
			        'nl' => 'jaar',
		        ],
	        ],
	        [
		        'measure_type' => 'energy_saving',
		        'measure_names' => [
			        'nl' => 'Isolatie hellend dak met vervanging van de dakpannen',
		        ],
		        'application' => 'replace',
		        'costs' => 65, // euro
		        'cost_unit' => [
			        'nl' => 'per m2',
		        ],
		        'minimal_costs' => 1200, // euro
		        'maintenance_interval' => 100,
		        'maintenance_unit' => [
			        'nl' => 'jaar',
		        ],
	        ],
	        [
		        'measure_type' => 'energy_saving',
		        'measure_names' => [
			        'nl' => 'Isolatie plat dak op huidige dakbedekking',
		        ],
		        'application' => 'place',
		        'costs' => 40, // euro
		        'cost_unit' => [
			        'nl' => 'per m2',
		        ],
		        'minimal_costs' => 350, // euro
		        'maintenance_interval' => 25,
		        'maintenance_unit' => [
			        'nl' => 'jaar',
		        ],
	        ],
	        [
		        'measure_type' => 'energy_saving',
		        'measure_names' => [
			        'nl' => 'Isolatie plat dak met vervanging van de dakbedekking',
		        ],
		        'application' => 'replace',
		        'costs' => 30, // euro
		        'cost_unit' => [
			        'nl' => 'per m2',
		        ],
		        'minimal_costs' => 350, // euro
		        'maintenance_interval' => 50,
		        'maintenance_unit' => [
			        'nl' => 'jaar',
		        ],
	        ],



	        // add more energiebesparende maatregelen here!



	        // Onderhoudsmaatregelen
	        [
	        	'measure_type' => 'maintenance',
		        'measure_names' => [
		        	'nl' => 'Reparatie voegwerk',
		        ],
		        'application' => 'repair',
		        'costs' => 55, // euro
		        'cost_unit' => [
		        	'nl' => 'per m2',
		        ],
		        'minimal_costs' => 350,
		        'maintenance_interval' => 100,
		        'maintenance_unit' => [
		        	'nl' => 'jaar',
		        ],
	        ],
	        [
		        'measure_type' => 'maintenance',
		        'measure_names' => [
			        'nl' => 'Reinigen metselwerk',
		        ],
		        'application' => 'repair',
		        'costs' => 15, // euro
		        'cost_unit' => [
			        'nl' => 'per m2',
		        ],
		        'minimal_costs' => 150,
		        'maintenance_interval' => 100,
		        'maintenance_unit' => [
			        'nl' => 'jaar',
		        ],
	        ],
	        [
		        'measure_type' => 'maintenance',
		        'measure_names' => [
			        'nl' => 'Impregneren gevel',
		        ],
		        'application' => 'place',
		        'costs' => 10, // euro
		        'cost_unit' => [
			        'nl' => 'per m2',
		        ],
		        'minimal_costs' => 150,
		        'maintenance_interval' => 15,
		        'maintenance_unit' => [
			        'nl' => 'jaar',
		        ],
	        ],
	        [
		        'measure_type' => 'maintenance',
		        'measure_names' => [
			        'nl' => 'Gevelschilderwerk op stuk- of metselwerk',
		        ],
		        'application' => 'place',
		        'costs' => 35, // euro
		        'cost_unit' => [
			        'nl' => 'per m2',
		        ],
		        'minimal_costs' => 350,
		        'maintenance_interval' => 10,
		        'maintenance_unit' => [
			        'nl' => 'jaar',
		        ],
	        ],
	        [
		        'measure_type' => 'maintenance',
		        'measure_names' => [
			        'nl' => 'Schilderwerk houten geveldelen',
		        ],
		        'application' => 'place',
		        'costs' => 140, // euro
		        'cost_unit' => [
			        'nl' => 'per m2',
		        ],
		        'minimal_costs' => 400,
		        'maintenance_interval' => 7,
		        'maintenance_unit' => [
			        'nl' => 'jaar',
		        ],
	        ],
	        [
		        'measure_type' => 'maintenance',
		        'measure_names' => [
			        'nl' => 'Vervangen dakpannen',
		        ],
		        'application' => 'replace',
		        'costs' => 135, // euro
		        'cost_unit' => [
			        'nl' => 'per m2',
		        ],
		        'minimal_costs' => 1200,
		        'maintenance_interval' => 80,
		        'maintenance_unit' => [
			        'nl' => 'jaar',
		        ],
	        ],
	        [
		        'measure_type' => 'maintenance',
		        'measure_names' => [
			        'nl' => 'Vervangen dakbedekking',
		        ],
		        'application' => 'replace',
		        'costs' => 100, // euro
		        'cost_unit' => [
			        'nl' => 'per m2',
		        ],
		        'minimal_costs' => 700,
		        'maintenance_interval' => 25,
		        'maintenance_unit' => [
			        'nl' => 'jaar',
		        ],
	        ],


			// add more onderhoudsmaatregelen here!

        ];




        $translationUUIDs = [];

        foreach($measureApplications as $measureApplication){
            foreach($measureApplication['measure_names'] as $locale => $measureName) {
            	if (!array_key_exists('measure_names', $translationUUIDs)){
            		$translationUUIDs['measure_names'] = [];
	            }
	            if (!array_key_exists($locale, $translationUUIDs['measure_names'])){
            		$translationUUIDs['measure_names'][$locale] = [];
	            }
            	if (!isset($translationUUIDs['measure_names'][$locale][$measureName])) {
		            $mnUuid = \App\Helpers\Str::uuid();
		            \DB::table( 'translations' )->insert( [
			            'key'         => $mnUuid,
			            'language'    => $locale,
			            'translation' => $measureName,
		            ] );
		            $translationUUIDs['measure_names'][$locale][$measureName] = $mnUuid;
	            }
	            else {
		            $mnUuid = $translationUUIDs['measure_names'][$locale][$measureName];
	            }
            }

	        foreach($measureApplication['cost_unit'] as $locale => $costUnitName) {
		        if (!array_key_exists('cost_unit', $translationUUIDs)){
			        $translationUUIDs['cost_unit'] = [];
		        }
		        if (!array_key_exists($locale, $translationUUIDs['cost_unit'])){
			        $translationUUIDs['cost_unit'][$locale] = [];
		        }
		        if (!isset($translationUUIDs['cost_unit'][$locale][$costUnitName])) {
			        $cuUUID = \App\Helpers\Str::uuid();
			        \DB::table( 'translations' )->insert( [
				        'key'         => $cuUUID,
				        'language'    => $locale,
				        'translation' => $costUnitName,
			        ] );
			        $translationUUIDs['cost_unit'][$locale][$costUnitName] = $cuUUID;
		        }
		        else {
			        $cuUUID = $translationUUIDs['cost_unit'][$locale][$costUnitName];
		        }
	        }

	        foreach($measureApplication['maintenance_unit'] as $locale => $maintenanceUnitName) {
		        if (!array_key_exists('maintenance_unit', $translationUUIDs)){
			        $translationUUIDs['maintenance_unit'] = [];
		        }
		        if (!array_key_exists($locale, $translationUUIDs['maintenance_unit'])){
			        $translationUUIDs['maintenance_unit'][$locale] = [];
		        }
		        if (!isset($translationUUIDs['maintenance_unit'][$locale][$maintenanceUnitName])) {
			        $muUUID = \App\Helpers\Str::uuid();
			        \DB::table( 'translations' )->insert( [
				        'key'         => $muUUID,
				        'language'    => $locale,
				        'translation' => $maintenanceUnitName,
			        ] );
			        $translationUUIDs['maintenance_unit'][$locale][$maintenanceUnitName] = $muUUID;
		        }
		        else {
			        $muUUID = $translationUUIDs['maintenance_unit'][$locale][$maintenanceUnitName];
		        }
	        }

	        DB::table('measure_applications')->insert([
	        	'measure_type' => $measureApplication['measure_type'],
		        'measure_name' => $mnUuid,
		        'application' => $measureApplication['application'],
		        'costs' => $measureApplication['costs'],
		        'cost_unit' => $cuUUID,
		        'minimal_costs' => $measureApplication['minimal_costs'],
		        'maintenance_interval' => $measureApplication['maintenance_interval'],
		        'maintenance_unit' => $muUUID,
	        ]);

        }
    }
}
