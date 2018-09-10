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
                [
                    'names' => [
                        'nl' => 'Isolatie van de vloer',
                    ],
                    'service_types' => [
                        'Heating',
                    ],
                ],
                [
                    'names' => [
                        'nl' => 'Isolatie van de bodem',
                    ],
                    'service_types' => [
                        'Heating',
                    ],
                ],
                [
                    'names' => [
                        'nl' => 'Isolatie van de kruipruimte',
                    ],
                    'service_types' => [
                        'Heating',
                    ],
                ],
                [
                    'names' => [
                        'nl' => 'Leiding isolatie kruipruimte',
                    ],
                    'service_types' => [
                        'Heating',
                    ],
                ],
            ],
            'Gevelisolatie' => [
                [
                    'names' => [
                        'nl' => 'Gevel isolatie spouw',
                    ],
                    'service_types' => [
                        'Heating',
                    ],
                ],
                [
                    'names' => [
                        'nl' => 'Gevel isolatie binnenzijde',
                    ],
                    'service_types' => [
                        'Heating',
                    ],
                ],
                [
                    'names' => [
                        'nl' => 'Gevel isolatie buitenzijde',
                    ],
                    'service_types' => [
                        'Heating',
                    ],
                ],
            ],
            'Dakisolatie' => [
                [
                    'names' => [
                        'nl' => 'Isolatie hellend dak binnen',
                    ],
                    'service_types' => [
                        'Heating',
                    ],
                ],
                [
                    'names' => [
                        'nl' => 'Isolatie hellend dak, buiten (vervangen dakpannen, bitume isolerend onderdak etc)',
                    ],
                    'service_types' => [
                        'Heating',
                    ],
                ],
                [
                    'names' => [
                        'nl' => 'Isolatie plat dak, buiten (op huidige dakbedekking)',
                    ],
                    'service_types' => [
                        'Heating',
                    ],
                ],
                [
                    'names' => [
                        'nl' => 'Isolatie plat dak, buiten (vervanging huidige dakbedekking)',
                    ],
                    'service_types' => [
                        'Heating',
                    ],
                ],
                [
                    'names' => [
                        'nl' => 'Vegetatiedak',
                    ],
                    'service_types' => [
                        'Heating',
                    ],
                ],
                [
                    'names' => [
                        'nl' => 'Isolatie zoldervloer, bovenop',
                    ],
                    'service_types' => [
                        'Heating',
                    ],
                ],
                [
                    'names' => [
                        'nl' => 'Isolatie zoldervloer, tussen plafond',
                    ],
                    'service_types' => [
                        'Heating',
                    ],
                ],
            ],
            'Isolatieglas' => [
                [
                    'names' => [
                        'nl' => 'Glas-in-lood',
                    ],
                    'service_types' => [
                        'Heating',
                    ],
                ],
                [
                    'names' => [
                        'nl' => 'Plaatsen isolatieglas, alleen beglazing',
                    ],
                    'service_types' => [
                        'Heating',
                    ],
                ],
                [
                    'names' => [
                        'nl' => 'Plaatsen isolatieglas, inclusief kozijn',
                    ],
                    'service_types' => [
                        'Heating',
                    ],
                ],
                [
                    'names' => [
                        'nl' => 'Plaatsen geïsoleerd kozijn met triple glas',
                    ],
                    'service_types' => [
                        'Heating',
                    ],
                ],
                [
                    'names' => [
                        'nl' => 'Plaatsen achterzetbeglazing',
                    ],
                    'service_types' => [
                        'Heating',
                    ],
                ],
            ],
            'Kierdichting' => [
                [
                    'names' => [
                        'nl' => 'Kierdichting ramen en deuren',
                    ],
                    'service_types' => [
                        'Heating',
                    ],
                ],
                [
                    'names' => [
                        'nl' => 'Kierdichting aansluiting kozijn en muur',
                    ],
                    'service_types' => [
                        'Heating',
                    ],
                ],
                [
                    'names' => [
                        'nl' => 'Kierdichting aansluiting dak en muur',
                    ],
                    'service_types' => [
                        'Heating',
                    ],
                ],
                [
                    'names' => [
                        'nl' => 'Kierdichting aansluiting nok',
                    ],
                    'service_types' => [
                        'Heating',
                    ],
                ],
                [
                    'names' => [
                        'nl' => 'Kierdichting kruipluik, houten vloer',
                    ],
                    'service_types' => [
                        'Heating',
                    ],
                ],
            ],
            'Ventilatie' => [
                [
                    'names' => [
                        'nl' => 'Ventilatie roosters',
                    ],
                    'service_types' => [
                        'Ventilation',
                    ],
                ],
                [
                    'names' => [
                        'nl' => 'Vraag gestuurde ventilatie roosters',
                    ],
                    'service_types' => [
                        'Ventilation',
                    ],
                ],
                [
                    'names' => [
                        'nl' => 'Ventilatie lucht/water warmtepomp',
                    ],
                    'service_types' => [
                        'Ventilation',
                    ],
                ],
                [
                    'names' => [
                        'nl' => 'Decentrale wtw',
                    ],
                    'service_types' => [
                        'Ventilation',
                    ],
                ],
                [
                    'names' => [
                        'nl' => 'Centrale wtw',
                    ],
                    'service_types' => [
                        'Ventilation',
                    ],
                ],
                [
                    'names' => [
                        'nl' => 'Gelijkstroom ventilatiebox',
                    ],
                    'service_types' => [
                        'Ventilation',
                    ],
                ],
            ],
            'Cv-ketel' => [
                [
                    'names' => [
                        'nl' => 'Ketelvervanging',
                    ],
                    'service_types' => [
                        'Heating',
                    ],
                ],
                [
                    'names' => [
                        'nl' => 'Waterzijdig inregelen',
                    ],
                    'service_types' => [
                        'Heating',
                    ],
                ],
                [
                    'names' => [
                        'nl' => 'Thermostaatknoppen',
                    ],
                    'service_types' => [
                        'Heating',
                    ],
                ],
                [
                    'names' => [
                        'nl' => 'Weersafhankelijke regeling',
                    ],
                    'service_types' => [
                        'Heating',
                    ],
                ],
                [
                    'names' => [
                        'nl' => 'Slimme thermostaat (thermosmart, nest, tado,…) opentherm of aan/uit',
                    ],
                    'service_types' => [
                        'Heating',
                    ],
                ],
                [
                    'names' => [
                        'nl' => 'Zone indeling',
                    ],
                    'service_types' => [
                        'Heating',
                    ],
                ],
                [
                    'names' => [
                        'nl' => 'Isolatie leiding onverwarmde ruimte',
                    ],
                    'service_types' => [
                        'Heating',
                    ],
                ],
            ],
            'Warmtepomp' => [
                [
                    'names' => [
                        'nl' => 'Hybride (bron lucht)',
                    ],
                    'service_types' => [
                        'Heating',
                    ],
                ],
                [
                    'names' => [
                        'nl' => 'Volledig (bron lucht)',
                    ],
                    'service_types' => [
                        'Heating',
                    ],
                ],
                [
                    'names' => [
                        'nl' => 'Volledig (bron bodem)',
                    ],
                    'service_types' => [
                        'Heating',
                    ],
                ],
                [
                    'names' => [
                        'nl' => 'Volledig (bron ventilatielucht)',
                    ],
                    'service_types' => [
                        'Heating',
                    ],
                ],
                [
                    'names' => [
                        'nl' => 'Warmtepompboiler (tbv tapwater)',
                    ],
                    'service_types' => [
                        'Heating',
                    ],
                ],
                [
                    'names' => [
                        'nl' => 'Smart grid compatibel',
                    ],
                    'service_types' => [
                        'Heating',
                    ],
                ],
            ],
            'Biomassa' => [
                [
                    'names' => [
                        'nl' => 'Pelletketel',
                    ],
                    'service_types' => [
                        'Heating',
                    ],
                ],
                [
                    'names' => [
                        'nl' => 'Pelletkachel',
                    ],
                    'service_types' => [
                        'Heating',
                    ],
                ],
                [
                    'names' => [
                        'nl' => 'Massakachel (Tulikivi, Ortner)',
                    ],
                    'service_types' => [
                        'Heating',
                    ],
                ],
                [
                    'names' => [
                        'nl' => 'Cv-gekoppeld',
                    ],
                    'service_types' => [
                        'Heating',
                    ],
                ],
                [
                    'names' => [
                        'nl' => 'Hoogrendementshoutkachel (laag emissie fijn stof)',
                    ],
                    'service_types' => [
                        'Heating',
                    ],
                ],
            ],
            'Warmte afgifte' => [
                [
                    'names' => [
                        'nl' => 'Laag temperatuur vloerverwarming',
                    ],
                    'service_types' => [
                        'PV-wind',
                    ],
                ],
                [
                    'names' => [
                        'nl' => 'Laag temperatuur wandverwarming',
                    ],
                    'service_types' => [
                        'PV-wind',
                    ],
                ],
                [
                    'names' => [
                        'nl' => 'Laag temperatuur convectoren',
                    ],
                    'service_types' => [
                        'PV-wind',
                    ],
                ],
                [
                    'names' => [
                        'nl' => 'Tralingspanelen',
                    ],
                    'service_types' => [
                        'PV-wind',
                    ],
                ],
                [
                    'names' => [
                        'nl' => 'Luchtverwarming',
                    ],
                    'service_types' => [
                        'PV-wind',
                    ],
                ],
                [
                    'names' => [
                        'nl' => 'Radiatoren (laag regime 55-45)',
                    ],
                    'service_types' => [
                        'PV-wind',
                    ],
                ],
            ],
            'Zonnepanelen' => [
            ],
            'Zonneboiler' => [
                [
                    'names' => [
                        'nl' => 'Vacuumbuiscollector',
                    ],
                    'service_types' => [
                        'Heating',
                        'Domestic hot water',
                    ],
                ],
                [
                    'names' => [
                        'nl' => 'Vlakkeplaat',
                    ],
                    'service_types' => [
                        'Heating',
                        'Domestic hot water',
                    ],
                ],
                [
                    'names' => [
                        'nl' => 'Voorverwarming SWW',
                    ],
                    'service_types' => [
                        'Heating',
                        'Domestic hot water',
                    ],
                ],
                [
                    'names' => [
                        'nl' => 'SWW naverwarming',
                    ],
                    'service_types' => [
                        'Heating',
                        'Domestic hot water',
                    ],
                ],
                [
                    'names' => [
                        'nl' => 'SWW + verwarmingsondersteuning',
                    ],
                    'service_types' => [
                        'Heating',
                        'Domestic hot water',
                    ],
                ],
            ],
            'PVT' => [],
            'Opslag' => [
                [
                    'names' => [
                        'nl' => 'Thermische opslag',
                    ],
                    'service_types' => [
                        'Others',
                    ],
                ],
                [
                    'names' => [
                        'nl' => 'Huisbatterij',
                    ],
                    'service_types' => [
                        'Others',
                    ],
                ],
                [
                    'names' => [
                        'nl' => 'Koppeling elektrische auto',
                    ],
                    'service_types' => [
                        'Others',
                    ],
                ],
            ],
            'Overig' => [
                [
                    'names' => [
                        'nl' => 'Spaardouche',
                    ],
                    'service_types' => [
                        'Domestic hot water',
                    ],
                ],
                [
                    'names' => [
                        'nl' => 'Douche wtw',
                    ],
                    'service_types' => [
                        'Domestic hot water',
                    ],
                ],
                [
                    'names' => [
                        'nl' => 'Hotfill',
                    ],
                    'service_types' => [
                        'Domestic hot water',
                    ],
                ],
                [
                    'names' => [
                        'nl' => 'LED verlichting',
                    ],
                    'service_types' => [
                        'Lighting',
                    ],
                ],
                [
                    'names' => [
                        'nl' => 'Witgoed',
                    ],
                    'service_types' => [
                        'Appliances',
                    ],
                ],
                [
                    'names' => [
                        'nl' => 'Elektrische apparatuur',
                    ],
                    'service_types' => [
                        'Appliances',
                    ],
                ],
                [
                    'names' => [
                        'nl' => 'Monitoring verbruik',
                    ],
                    'service_types' => [
                        'Building automation and control',
                    ],
                ],
                [
                    'names' => [
                        'nl' => 'Energiemanagement',
                    ],
                    'service_types' => [
                        'Building automation and control',
                    ],
                ],
                [
                    'names' => [
                        'nl' => 'Zonwering',
                    ],
                    'service_types' => [
                        'Others',
                    ],
                ],
                [
                    'names' => [
                        'nl' => 'Standbyverbruik',
                    ],
                    'service_types' => [
                        'Others',
                    ],
                ],
            ],
        ];

        foreach ($categorizedMeasures as $category => $measures) {
            $nameUuid = \DB::table('translations')
                          ->where('translation', $category)
                            ->where('language', 'nl')
                            ->first(['key']);

            // Get the category.
            $cat = \DB::table('measure_categories')->where('name', $nameUuid->key)->first();
            if ($cat instanceof \stdClass) {
                // Create the measures
                foreach ($measures as $measure) {
                    $uuid = \App\Helpers\Str::uuid();
                    foreach ($measure['names'] as $locale => $name) {
                        \DB::table('translations')->insert([
                            'key'         => $uuid,
                            'language'    => $locale,
                            'translation' => $name,
                        ]);
                    }
                    $mid = \DB::table('measures')->insertGetId(
                        ['name' => $uuid]
                    );
                    // and link them
                    \DB::table('measure_measure_category')->insert([
                        'measure_id' => $mid,
                        'measure_category_id' => $cat->id,
                    ]);
                    foreach ($measure['service_types'] as $serviceTypeName) {
                        // Link to service type
                        $nameUuid = \DB::table('translations')
                                       ->where('translation', $serviceTypeName)
                                       ->where('language', 'en')
                                       ->first(['key']);

                        $serviceType = \DB::table('service_types')->where('name', $nameUuid->key)->first();
                        if ($serviceType instanceof \stdClass) {
                            \DB::table('measure_service_type')->insert([
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
