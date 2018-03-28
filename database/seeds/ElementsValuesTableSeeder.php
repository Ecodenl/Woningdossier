<?php

use Illuminate\Database\Seeder;

class ElementsValuesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $elements = [
        	[
	            'names' => [
	                'nl' => 'Ramen in de leefruimtes',
		        ],
		        'service_type' => 'Heating',
		        'order' => 0,
		        'info' => [
		        	'nl' => 'Infotext hier',
		        ],
		        'element_values' => [
		            [
		            	'values' => [
		                    'nl' => 'Enkelglas',
			            ],
			            'order' => 0,
			        ],
			        [
				        'values' => [
				            'nl' => 'Dubbelglas',
				        ],
				        'order' => 1,
			        ],
			        [
				        'values' => [
				            'nl' => 'HR++ glas',
				        ],
				        'order' => 2,
			        ],
			        [
				        'values' => [
				            'nl' => 'Drievoudige beglazing',
				        ],
				        'order' => 3,
			        ],
		        ],
	        ],
	        [
		        'names' => [
			        'nl' => 'Ramen in de slaapruimtes',
		        ],
		        'service_type' => 'Heating',
		        'order' => 1,
		        'info' => [
			        'nl' => 'Infotext hier',
		        ],
		        'element_values' => [
			        [
				        'values' => [
					        'nl' => 'Enkelglas',
				        ],
				        'order' => 0,
			        ],
			        [
				        'values' => [
					        'nl' => 'Dubbelglas',
				        ],
				        'order' => 1,
			        ],
			        [
				        'values' => [
					        'nl' => 'HR++ glas',
				        ],
				        'order' => 2,
			        ],
			        [
				        'values' => [
					        'nl' => 'Drievoudige beglazing',
				        ],
				        'order' => 3,
			        ],
		        ],
	        ],
	        [
		        'names' => [
			        'nl' => 'Gevelisolatie',
		        ],
		        'service_type' => 'Heating',
		        'order' => 2,
		        'info' => [
			        'nl' => 'Infotext hier',
		        ],
		        'element_values' => [
			        [
				        'values' => [
					        'nl' => 'Onbekend',
				        ],
				        'order' => 0,
			        ],
			        [
				        'values' => [
					        'nl' => 'Geen isolatie',
				        ],
				        'order' => 1,
			        ],
			        [
				        'values' => [
					        'nl' => 'Matige isolatie (tot 8 cm isolatie)',
				        ],
				        'order' => 2,
			        ],
			        [
				        'values' => [
					        'nl' => 'Goede isolatie (8 tot 20 cm isolatie)',
				        ],
				        'order' => 3,
			        ],
			        [
				        'values' => [
					        'nl' => 'Goede isolatie (8 tot 20 cm isolatie)',
				        ],
				        'order' => 4,
			        ],
			        [
				        'values' => [
					        'nl' => 'Niet van toepassing',
				        ],
				        'order' => 5,
			        ],
		        ],
	        ],
	        [
		        'names' => [
			        'nl' => 'Vloerisolatie',
		        ],
		        'service_type' => 'Heating',
		        'order' => 3,
		        'info' => [
			        'nl' => 'Infotext hier',
		        ],
		        'element_values' => [
			        [
				        'values' => [
					        'nl' => 'Onbekend',
				        ],
				        'order' => 0,
			        ],
			        [
				        'values' => [
					        'nl' => 'Geen isolatie',
				        ],
				        'order' => 1,
			        ],
			        [
				        'values' => [
					        'nl' => 'Matige isolatie (tot 8 cm isolatie)',
				        ],
				        'order' => 2,
			        ],
			        [
				        'values' => [
					        'nl' => 'Goede isolatie (8 tot 20 cm isolatie)',
				        ],
				        'order' => 3,
			        ],
			        [
				        'values' => [
					        'nl' => 'Goede isolatie (8 tot 20 cm isolatie)',
				        ],
				        'order' => 4,
			        ],
			        [
				        'values' => [
					        'nl' => 'Niet van toepassing',
				        ],
				        'order' => 5,
			        ],
		        ],
	        ],
	        [
		        'names' => [
			        'nl' => 'Dakisolatie',
		        ],
		        'service_type' => 'Heating',
		        'order' => 4,
		        'info' => [
			        'nl' => 'Infotext hier',
		        ],
		        'element_values' => [
			        [
				        'values' => [
					        'nl' => 'Onbekend',
				        ],
				        'order' => 0,
			        ],
			        [
				        'values' => [
					        'nl' => 'Geen isolatie',
				        ],
				        'order' => 1,
			        ],
			        [
				        'values' => [
					        'nl' => 'Matige isolatie (tot 8 cm isolatie)',
				        ],
				        'order' => 2,
			        ],
			        [
				        'values' => [
					        'nl' => 'Goede isolatie (8 tot 20 cm isolatie)',
				        ],
				        'order' => 3,
			        ],
			        [
				        'values' => [
					        'nl' => 'Goede isolatie (8 tot 20 cm isolatie)',
				        ],
				        'order' => 4,
			        ],
			        [
				        'values' => [
					        'nl' => 'Niet van toepassing',
				        ],
				        'order' => 5,
			        ],
		        ],
	        ],
        ];

        foreach($elements as $element){
	        $uuid = \App\Helpers\Str::uuid();
	        foreach($element['names'] as $locale => $name) {
		        \DB::table( 'translations' )->insert( [
			        'key'         => $uuid,
			        'language'    => $locale,
			        'translation' => $name,
		        ] );
	        }

	        $infoUuid = \App\Helpers\Str::uuid();
	        foreach($element['info'] as $locale => $name) {
		        \DB::table( 'translations' )->insert( [
			        'key'         => $infoUuid,
			        'language'    => $locale,
			        'translation' => $name,
		        ] );
	        }

	        $nameUuid = \DB::table('translations')
	                       ->where('translation', $element['service_type'])
	                       ->where('language', 'en')
	                       ->first(['key']);

	        // Get the category. If it doesn't exist: create it
	        $serviceType = \DB::table('service_types')->where('name', $nameUuid->key)->first();

	        if ($serviceType instanceof \stdClass) {
		        $elementId = \DB::table( 'elements' )->insertGetId( [
			        'name'            => $uuid,
			        'service_type_id' => $serviceType->id,
			        'order' => $element['order'],
					'info' => $infoUuid,
	            ] );

		        foreach($element['element_values'] as $elementValue){
		        	$uuid = \App\Helpers\Str::uuid();
		        	foreach($elementValue['values'] as $locale => $name){
				        \DB::table( 'translations' )->insert( [
					        'key'         => $uuid,
					        'language'    => $locale,
					        'translation' => $name,
				        ] );
			        }

			        \DB::table('element_values')->insert([
			        	'element_id' => $elementId,
				        'value' => $uuid,
				        'order' => $elementValue['order'],
			        ]);
		        }
	        }
        }
    }
}
