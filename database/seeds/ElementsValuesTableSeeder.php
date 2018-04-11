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
		        'short' => 'living-rooms-windows',
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
		        'short' => 'sleeping-rooms-windows',
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
		        'short' => 'wall-insulation',
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
				        'calculate_value' => 1,
			        ],
			        [
				        'values' => [
					        'nl' => 'Geen isolatie',
				        ],
				        'order' => 1,
				        'calculate_value' => 2,
			        ],
			        [
				        'values' => [
					        'nl' => 'Matige isolatie (tot 8 cm isolatie)',
				        ],
				        'order' => 2,
				        'calculate_value' => 3,
			        ],
			        [
				        'values' => [
					        'nl' => 'Goede isolatie (8 tot 20 cm isolatie)',
				        ],
				        'order' => 3,
				        'calculate_value' => 4,
			        ],
			        [
				        'values' => [
					        'nl' => 'Niet van toepassing',
				        ],
				        'order' => 5,
				        'calculate_value' => 5,
			        ],
		        ],
	        ],
	        [
		        'names' => [
			        'nl' => 'Vloerisolatie',
		        ],
		        'short' => 'floor-insulation',
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
				        'calculate_value' => 1,
			        ],
			        [
				        'values' => [
					        'nl' => 'Geen isolatie',
				        ],
				        'order' => 1,
				        'calculate_value' => 2,
			        ],
			        [
				        'values' => [
					        'nl' => 'Matige isolatie (tot 8 cm isolatie)',
				        ],
				        'order' => 2,
				        'calculate_value' => 3,
			        ],
			        [
				        'values' => [
					        'nl' => 'Goede isolatie (8 tot 20 cm isolatie)',
				        ],
				        'order' => 3,
				        'calculate_value' => 4,
			        ],
			        [
				        'values' => [
					        'nl' => 'Niet van toepassing',
				        ],
				        'order' => 5,
				        'calculate_value' => 5,
			        ],
		        ],
	        ],
	        [
		        'names' => [
			        'nl' => 'Dakisolatie',
		        ],
		        'short' => 'roof-insulation',
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
			[
				'names' => [
					'nl' => 'Kierdichting',
				],
				'short' => 'crack-sealing',
				'service_type' => 'Heating',
				'order' => 5,
				'info' => [
					'nl' => 'Infotext hier',
				],
				'element_values' => [
					[
						'values' => [
							'nl' => 'Ja, in goede staat',
						],
						'order' => 0,
						'calculate_value' => 1,
					],
					[
						'values' => [
							'nl' => 'Ja, in slechte staat',
						],
						'order' => 1,
						'calculate_value' => 2,
					],
					[
						'values' => [
							'nl' => 'Nee',
						],
						'order' => 2,
						'calculate_value' => 3,
					],
					[
						'values' => [
							'nl' => 'Onbekend',
						],
						'order' => 3,
						'calculate_value' => 4,
					],
				],
			],
	        [
	        	'names' => [
	        		'nl' => 'Kozijnen',
		        ],
		        'short' => 'frames',
		        'service_type' => 'Heating',
		        'order' => 6,
		        'info' => [
		        	'nl' => 'Infotext hier',
		        ],
		        'element_values' => [
			        [
				        'values' => [
					        'nl' => 'Alleen houten kozijnen',
				        ],
				        'order' => 0,
				        'calculate_value' => 1, // 100%
			        ],
			        [
				        'values' => [
					        'nl' => 'Houten kozijnen en enkele andere kozijnen (bijvoorbeeld kunststof of aluminium)',
				        ],
				        'order' => 1,
				        'calculate_value' => 0.7, // 70%
			        ],
			        [
				        'values' => [
					        'nl' => 'Enkele houten kozijnen, voornamelijk kunststof en of aluminium',
				        ],
				        'order' => 2,
				        'calculate_value' => 0.3, // 30%
			        ],
			        [
				        'values' => [
					        'nl' => 'Geen houten kozijnen',
				        ],
				        'order' => 3,
				        'calculate_value' => 0, // 0%
			        ],
			        [
				        'values' => [
					        'nl' => 'Overig',
				        ],
				        'order' => 4,
				        'calculate_value' => 0, // 0%
			        ],
		        ],
	        ],
	        [
		        'names' => [
			        'nl' => 'Houten bouwdelen',
		        ],
		        'short' => 'wood-elements',
		        'service_type' => 'Heating',
		        'order' => 7,
		        'info' => [
			        'nl' => 'Infotext hier',
		        ],
		        'element_values' => [
			        [
				        'values' => [
					        'nl' => 'Dakranden / boeidelen',
				        ],
				        'order' => 0,
				        'calculate_value' => 10, // m2
			        ],
			        [
				        'values' => [
					        'nl' => 'Dakkapellen',
				        ],
				        'order' => 1,
				        'calculate_value' => 2.5, // m2
			        ],
			        [
				        'values' => [
					        'nl' => 'Gevelbekleding',
				        ],
				        'order' => 2,
				        'calculate_value' => 5, // m2
			        ],
		        ],
	        ],
	        [
		        'names' => [
			        'nl' => 'Kruipruimte',
		        ],
		        'short' => 'crawlspace',
		        'service_type' => 'Heating',
		        'order' => 8,
		        'info' => [
			        'nl' => 'Infotext hier',
		        ],
		        'element_values' => [
			        [
				        'values' => [
					        'nl' => 'Best hoog (meer dan 45 cm)',
				        ],
				        'order' => 0,
				        'calculate_value' => 45, // cm
			        ],
			        [
				        'values' => [
					        'nl' => 'Laag (tussen 30 en 45 cm)',
				        ],
				        'order' => 1,
				        'calculate_value' => 30, // m2
			        ],
			        [
				        'values' => [
					        'nl' => 'Heel laag (minder dan 30 cm)',
				        ],
				        'order' => 2,
				        'calculate_value' => 0, // m2
			        ],
			        [
				        'values' => [
					        'nl' => 'Onbekend',
				        ],
				        'order' => 3,
				        'calculate_value' => 0, // m2
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
			        'short' => $element['short'],
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
				        'calculate_value' => isset($elementValue['calculate_value']) ? $elementValue['calculate_value'] : null,
			        ]);
		        }
	        }
        }
    }
}
