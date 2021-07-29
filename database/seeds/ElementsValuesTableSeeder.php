<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

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
                'name' => [
                    'nl' => 'Ramen in de leefruimtes',
                ],
                'short' => 'living-rooms-windows',
                'service_type' => [
                    'locale' => 'en',
                    'value' => 'Heating',
                ],
                'order' => 0,
                'info' => [
                    'nl' => 'Als er meerdere soorten glas voorkomen, kies dan hier de soort met de grootste oppervlakte',
                ],
                'element_values' => [
                    [
                        'value' => [
                            'nl' => 'Enkelglas',
                        ],
                        'order' => 0,
                    ],
                    [
                        'value' => [
                            'nl' => 'Dubbelglas',
                        ],
                        'order' => 1,
                    ],
                    [
                        'value' => [
                            'nl' => 'HR++ glas',
                        ],
                        'order' => 2,
                    ],
                    [
                        'value' => [
                            'nl' => 'Drievoudige beglazing',
                        ],
                        'order' => 3,
                    ],
                ],
            ],
            [
                'name' => [
                    'nl' => 'Ramen in de slaapruimtes',
                ],
                'short' => 'sleeping-rooms-windows',
                'service_type' => [
                    'locale' => 'en',
                    'value' => 'Heating',
                ],
                'order' => 1,
                'info' => [
                    'nl' => 'Als er meerdere soorten glas voorkomen, kies dan hier de soort met de grootste oppervlakte',
                ],
                'element_values' => [
                    [
                        'value' => [
                            'nl' => 'Enkelglas',
                        ],
                        'order' => 0,
                    ],
                    [
                        'value' => [
                            'nl' => 'Dubbelglas',
                        ],
                        'order' => 1,
                    ],
                    [
                        'value' => [
                            'nl' => 'HR++ glas',
                        ],
                        'order' => 2,
                    ],
                    [
                        'value' => [
                            'nl' => 'Drievoudige beglazing',
                        ],
                        'order' => 3,
                    ],
                ],
            ],
            [
                'name' => [
                    'nl' => 'Gevelisolatie',
                ],
                'short' => 'wall-insulation',
                'service_type' => [
                    'locale' => 'en',
                    'value' => 'Heating',
                ],
                'order' => 3,
                'info' => [
                    'nl' => 'Denk aan de volgende isolatiemogelijkheden: Gevelisolatie tijdens de bouw, Spouwmuurisolatie, Isolerende voorzetwanden binnen, Buitengevelisolatie.\n Geen isolatie = gevels met 2 cm isolatie of minder Matige isolatie = gevels met een Rc-waarde van minder dan 2,58 m2K/W\n Goede isolatie = gevels met een Rc-waarde van meer dan 2,58 m2K/W',
                ],
                'element_values' => [
                    [
                        'value' => [
                            'nl' => 'Onbekend',
                        ],
                        'order' => 0,
                        'calculate_value' => 1,
                    ],
                    [
                        'value' => [
                            'nl' => 'Geen isolatie',
                        ],
                        'order' => 1,
                        'calculate_value' => 2,
                    ],
                    [
                        'value' => [
                            'nl' => 'Matige isolatie (tot 8 cm isolatie)',
                        ],
                        'order' => 2,
                        'calculate_value' => 3,
                    ],
                    [
                        'value' => [
                            'nl' => 'Goede isolatie (8 tot 20 cm isolatie)',
                        ],
                        'order' => 3,
                        'calculate_value' => 4,
                    ],
                    [
                        'value' => [
                            'nl' => 'Zeer goede isolatie (meer dan 20 cm isolatie)',
                        ],
                        'order' => 5,
                        'calculate_value' => 5,
                    ],
                ],
            ],
            [
                'name' => [
                    'nl' => 'Vloerisolatie',
                ],
                'short' => 'floor-insulation',
                'service_type' => [
                    'locale' => 'en',
                    'value' => 'Heating',
                ],
                'order' => 4,
                'info' => [
                    'nl' => 'Denk aan de volgende isolatiemogelijkheden: Gevelisolatie tijdens de bouw, Spouwmuurisolatie, Isolerende voorzetwanden binnen, Buitengevelisolatie.\n Geen isolatie = gevels met 2 cm isolatie of minder Matige isolatie = gevels met een Rc-waarde van minder dan 2,58 m2K/W\n Goede isolatie = gevels met een Rc-waarde van meer dan 2,58 m2K/W',
                ],
                'element_values' => [
                    [
                        'value' => [
                            'nl' => 'Onbekend',
                        ],
                        'order' => 0,
                        'calculate_value' => 1,
                    ],
                    [
                        'value' => [
                            'nl' => 'Geen isolatie',
                        ],
                        'order' => 1,
                        'calculate_value' => 2,
                    ],
                    [
                        'value' => [
                            'nl' => 'Matige isolatie (tot 8 cm isolatie)',
                        ],
                        'order' => 2,
                        'calculate_value' => 3,
                    ],
                    [
                        'value' => [
                            'nl' => 'Goede isolatie (8 tot 20 cm isolatie)',
                        ],
                        'order' => 3,
                        'calculate_value' => 4,
                    ],
                    [
                        'value' => [
                            'nl' => 'Zeer goede isolatie (meer dan 20 cm isolatie)',
                        ],
                        'order' => 4,
                        'calculate_value' => 5,
                    ],
                    [
                        'value' => [
                            'nl' => 'Niet van toepassing',
                        ],
                        'order' => 5,
                        'calculate_value' => 6,
                    ],
                ],
            ],
            [
                'name' => [
                    'nl' => 'Dakisolatie',
                ],
                'short' => 'roof-insulation',
                'service_type' => [
                    'locale' => 'en',
                    'value' => 'Heating',
                ],
                'order' => 5,
                'info' => [
                    'nl' => 'Denk aan de volgende isolatiemogelijkheden: Gevelisolatie tijdens de bouw, Spouwmuurisolatie, Isolerende voorzetwanden binnen, Buitengevelisolatie.\n Geen isolatie = gevels met 2 cm isolatie of minder Matige isolatie = gevels met een Rc-waarde van minder dan 2,58 m2K/W\n Goede isolatie = gevels met een Rc-waarde van meer dan 2,58 m2K/W',
                ],
                'element_values' => [
                    [
                        'value' => [
                            'nl' => 'Onbekend',
                        ],
                        'order' => 0,
                        'calculate_value' => 1,
                    ],
                    [
                        'value' => [
                            'nl' => 'Geen isolatie',
                        ],
                        'order' => 1,
                        'calculate_value' => 2,
                    ],
                    [
                        'value' => [
                            'nl' => 'Matige isolatie (tot 8 cm isolatie)',
                        ],
                        'order' => 2,
                        'calculate_value' => 3,
                    ],
                    [
                        'value' => [
                            'nl' => 'Goede isolatie (8 tot 20 cm isolatie)',
                        ],
                        'order' => 3,
                        'calculate_value' => 4,
                    ],
                    [
                        'value' => [
                            'nl' => 'Zeer goede isolatie (meer dan 20 cm isolatie)',
                        ],
                        'order' => 4,
                        'calculate_value' => 5,
                    ],
                    [
                        'value' => [
                            'nl' => 'Niet van toepassing',
                        ],
                        'order' => 5,
                        'calculate_value' => 6,
                    ],
                ],
            ],
            [
                'name' => [
                    'nl' => 'Kierdichting',
                ],
                'short' => 'crack-sealing',
                'service_type' => [
                    'locale' => 'en',
                    'value' => 'Heating',
                ],
                'order' => 2,
                'info' => [
                    'nl' => 'Infotext hier',
                ],
                'element_values' => [
                    [
                        'value' => [
                            'nl' => 'Ja, in goede staat',
                        ],
                        'order' => 0,
                        'calculate_value' => 1,
                    ],
                    [
                        'value' => [
                            'nl' => 'Ja, in slechte staat',
                        ],
                        'order' => 1,
                        'calculate_value' => 2,
                    ],
                    [
                        'value' => [
                            'nl' => 'Nee',
                        ],
                        'order' => 2,
                        'calculate_value' => 3,
                    ],
                    [
                        'value' => [
                            'nl' => 'Onbekend',
                        ],
                        'order' => 3,
                        'calculate_value' => 4,
                    ],
                ],
            ],
            [
                'name' => [
                    'nl' => 'Kozijnen',
                ],
                'short' => 'frames',
                'service_type' => [
                    'locale' => 'en',
                    'value' => 'Heating',
                ],
                'order' => 6,
                'info' => [
                    'nl' => 'Infotext hier',
                ],
                'element_values' => [
                    [
                        'value' => [
                            'nl' => 'Alleen houten kozijnen',
                        ],
                        'order' => 0,
                        'calculate_value' => 1, // 100%
                    ],
                    [
                        'value' => [
                            'nl' => 'Houten kozijnen en enkele andere kozijnen (bijvoorbeeld kunststof of aluminium)',
                        ],
                        'order' => 1,
                        'calculate_value' => 0.7, // 70%
                    ],
                    [
                        'value' => [
                            'nl' => 'Enkele houten kozijnen, voornamelijk kunststof en of aluminium',
                        ],
                        'order' => 2,
                        'calculate_value' => 0.3, // 30%
                    ],
                    [
                        'value' => [
                            'nl' => 'Geen houten kozijnen',
                        ],
                        'order' => 3,
                        'calculate_value' => 0, // 0%
                    ],
                    [
                        'value' => [
                            'nl' => 'Overig',
                        ],
                        'order' => 4,
                        'calculate_value' => 0, // 0%
                    ],
                ],
            ],
            [
                'name' => [
                    'nl' => 'Houten bouwdelen',
                ],
                'short' => 'wood-elements',
                'service_type' => [
                    'locale' => 'en',
                    'value' => 'Heating',
                ],
                'order' => 7,
                'info' => [
                    'nl' => 'Infotext hier',
                ],
                'element_values' => [
                    [
                        'value' => [
                            'nl' => 'Dakranden / boeidelen',
                        ],
                        'order' => 0,
                        'calculate_value' => 10, // m2
                    ],
                    [
                        'value' => [
                            'nl' => 'Dakkapellen',
                        ],
                        'order' => 1,
                        'calculate_value' => 2.5, // m2
                    ],
                    [
                        'value' => [
                            'nl' => 'Gevelbekleding',
                        ],
                        'order' => 2,
                        'calculate_value' => 5, // m2
                    ],
                ],
            ],
            [
                'name' => [
                    'nl' => 'Kruipruimte',
                ],
                'short' => 'crawlspace',
                'service_type' => [
                    'locale' => 'en',
                    'value' => 'Heating',
                ],
                'order' => 8,
                'info' => [
                    'nl' => 'Infotext hier',
                ],
                'element_values' => [
                    [
                        'value' => [
                            'nl' => 'Best hoog (meer dan 45 cm)',
                        ],
                        'order' => 0,
                        'calculate_value' => 45, // cm
                    ],
                    [
                        'value' => [
                            'nl' => 'Laag (tussen 30 en 45 cm)',
                        ],
                        'order' => 1,
                        'calculate_value' => 30, // m2
                    ],
                    [
                        'value' => [
                            'nl' => 'Heel laag (minder dan 30 cm)',
                        ],
                        'order' => 2,
                        'calculate_value' => 0, // m2
                    ],
                    [
                        'value' => [
                            'nl' => 'Onbekend',
                        ],
                        'order' => 3,
                        'calculate_value' => 0, // m2
                    ],
                ],
            ],
        ];

        foreach ($elements as $element) {
            $typeLocale = $element['service_type']['locale'];
            $typeValue = $element['service_type']['value'];

            // Get the service type
            $serviceType = DB::table('service_types')
                ->where("name->{$typeLocale}", $typeValue)
                ->first();

            if ($serviceType instanceof \stdClass) {
                $elementId = DB::table('elements')->insertGetId([
                    'name' => json_encode($element['name']),
                    'short' => $element['short'],
                    'service_type_id' => $serviceType->id,
                    'order' => $element['order'],
                    'info' => json_encode($element['info']),
                ]);

                foreach ($element['element_values'] as $elementValue) {
                    DB::table('element_values')->insert([
                        'element_id' => $elementId,
                        'value' => json_encode($elementValue['value']),
                        'order' => $elementValue['order'],
                        'calculate_value' => ($elementValue['calculate_value'] ?? null),
                    ]);
                }
            }
        }
    }
}
