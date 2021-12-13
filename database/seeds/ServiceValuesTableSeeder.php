<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServiceValuesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $services = [
            [
                'name' => [
                    'nl' => 'Warmtepomp',
                ],
                'short' => 'heat-pump',
                'service_type' => [
                    'locale' => 'en',
                    'value' => 'Heating',
                ],
                'order' => 2,
                'info' => [
                    'nl' => 'Hier kunt u aangeven of u in de huidige situatie in plaats van een cv-ketel een warmtepomp als enige warmteopwekker in huis hebt. U hebt de keuze uit een warmtepomp met buitenlucht of bodemenergie als warmtebron.',
                ],
                'service_values' => [
                    [
                        'value' => [
                            'nl' => 'Geen',
                        ],
                        'order' => 1,
                        'calculate_value' => 1,
                        'configurations' => [
                            'comfort' => 0,
                        ],
                    ],
                    [
                        'value' => [
                            'nl' => 'Volledige warmtepomp buitenlucht',
                        ],
                        'order' => 2,
                        'calculate_value' => 2,
                        'configurations' => [
                            'comfort' => 3,
                        ],
                    ],
                    [
                        'value' => [
                            'nl' => 'Volledige warmtepomp bodem',
                        ],
                        'order' => 3,
                        'calculate_value' => 3,
                        'configurations' => [
                            'comfort' => 3,
                        ],
                    ],
                    [
                        'value' => [
                            'nl' => 'Hybride warmtepomp',
                        ],
                        'order' => 4,
                        'calculate_value' => 4,
                        'configurations' => [
                            'comfort' => 2,
                        ],
                    ],
                    [
                        'value' => [
                            'nl' => 'Collectieve warmtepomp',
                        ],
                        'order' => 5,
                        'calculate_value' => 5,
                        'configurations' => [
                            'comfort' => 2,
                        ],
                    ],
                ],
            ],
            [
                'name' => [
                    'nl' => 'Zonneboiler',
                ],
                'short' => 'sun-boiler',
                'service_type' => [
                    'locale' => 'en',
                    'value' => 'Heating',
                ],
                'order' => 4,
                'info' => [
                    'nl' => 'Hier kunt u aangeven of u in de huidige situatie een zonneboiler hebt en waarvoor u de warmte gebruikt.',
                ],
                'service_values' => [
                    [
                        'value' => [
                            'nl' => 'Geen',
                        ],
                        'order' => 1,
                        'calculate_value' => 1,
                        'configurations' => [
                            'comfort' => 0,
                        ],
                    ],
                    [
                        'value' => [
                            'nl' => 'Voor warm tapwater',
                        ],
                        'order' => 2,
                        'calculate_value' => 2,
                        'configurations' => [
                            'comfort' => 3,
                        ],
                    ],
                    [
                        'value' => [
                            'nl' => 'Voor verwarming',
                        ],
                        'order' => 3,
                        'calculate_value' => 3,
                        'configurations' => [
                            'comfort' => 1,
                        ],
                    ],
                    [
                        'value' => [
                            'nl' => 'Voor verwarming en warm tapwater',
                        ],
                        'order' => 4,
                        'calculate_value' => 4,
                        'configurations' => [
                            'comfort' => 3,
                        ],
                    ],
                ],
            ],
            [
                ########## DEPRECATED ##########
                'name' => [
                    'nl' => 'HR CV ketel',
                ],
                'short' => 'hr-boiler',
                'service_type' => [
                    'locale' => 'en',
                    'value' => 'Heating',
                ],
                'order' => 0,
                'info' => [
                    'nl' => 'Hier kunt u aangeven of er in de huidige situatie een cv ketel aanwezig is en hoe oud deze ongeveer is.',
                ],
                'service_values' => [
                    [
                        'value' => [
                            'nl' => 'Aanwezig, recent vervangen',
                        ],
                        'order' => 1,
                        'calculate_value' => 1,
                        'configurations' => [
                            'comfort' => 0,
                        ],
                    ],
                    [
                        'value' => [
                            'nl' => 'Aanwezig, tussen 6 en 13 jaar oud',
                        ],
                        'order' => 2,
                        'calculate_value' => 1,
                        'configurations' => [
                            'comfort' => 0,
                        ],
                    ],
                    [
                        'value' => [
                            'nl' => 'Aanwezig, ouder dan 13 jaar',
                        ],
                        'order' => 3,
                        'calculate_value' => 2,
                        'configurations' => [
                            'comfort' => 0,
                        ],
                    ],
                    [
                        'value' => [
                            'nl' => 'Niet aanwezig',
                        ],
                        'order' => 4,
                        'calculate_value' => 2,
                        'configurations' => [
                            'comfort' => 0,
                        ],
                    ],
                    [
                        'value' => [
                            'nl' => 'Onbekend',
                        ],
                        'order' => 2,
                        'calculate_value' => 5,
                        'configurations' => [
                            'comfort' => 0,
                        ],
                    ],
                ],
            ],
            [
                'name' => [
                    'nl' => 'Type ketel',
                ],
                'short' => 'boiler',
                'service_type' => [
                    'locale' => 'en',
                    'value' => 'Heating',
                ],
                'order' => 1,
                'info' => [
                    'nl' => 'Hier kunt u aangeven welk type ketel u heeft. Als u het niet weet kies dan HR107 ketel.',
                ],
                'service_values' => [
                    [
                        'value' => [
                            'nl' => 'conventioneel rendement ketel',
                        ],
                        'order' => 1,
                        'calculate_value' => 1,
                        'is_default' => false,
                        'configurations' => [
                            'comfort' => 0,
                        ],
                    ],
                    [
                        'value' => [
                            'nl' => 'verbeterd rendement ketel',
                        ],
                        'order' => 2,
                        'calculate_value' => 2,
                        'is_default' => false,
                        'configurations' => [
                            'comfort' => 0,
                        ],
                    ],
                    [
                        'value' => [
                            'nl' => 'HR100 ketel',
                        ],
                        'order' => 3,
                        'calculate_value' => 3,
                        'is_default' => false,
                        'configurations' => [
                            'comfort' => 1,
                        ],
                    ],
                    [
                        'value' => [
                            'nl' => 'HR104 ketel',
                        ],
                        'order' => 4,
                        'calculate_value' => 4,
                        'is_default' => false,
                        'configurations' => [
                            'comfort' => 2,
                        ],
                    ],
                    [
                        'value' => [
                            'nl' => 'HR107 ketel',
                        ],
                        'order' => 5,
                        'calculate_value' => 5,
                        'is_default' => true,
                        'configurations' => [
                            'comfort' => 3,
                        ],
                    ],
                ],
            ],
//            [
//                'name' => [
//                    'nl' => 'Douche wtw',
//                ],
//                'service_type' => [
//                    'locale' => 'en',
//                    'value' => 'Heating',
//                ],
//                'order' => 0,
//                'info' => [
//                    'nl' => 'Infotext hier',
//                ],
//                'service_values' => [
//                    [
//                        'value' => [
//                            'nl' => 'Geen',
//                        ],
//                        'order' => 1,
//                        'calculate_value' => 1,
//                        'configurations' => [
//                            'comfort' => 0,
//                        ],
//                    ],
//                    [
//                        'value' => [
//                            'nl' => 'Aanwezig',
//                        ],
//                        'order' => 2,
//                        'calculate_value' => 2,
//                    ],
//                ],
//            ],
            [
                'name' => [
                    'nl' => 'Hoe wordt het huis geventileerd?',
                ],
                'short' => 'house-ventilation',
                'service_type' => [
                    'locale' => 'en',
                    'value' => 'Ventilation',
                ],
                'order' => 5,
                'info' => [
                    'nl' => 'Dit veld dient alleen ervoor om de huidige situatie vast te leggen, er wordt niet mee gerekend. Meer informatie over ventilatie en aandachtspunten voor goed ventileren kunt u op de volgende pagina vinden.\n Natuurlijke ventilatie:\n De lucht in de woning wordt zonder behulp van ventilatoren ververst, bijvoorbeeld door ramen open te doen en/of ventilatieroosters. Een badkamer ventilator en de keukenafzuiging hoort daar niet bij. Mechanisch:\n De lucht wordt door een ventilator continu afgezogen, de lucht komt via roosters of open ramen naar binnen. De ventilatiebox zit vaak op zolder.\n Gebalanceerd:\n De lucht wordt mechanisch afgevoerd en mechanisch ingeblazen. Dit systeem is vaak in nieuwbouw woningen aanwezig.\n Decentraal mechanisch:\n De lucht wordt per kamer met een apart apparaat afgezogen en ingeblazen. De ventilatie-unit kan bijvoorbeeld geïntegreerd zijn in een radiator. Vraaggestuurd:\n Bij vraaggestuurde ventilatie gaat het altijd om een mechanisch systeem dat door luchtsensoren in de ruimtes gestuurd wordt. Bijvoorbeeld op basis van co2 of vochtgehalte.',
                ],
                'service_values' => [
                    [
                        'value' => [
                            'nl' => 'Natuurlijke ventilatie',
                        ],
                        'order' => 1,
                        'calculate_value' => 1,
                        'configurations' => [
                            'comfort' => 0,
                        ],
                    ],
                    [
                        'value' => [
                            'nl' => 'Mechanische ventilatie',
                        ],
                        'order' => 2,
                        'calculate_value' => 2,
                        'configurations' => [
                            'comfort' => 1,
                        ],
                    ],
                    [
                        'value' => [
                            'nl' => 'Gebalanceerde ventilatie',
                        ],
                        'order' => 3,
                        'calculate_value' => 3,
                        'configurations' => [
                            'comfort' => 3,
                        ],
                    ],
                    [
                        'value' => [
                            'nl' => 'Decentrale mechanische ventilatie',
                        ],
                        'order' => 4,
                        'calculate_value' => 4,
                        'configurations' => [
                            'comfort' => 3,
                        ],
                    ],
                ],
            ],
            [
                'name' => [
                    'nl' => 'Hoeveel zonnepanelen zijn er aanwezig',
                ],
                'short' => 'total-sun-panels',
                'service_type' => [
                    'locale' => 'en',
                    'value' => 'PV-wind',
                ],
                'order' => 3,
                'info' => [
                    'nl' => 'Voer hier het aantal zonnepanelen in dat in de huidige situatie geïnstalleerd is. Als u geen panelen hebt vul dan 0 in of laat het veld leeg.',
                ],
                'service_values' => [], // no values
            ],
            /*
            [
                'name' => [
                    'nl' => 'PV panelen',
                ],
                'short' => 'pv-panels',
                'service_type' => [
                    'locale' => 'en',
                    'value' => 'PV-wind',
                ],
                'order' => 0,
                'info' => [
                    'nl' => 'Infotext hier',
                ],
                'service_values' => [
                    // the peak powers
                    [
                        'value' => [
                            'nl' => '260'
                        ],
                        'order' => 0,
                        'calculate_value' => 260,
                    ],
                    [
                        'value' => [
                            'nl' => '265'
                        ],
                        'order' => 1,
                        'calculate_value' => 265,
                    ],
                    [
                        'value' => [
                            'nl' => '270'
                        ],
                        'order' => 2,
                        'calculate_value' => 270,
                    ],
                    [
                        'value' => [
                            'nl' => '275'
                        ],
                        'order' => 3,
                        'calculate_value' => 275,
                    ],
                    [
                        'value' => [
                            'nl' => '280'
                        ],
                        'order' => 4,
                        'calculate_value' => 280,
                    ],
                    [
                        'value' => [
                            'nl' => '285'
                        ],
                        'order' => 5,
                        'calculate_value' => 285,
                    ],
                    [
                        'value' => [
                            'nl' => '290'
                        ],
                        'order' => 6,
                        'calculate_value' => 290,
                    ],
                    [
                        'value' => [
                            'nl' => '295'
                        ],
                        'order' => 7,
                        'calculate_value' => 295,
                    ],
                    [
                        'value' => [
                            'nl' => '300'
                        ],
                        'order' => 8,
                        'calculate_value' => 300,
                    ],
                ],
            ],*/
        ];

        foreach ($services as $service) {
            $typeLocale = $service['service_type']['locale'];
            $typeValue = $service['service_type']['value'];

            // Get the service type
            $serviceType = DB::table('service_types')
                ->where("name->{$typeLocale}", $typeValue)
                ->first();

            if ($serviceType instanceof stdClass) {
                $dbService = DB::table('services')->where('short', $service['short'])->first();

                if (! $dbService instanceof stdClass) {
                    $serviceId = DB::table('services')->insertGetId([
                        'name' => json_encode($service['name']),
                        'short' => $service['short'],
                        'service_type_id' => $serviceType->id,
                        'order' => $service['order'],
                        'info' => json_encode($service['info']),
                    ]);
                } else {
                    $serviceId = $dbService->id;
                }

                foreach ($service['service_values'] as $serviceValue) {
                    DB::table('service_values')->updateOrInsert([
                        'service_id' => $serviceId,
                        'calculate_value' => ($serviceValue['calculate_value'] ?? null),
                        'order' => $serviceValue['order'],

                    ], [
                        'value' => json_encode($serviceValue['value']),
                        'configurations' => json_encode($serviceValue['configurations']),
                        'is_default' => $serviceValue['is_default'] ?? false,
                    ]);
                }
            }
        }
    }
}
