<?php

use Illuminate\Database\Seeder;

class KeyFigureTemperaturesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

	    $figures = [
	    	[
				'measure_application' => 'Bent u geïnteresseerd in glas in lood vervangen?',
			    'insulating_glazing' => 'Enkelglas',
			    'building_heating' => 'Verwarmd, de meeste radiatoren staan aan',
			    'key_figure' => 13.04,
		    ],
		    [
			    'measure_application' => 'Bent u geïnteresseerd in glas in lood vervangen?',
			    'insulating_glazing' => 'Enkelglas',
			    'building_heating' => 'Matig verwarmd, de meeste radiatoren staan hoger dan * of een aantal radiatoren staan hoog',
			    'key_figure' => 9.02,
		    ],
		    [
			    'measure_application' => 'Bent u geïnteresseerd in glas in lood vervangen?',
			    'insulating_glazing' => 'Enkelglas',
			    'building_heating' => 'Onverwarmd, de radiatoren staan op * of uit',
			    'key_figure' => 5.65,
		    ],
		    [
			    'measure_application' => 'Bent u geïnteresseerd in glas in lood vervangen?',
			    'insulating_glazing' => 'Dubbelglas / voorzetraam',
			    'building_heating' => 'Verwarmd, de meeste radiatoren staan aan',
			    'key_figure' => 0.00,
		    ],
		    [
			    'measure_application' => 'Bent u geïnteresseerd in glas in lood vervangen?',
			    'insulating_glazing' => 'Dubbelglas / voorzetraam',
			    'building_heating' => 'Matig verwarmd, de meeste radiatoren staan hoger dan * of een aantal radiatoren staan hoog',
			    'key_figure' => 0.00,
		    ],
		    [
			    'measure_application' => 'Bent u geïnteresseerd in glas in lood vervangen?',
			    'insulating_glazing' => 'Dubbelglas / voorzetraam',
			    'building_heating' => 'Onverwarmd, de radiatoren staan op * of uit',
			    'key_figure' => 0.00,
		    ],
		    [
			    'measure_application' => 'Bent u geïnteresseerd in het plaatsen van HR++ glas (alleen het glas)?',
			    'insulating_glazing' => 'Enkelglas',
			    'building_heating' => 'Verwarmd, de meeste radiatoren staan aan',
			    'key_figure' => 20.32,
		    ],
		    [
			    'measure_application' => 'Bent u geïnteresseerd in het plaatsen van HR++ glas (alleen het glas)?',
			    'insulating_glazing' => 'Enkelglas',
			    'building_heating' => 'Matig verwarmd, de meeste radiatoren staan hoger dan * of een aantal radiatoren staan hoog',
			    'key_figure' => 14.28,
		    ],
		    [
			    'measure_application' => 'Bent u geïnteresseerd in het plaatsen van HR++ glas (alleen het glas)?',
			    'insulating_glazing' => 'Enkelglas',
			    'building_heating' => 'Onverwarmd, de radiatoren staan op * of uit',
			    'key_figure' => 9.19,
		    ],
		    [
			    'measure_application' => 'Bent u geïnteresseerd in het plaatsen van HR++ glas (alleen het glas)?',
			    'insulating_glazing' => 'Dubbelglas / voorzetraam',
			    'building_heating' => 'Verwarmd, de meeste radiatoren staan aan',
			    'key_figure' => 7.27,
		    ],
		    [
			    'measure_application' => 'Bent u geïnteresseerd in het plaatsen van HR++ glas (alleen het glas)?',
			    'insulating_glazing' => 'Dubbelglas / voorzetraam',
			    'building_heating' => 'Matig verwarmd, de meeste radiatoren staan hoger dan * of een aantal radiatoren staan hoog',
			    'key_figure' => 5.26,
		    ],
		    [
			    'measure_application' => 'Bent u geïnteresseerd in het plaatsen van HR++ glas (alleen het glas)?',
			    'insulating_glazing' => 'Dubbelglas / voorzetraam',
			    'building_heating' => 'Onverwarmd, de radiatoren staan op * of uit',
			    'key_figure' => 3.54,
		    ],
		    [
			    'measure_application' => 'Bent u geïnteresseerd in het plaatsen van HR++ glas (inclusief kozijn)?',
			    'insulating_glazing' => 'Enkelglas',
			    'building_heating' => 'Verwarmd, de meeste radiatoren staan aan',
			    'key_figure' => 21.64,
		    ],
		    [
			    'measure_application' => 'Bent u geïnteresseerd in het plaatsen van HR++ glas (inclusief kozijn)?',
			    'insulating_glazing' => 'Enkelglas',
			    'building_heating' => 'Matig verwarmd, de meeste radiatoren staan hoger dan * of een aantal radiatoren staan hoog',
			    'key_figure' => 15.23,
		    ],
		    [
			    'measure_application' => 'Bent u geïnteresseerd in het plaatsen van HR++ glas (inclusief kozijn)?',
			    'insulating_glazing' => 'Enkelglas',
			    'building_heating' => 'Onverwarmd, de radiatoren staan op * of uit',
			    'key_figure' => 9.82,
		    ],
		    [
			    'measure_application' => 'Bent u geïnteresseerd in het plaatsen van HR++ glas (inclusief kozijn)?',
			    'insulating_glazing' => 'Dubbelglas / voorzetraam',
			    'building_heating' => 'Verwarmd, de meeste radiatoren staan aan',
			    'key_figure' => 8.6,
		    ],
		    [
			    'measure_application' => 'Bent u geïnteresseerd in het plaatsen van HR++ glas (inclusief kozijn)?',
			    'insulating_glazing' => 'Dubbelglas / voorzetraam',
			    'building_heating' => 'Matig verwarmd, de meeste radiatoren staan hoger dan * of een aantal radiatoren staan hoog',
			    'key_figure' => 6.21,
		    ],
		    [
			    'measure_application' => 'Bent u geïnteresseerd in het plaatsen van HR++ glas (inclusief kozijn)?',
			    'insulating_glazing' => 'Dubbelglas / voorzetraam',
			    'building_heating' => 'Onverwarmd, de radiatoren staan op * of uit',
			    'key_figure' => 4.18,
		    ],
		    [
			    'measure_application' => 'Bent u geïnteresseerd in het plaatsen van drievoudige HR beglazing (inclusief kozijn)?',
			    'insulating_glazing' => 'Enkelglas',
			    'building_heating' => 'Verwarmd, de meeste radiatoren staan aan',
			    'key_figure' => 22.96,
		    ],
		    [
			    'measure_application' => 'Bent u geïnteresseerd in het plaatsen van drievoudige HR beglazing (inclusief kozijn)?',
			    'insulating_glazing' => 'Enkelglas',
			    'building_heating' => 'Matig verwarmd, de meeste radiatoren staan hoger dan * of een aantal radiatoren staan hoog',
			    'key_figure' => 16.19,
		    ],
		    [
			    'measure_application' => 'Bent u geïnteresseerd in het plaatsen van drievoudige HR beglazing (inclusief kozijn)?',
			    'insulating_glazing' => 'Enkelglas',
			    'building_heating' => 'Onverwarmd, de radiatoren staan op * of uit',
			    'key_figure' => 10.47,
		    ],
		    [
			    'measure_application' => 'Bent u geïnteresseerd in het plaatsen van drievoudige HR beglazing (inclusief kozijn)?',
			    'insulating_glazing' => 'Dubbelglas / voorzetraam',
			    'building_heating' => 'Verwarmd, de meeste radiatoren staan aan',
			    'key_figure' => 9.92,
		    ],
		    [
			    'measure_application' => 'Bent u geïnteresseerd in het plaatsen van drievoudige HR beglazing (inclusief kozijn)?',
			    'insulating_glazing' => 'Dubbelglas / voorzetraam',
			    'building_heating' => 'Matig verwarmd, de meeste radiatoren staan hoger dan * of een aantal radiatoren staan hoog',
			    'key_figure' => 7.17,
		    ],
		    [
			    'measure_application' => 'Bent u geïnteresseerd in het plaatsen van drievoudige HR beglazing (inclusief kozijn)?',
			    'insulating_glazing' => 'Dubbelglas / voorzetraam',
			    'building_heating' => 'Onverwarmd, de radiatoren staan op * of uit',
			    'key_figure' => 4.82,
		    ],
	    ];

	    foreach($figures as $figure){

	    	$measureApplication = \DB::table('measure_applications')
		                             ->where('translations.language', '=', 'nl')
		                             ->where('translations.translation', '=', $figure['measure_application'])
		                             ->join('translations', 'measure_applications.measure_name', '=', 'translations.key')
		                             ->first(['measure_applications.*']);

		    $insulatedGlazing = \DB::table('insulating_glazings')
		                             ->where('translations.language', '=', 'nl')
		                             ->where('translations.translation', '=', $figure['insulating_glazing'])
		                             ->join('translations', 'insulating_glazings.name', '=', 'translations.key')
		                             ->first(['insulating_glazings.*']);

		    $buildingHeating = \DB::table('building_heatings')
		                             ->where('translations.language', '=', 'nl')
		                             ->where('translations.translation', '=', $figure['building_heating'])
		                             ->join('translations', 'building_heatings.name', '=', 'translations.key')
			                         ->first(['building_heatings.*']);


		    if ($measureApplication instanceof \App\Models\MeasureApplication) {

    	    	\DB::table('key_figure_temperatures')->insert([
	        		'measure_application_id' => $measureApplication->id,
		    	    'insulating_glazing_id' => $insulatedGlazing instanceof \stdClass ? $insulatedGlazing->id : null,
			        'building_heating_id' => $buildingHeating->id,
			        'key_figure' => $figure['key_figure'],
		        ]);
            }
	    }


    }
}
