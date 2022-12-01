<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

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
                [
                    'name' => [
                        'nl' => 'Isolatie van de vloer',
                    ],
                    'service_types' => [
                        'locale' => 'en',
                        'value' => 'Heating',
                    ],
                ],
                [
                    'name' => [
                        'nl' => 'Isolatie van de bodem',
                    ],
                    'service_types' => [
                        'locale' => 'en',
                        'value' => 'Heating',
                    ],
                ],
                [
                    'name' => [
                        'nl' => 'Isolatie van de kruipruimte',
                    ],
                    'service_types' => [
                        'locale' => 'en',
                        'value' => 'Heating',
                    ],
                ],
                [
                    'name' => [
                        'nl' => 'Leiding isolatie kruipruimte',
                    ],
                    'service_types' => [
                        'locale' => 'en',
                        'value' => 'Heating',
                    ],
                ],
            ],
            'Gevelisolatie' => [
                [
                    'name' => [
                        'nl' => 'Gevel isolatie spouw',
                    ],
                    'service_types' => [
                        'locale' => 'en',
                        'value' => 'Heating',
                    ],
                ],
                [
                    'name' => [
                        'nl' => 'Gevel isolatie binnenzijde',
                    ],
                    'service_types' => [
                        'locale' => 'en',
                        'value' => 'Heating',
                    ],
                ],
                [
                    'name' => [
                        'nl' => 'Gevel isolatie buitenzijde',
                    ],
                    'service_types' => [
                        'locale' => 'en',
                        'value' => 'Heating',
                    ],
                ],
            ],
            'Dakisolatie' => [
                [
                    'name' => [
                        'nl' => 'Isolatie hellend dak binnen',
                    ],
                    'service_types' => [
                        'locale' => 'en',
                        'value' => 'Heating',
                    ],
                ],
                [
                    'name' => [
                        'nl' => 'Isolatie hellend dak, buiten (vervangen dakpannen, bitume isolerend onderdak etc)',
                    ],
                    'service_types' => [
                        'locale' => 'en',
                        'value' => 'Heating',
                    ],
                ],
                [
                    'name' => [
                        'nl' => 'Isolatie plat dak, buiten (op huidige dakbedekking)',
                    ],
                    'service_types' => [
                        'locale' => 'en',
                        'value' => 'Heating',
                    ],
                ],
                [
                    'name' => [
                        'nl' => 'Isolatie plat dak, buiten (vervanging huidige dakbedekking)',
                    ],
                    'service_types' => [
                        'locale' => 'en',
                        'value' => 'Heating',
                    ],
                ],
                [
                    'name' => [
                        'nl' => 'Vegetatiedak',
                    ],
                    'service_types' => [
                        'locale' => 'en',
                        'value' => 'Heating',
                    ],
                ],
                [
                    'name' => [
                        'nl' => 'Isolatie zoldervloer, bovenop',
                    ],
                    'service_types' => [
                        'locale' => 'en',
                        'value' => 'Heating',
                    ],
                ],
                [
                    'name' => [
                        'nl' => 'Isolatie zoldervloer, tussen plafond',
                    ],
                    'service_types' => [
                        'locale' => 'en',
                        'value' => 'Heating',
                    ],
                ],
            ],
            'Isolatieglas' => [
                [
                    'name' => [
                        'nl' => 'Glas-in-lood',
                    ],
                    'service_types' => [
                        'locale' => 'en',
                        'value' => 'Heating',
                    ],
                ],
                [
                    'name' => [
                        'nl' => 'Plaatsen isolatieglas, alleen beglazing',
                    ],
                    'service_types' => [
                        'locale' => 'en',
                        'value' => 'Heating',
                    ],
                ],
                [
                    'name' => [
                        'nl' => 'Plaatsen isolatieglas, inclusief kozijn',
                    ],
                    'service_types' => [
                        'locale' => 'en',
                        'value' => 'Heating',
                    ],
                ],
                [
                    'name' => [
                        'nl' => 'Plaatsen geïsoleerd kozijn met triple glas',
                    ],
                    'service_types' => [
                        'locale' => 'en',
                        'value' => 'Heating',
                    ],
                ],
                [
                    'name' => [
                        'nl' => 'Plaatsen achterzetbeglazing',
                    ],
                    'service_types' => [
                        'locale' => 'en',
                        'value' => 'Heating',
                    ],
                ],
            ],
            'Kierdichting' => [
                [
                    'name' => [
                        'nl' => 'Kierdichting ramen en deuren',
                    ],
                    'service_types' => [
                        'locale' => 'en',
                        'value' => 'Heating',
                    ],
                ],
                [
                    'name' => [
                        'nl' => 'Kierdichting aansluiting kozijn en muur',
                    ],
                    'service_types' => [
                        'locale' => 'en',
                        'value' => 'Heating',
                    ],
                ],
                [
                    'name' => [
                        'nl' => 'Kierdichting aansluiting dak en muur',
                    ],
                    'service_types' => [
                        'locale' => 'en',
                        'value' => 'Heating',
                    ],
                ],
                [
                    'name' => [
                        'nl' => 'Kierdichting aansluiting nok',
                    ],
                    'service_types' => [
                        'locale' => 'en',
                        'value' => 'Heating',
                    ],
                ],
                [
                    'name' => [
                        'nl' => 'Kierdichting kruipluik, houten vloer',
                    ],
                    'service_types' => [
                        'locale' => 'en',
                        'value' => 'Heating',
                    ],
                ],
            ],
            'Ventilatie' => [
                [
                    'name' => [
                        'nl' => 'Ventilatie roosters',
                    ],
                    'service_types' => [
                        'locale' => 'en',
                        'value' => 'Ventilation',
                    ],
                ],
                [
                    'name' => [
                        'nl' => 'Vraag gestuurde ventilatie roosters',
                    ],
                    'service_types' => [
                        'locale' => 'en',
                        'value' => 'Ventilation',
                    ],
                ],
                [
                    'name' => [
                        'nl' => 'Ventilatie lucht/water warmtepomp',
                    ],
                    'service_types' => [
                        'locale' => 'en',
                        'value' => 'Ventilation',
                    ],
                ],
                [
                    'name' => [
                        'nl' => 'Decentrale wtw',
                    ],
                    'service_types' => [
                        'locale' => 'en',
                        'value' => 'Ventilation',
                    ],
                ],
                [
                    'name' => [
                        'nl' => 'Centrale wtw',
                    ],
                    'service_types' => [
                        'locale' => 'en',
                        'value' => 'Ventilation',
                    ],
                ],
                [
                    'name' => [
                        'nl' => 'Gelijkstroom ventilatiebox',
                    ],
                    'service_types' => [
                        'locale' => 'en',
                        'value' => 'Ventilation',
                    ],
                ],
            ],
            'Cv-ketel' => [
                [
                    'name' => [
                        'nl' => 'Ketelvervanging',
                    ],
                    'service_types' => [
                        'locale' => 'en',
                        'value' => 'Heating',
                    ],
                ],
                [
                    'name' => [
                        'nl' => 'Waterzijdig inregelen',
                    ],
                    'service_types' => [
                        'locale' => 'en',
                        'value' => 'Heating',
                    ],
                ],
                [
                    'name' => [
                        'nl' => 'Thermostaatknoppen',
                    ],
                    'service_types' => [
                        'locale' => 'en',
                        'value' => 'Heating',
                    ],
                ],
                [
                    'name' => [
                        'nl' => 'Weersafhankelijke regeling',
                    ],
                    'service_types' => [
                        'locale' => 'en',
                        'value' => 'Heating',
                    ],
                ],
                [
                    'name' => [
                        'nl' => 'Slimme thermostaat (thermosmart, nest, tado,…) opentherm of aan/uit',
                    ],
                    'service_types' => [
                        'locale' => 'en',
                        'value' => 'Heating',
                    ],
                ],
                [
                    'name' => [
                        'nl' => 'Zone indeling',
                    ],
                    'service_types' => [
                        'locale' => 'en',
                        'value' => 'Heating',
                    ],
                ],
                [
                    'name' => [
                        'nl' => 'Isolatie leiding onverwarmde ruimte',
                    ],
                    'service_types' => [
                        'locale' => 'en',
                        'value' => 'Heating',
                    ],
                ],
            ],
            'Warmtepomp' => [
                [
                    'name' => [
                        'nl' => 'Hybride (bron lucht)',
                    ],
                    'service_types' => [
                        'locale' => 'en',
                        'value' => 'Heating',
                    ],
                ],
                [
                    'name' => [
                        'nl' => 'Volledig (bron lucht)',
                    ],
                    'service_types' => [
                        'locale' => 'en',
                        'value' => 'Heating',
                    ],
                ],
                [
                    'name' => [
                        'nl' => 'Volledig (bron bodem)',
                    ],
                    'service_types' => [
                        'locale' => 'en',
                        'value' => 'Heating',
                    ],
                ],
                [
                    'name' => [
                        'nl' => 'Volledig (bron ventilatielucht)',
                    ],
                    'service_types' => [
                        'locale' => 'en',
                        'value' => 'Heating',
                    ],
                ],
                [
                    'name' => [
                        'nl' => 'Warmtepompboiler (tbv tapwater)',
                    ],
                    'service_types' => [
                        'locale' => 'en',
                        'value' => 'Heating',
                    ],
                ],
                [
                    'name' => [
                        'nl' => 'Smart grid compatibel',
                    ],
                    'service_types' => [
                        'locale' => 'en',
                        'value' => 'Heating',
                    ],
                ],
            ],
            'Biomassa' => [
                [
                    'name' => [
                        'nl' => 'Pelletketel',
                    ],
                    'service_types' => [
                        'locale' => 'en',
                        'value' => 'Heating',
                    ],
                ],
                [
                    'name' => [
                        'nl' => 'Pelletkachel',
                    ],
                    'service_types' => [
                        'locale' => 'en',
                        'value' => 'Heating',
                    ],
                ],
                [
                    'name' => [
                        'nl' => 'Massakachel (Tulikivi, Ortner)',
                    ],
                    'service_types' => [
                        'locale' => 'en',
                        'value' => 'Heating',
                    ],
                ],
                [
                    'name' => [
                        'nl' => 'Cv-gekoppeld',
                    ],
                    'service_types' => [
                        'locale' => 'en',
                        'value' => 'Heating',
                    ],
                ],
                [
                    'name' => [
                        'nl' => 'Hoogrendementshoutkachel (laag emissie fijn stof)',
                    ],
                    'service_types' => [
                        'locale' => 'en',
                        'value' => 'Heating',
                    ],
                ],
            ],
            'Warmte afgifte' => [
                [
                    'name' => [
                        'nl' => 'Laag temperatuur vloerverwarming',
                    ],
                    'service_types' => [
                        'locale' => 'en',
                        'value' => 'PV-wind',
                    ],
                ],
                [
                    'name' => [
                        'nl' => 'Laag temperatuur wandverwarming',
                    ],
                    'service_types' => [
                        'locale' => 'en',
                        'value' => 'PV-wind',
                    ],
                ],
                [
                    'name' => [
                        'nl' => 'Laag temperatuur convectoren',
                    ],
                    'service_types' => [
                        'locale' => 'en',
                        'value' => 'PV-wind',
                    ],
                ],
                [
                    'name' => [
                        'nl' => 'Tralingspanelen',
                    ],
                    'service_types' => [
                        'locale' => 'en',
                        'value' => 'PV-wind',
                    ],
                ],
                [
                    'name' => [
                        'nl' => 'Luchtverwarming',
                    ],
                    'service_types' => [
                        'locale' => 'en',
                        'value' => 'PV-wind',
                    ],
                ],
                [
                    'name' => [
                        'nl' => 'Radiatoren (laag regime 55-45)',
                    ],
                    'service_types' => [
                        'locale' => 'en',
                        'value' => 'PV-wind',
                    ],
                ],
            ],
            'Zonnepanelen' => [
            ],
            'Zonneboiler' => [
                [
                    'name' => [
                        'nl' => 'Vacuumbuiscollector',
                    ],
                    'service_types' => [
                        'locale' => 'en',
                        'value' => [
                            'Heating', 
                            'Domestic hot water',
                        ],
                    ],
                ],
                [
                    'name' => [
                        'nl' => 'Vlakkeplaat',
                    ],
                    'service_types' => [
                        'locale' => 'en',
                        'value' => [
                            'Heating',
                            'Domestic hot water',
                        ],
                    ],
                ],
                [
                    'name' => [
                        'nl' => 'Voorverwarming SWW',
                    ],
                    'service_types' => [
                        'locale' => 'en',
                        'value' => [
                            'Heating',
                            'Domestic hot water',
                        ],
                    ],
                ],
                [
                    'name' => [
                        'nl' => 'SWW naverwarming',
                    ],
                    'service_types' => [
                        'locale' => 'en',
                        'value' => [
                            'Heating',
                            'Domestic hot water',
                        ],
                    ],
                ],
                [
                    'name' => [
                        'nl' => 'SWW + verwarmingsondersteuning',
                    ],
                    'service_types' => [
                        'locale' => 'en',
                        'value' => [
                            'Heating',
                            'Domestic hot water',
                        ],
                    ],
                ],
            ],
            'PVT' => [],
            'Opslag' => [
                [
                    'name' => [
                        'nl' => 'Thermische opslag',
                    ],
                    'service_types' => [
                        'locale' => 'en',
                        'value' => 'Others',
                    ],
                ],
                [
                    'name' => [
                        'nl' => 'Huisbatterij',
                    ],
                    'service_types' => [
                        'locale' => 'en',
                        'value' => 'Others',
                    ],
                ],
                [
                    'name' => [
                        'nl' => 'Koppeling elektrische auto',
                    ],
                    'service_types' => [
                        'locale' => 'en',
                        'value' => 'Others',
                    ],
                ],
            ],
            'Overig' => [
                [
                    'name' => [
                        'nl' => 'Spaardouche',
                    ],
                    'service_types' => [
                        'locale' => 'en',
                        'value' => 'Domestic hot water',
                    ],
                ],
                [
                    'name' => [
                        'nl' => 'Douche wtw',
                    ],
                    'service_types' => [
                        'locale' => 'en',
                        'value' => 'Domestic hot water',
                    ],
                ],
                [
                    'name' => [
                        'nl' => 'Hotfill',
                    ],
                    'service_types' => [
                        'locale' => 'en',
                        'value' => 'Domestic hot water',
                    ],
                ],
                [
                    'name' => [
                        'nl' => 'LED verlichting',
                    ],
                    'service_types' => [
                        'locale' => 'en',
                        'value' => 'Lighting',
                    ],
                ],
                [
                    'name' => [
                        'nl' => 'Witgoed',
                    ],
                    'service_types' => [
                        'locale' => 'en',
                        'value' => 'Appliances',
                    ],
                ],
                [
                    'name' => [
                        'nl' => 'Elektrische apparatuur',
                    ],
                    'service_types' => [
                        'locale' => 'en',
                        'value' => 'Appliances',
                    ],
                ],
                [
                    'name' => [
                        'nl' => 'Monitoring verbruik',
                    ],
                    'service_types' => [
                        'locale' => 'en',
                        'value' => 'Building automation and control',
                    ],
                ],
                [
                    'name' => [
                        'nl' => 'Energiemanagement',
                    ],
                    'service_types' => [
                        'locale' => 'en',
                        'value' => 'Building automation and control',
                    ],
                ],
                [
                    'name' => [
                        'nl' => 'Zonwering',
                    ],
                    'service_types' => [
                        'locale' => 'en',
                        'value' => 'Others',
                    ],
                ],
                [
                    'name' => [
                        'nl' => 'Standbyverbruik',
                    ],
                    'service_types' => [
                        'locale' => 'en',
                        'value' => 'Others',
                    ],
                ],
            ],
        ];

        foreach ($categorizedMeasures as $category => $measures) {
            // Get the category.
            $cat = DB::table('measure_categories')->where('name->nl', $category)->first();
            if ($cat instanceof \stdClass) {
                // Create the measures
                foreach ($measures as $measure) {
                    $mid = DB::table('measures')->insertGetId(
                        ['name' => json_encode($measure['name'])]
                    );
                    // and link them
                    DB::table('measure_measure_category')->insert([
                        'measure_id' => $mid,
                        'measure_category_id' => $cat->id,
                    ]);

                    $serviceTypeData = $measure['service_types'];
                    $typeLocale = $serviceTypeData['locale'];
                    $serviceTypes = is_array($serviceTypeData['value']) ? $serviceTypeData['value'] : [$serviceTypeData['value']];

                    foreach ($serviceTypes as $serviceTypeName) {
                        $serviceType = DB::table('service_types')
                            ->where("name->{$typeLocale}", $serviceTypeName)
                            ->first();

                        if ($serviceType instanceof \stdClass) {
                            DB::table('measure_service_type')->insert([
                                'measure_id' => $mid,
                                'service_type_id' => $serviceType->id,
                            ]);
                        }
                    }
                }
            }
        }
    }
}
