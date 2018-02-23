<?php

use Illuminate\Database\Seeder;

class MeasuresTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
	    $categorizedMeasures = [
		    'Vloerisolatie' => [
			    'Isolatie van de vloer',
			    'Isolatie van de bodem',
			    'Isolatie van de kruipruimte',
			    'Leiding isolatie kruipruimte',
		    ],
		    'Gevelisolatie' => [
			    'Gevel isolatie spouw',
			    'Gevel isolatie  binnenzijde',
			    'Gevel isolatie buitenzijde',
		    ],
		    'Dakisolatie' => [
			    'Isolatie hellend dak binnen',
			    'Isolatie hellend dak, buiten (vervangen dakpannen, bitume isolerend onderdak etc)',
			    'Isolatie plat dak, buiten (op huidige dakbedekking)',
			    'Isolatie plat dak, buiten (vervanging huidige dakbedekking)',
			    'Vegetatiedak',
			    'Isolatie zoldervloer, bovenop',
			    'Isolatie zoldervloer, tussen plafond',
		    ],
		    'Isolatieglas' => [
			    'Glas-in-lood',
			    'Plaatsen isolatieglas, alleen beglazing',
			    'Plaatsen isolatieglas, inclusief kozijn',
			    'Plaatsen geïsoleerd kozijn met triple glas',
			    'Plaatsen achterzetbeglazing',
		    ],
		    'Kierdichting' => [
			    'Kierdichting ramen en deuren',
			    'Kierdichting aansluiting kozijn en muur',
			    'Kierdichting aansluiting dak en muur',
			    'Kierdichting aansluiting nok',
			    'Kierdichting kruipluik, houten vloer',
		    ],
		    'Ventilatie' => [
			    'Ventilatie roosters',
			    'Vraag gestuurde ventilatie roosters',
			    'Ventilatie lucht/water warmtepomp',
			    'Decentrale wtw',
			    'Centrale wtw',
			    'Gelijkstroom ventilatiebox',
		    ],
		    'Cv-ketel' => [
			    'Ketelvervanging',
			    'Waterzijdig inregelen',
			    'Thermostaatknoppen',
			    'Weersafhankelijke regeling',
			    'Slimme thermostaat (thermosmart, nest, tado,…) opentherm of aan/uit',
			    'Zone indeling',
			    'Isolatie leiding onverwarmde ruimte',
		    ],
		    'Warmtepomp' => [
			    'Gevel isolatie spouw',
			    'Gevel isolatie  binnenzijde',
			    'Gevel isolatie buitenzijde',
		    ],
		    'Biomassa' => [
			    'Hybride (bron lucht)',
			    'Volledig (bron lucht)',
			    'Volledig (bron bodem)',
			    'Volledig (bron ventilatielucht)',
			    'Warmtepompboiler (tbv tapwater)',
			    'Smart grid compatibel',
		    ],
		    'Warmte afgifte' => [
			    'Pelletketel',
			    'Pelletkachel',
			    'Massakachel (Tulikivi, Ortner)',
			    'Cv-gekoppeld',
			    'Hoogrendementshoutkachel (laag emissie fijn stof)',
		    ],
		    'Zonnepanelen' => [
			    'Laag temperatuur vloerverwarming',
			    'Laag temperatuur wandverwarming',
			    'Laag temperatuur convectoren',
			    'tralingspanelen',
			    'Luchtverwarming',
			    'Radiatoren (laag regime 55-45)',
		    ],
		    'Zonneboiler' => [
			    'Vacuumbuiscollector',
			    'Vlakkeplaat',
			    'Voorverwarming SWW',
			    'SWW naverwarming',
			    'SWW + verwarmingsondersteuning',

		    ],
		    'PVT' => [],
		    'Opslag' => [
			    'Thermische opslag',
			    'Huisbatterij',
			    'Koppeling elektrische auto',
		    ],
		    'Overig' => [
			    'Spaardouche',
			    'Douche wtw',
			    'Hotfill',
			    'LED verlichting',
			    'Witgoed',
		    ],
	    ];

	    foreach ($categorizedMeasures as $category => $measures) {
	    	// Get the category. If it doesn't exist: create it
	    	$cat = \DB::table('measure_categories')->where('name', $category)->first();
	    	if (!$cat instanceof \stdClass){
			    $catId = \DB::table('measure_categories')->insertGetId(
				    [ 'name' => $category ]
			    );
		    } else {
	    		$catId = $cat->id;
		    }
		    // Create the measures
	    	foreach($measures as $measure) {
			    $mid = \DB::table( 'measures' )->insertGetId(
				    [ 'name' => $measure ]
			    );
			    // and link them
			    \DB::table('measure_measure_categories')->insert([
			    	'measure_id' => $mid,
				    'measure_category_id' => $catId,
			    ]);
		    }

	    }
    }
}
