<?php

use App\Models\Alert;
use App\Models\SubStep;
use App\Helpers\Conditions\Clause;
use Illuminate\Database\Seeder;

class AlertsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $alerts = [
            [
                'short' => 'suboptimal-isolation-for-heat-pump',
                'text' => [
                    'nl' => 'Jouw huis is niet optimaal geïsoleerd. Het is aan te raden om  eerst de isolatie te verbeteren. Het is namelijk mogelijk dat de warmtepomp in de huidige situatie niet optimaal werkt.',
                ],
                'type' => Alert::TYPE_WARNING,
                'conditions' => [
                    [
                        [
                            'column' => [
                                'slug->nl' => 'warmtepomp-interesse',
                            ],
                            'operator' => Clause::PASSES,
                            'value' => SubStep::class,
                        ],
                        [
                            'column' => 'interested-in-heat-pump',
                            'operator' => Clause::EQ,
                            'value' => 'yes',
                        ],
                        [
                            'column' => 'fn',
                            'operator' => 'InsulationScore',
                        ],
                    ]
                ],
            ],
            [
                'short' => 'research-heat-pump',
                'text' => [
                    'nl' => 'Er is aanvullend onderzoek nodig of het mogelijk is om een (hybride) warmtepomp te installeren.',
                ],
                'type' => Alert::TYPE_INFO,
                'conditions' => [
                    [
                        [
                            'column' => 'heat-source-considerable',
                            'operator' => Clause::CONTAINS,
                            'value' => 'heat-pump',
                        ],
                        [
                            'column' => 'outside-unit-space',
                            'operator' => Clause::EQ,
                            'value' => 'no',
                        ],
                        [
                            'column' => 'inside-unit-space',
                            'operator' => Clause::EQ,
                            'value' => 'no',
                        ],
                    ],
                ],
            ],
            [
                'short' => 'too-high-temp-for-heat-pump',
                'text' => [
                    'nl' => 'Je stookt met hoge cv-temperatuur. Hierdoor kan een (hybride) warmtepomp niet optimaal werken. Zorg eerst voor lage temperatuurverwarming.',
                ],
                'type' => Alert::TYPE_WARNING,
                'conditions' => [
                    [
                        [
                            'column' => [
                                'slug->nl' => 'warmtepomp-interesse',
                            ],
                            'operator' => Clause::PASSES,
                            'value' => SubStep::class,
                        ],
                        [
                            'column' => 'interested-in-heat-pump',
                            'operator' => Clause::EQ,
                            'value' => 'yes',
                        ],
                        [
                            'column' => 'boiler-setting-comfort-heat',
                            'operator' => Clause::EQ,
                            'value' => 'temp-high',
                        ],
                    ],
                ],
            ],
            [
                'short' => 'insulation-advice-current-living-rooms-windows',
                'text' => [
                    'nl' => 'Jouw huis is niet optimaal geïsoleerd. Er wordt geadviseerd eerst de ramen van de leefruimtes te verbeteren.',
                ],
                'type' => Alert::TYPE_WARNING,
                'conditions' => [
                    [
                        [
                            'column' => [
                                'slug->nl' => 'warmtepomp-interesse',
                            ],
                            'operator' => Clause::PASSES,
                            'value' => SubStep::class,
                        ],
                        [
                            'column' => 'interested-in-heat-pump',
                            'operator' => Clause::EQ,
                            'value' => 'yes',
                        ],
                        [
                            'column' => 'fn',
                            'operator' => 'InsulationAdvice',
                            'value' => 'current-living-rooms-windows',
                        ],
                    ]
                ],
            ],
            [
                'short' => 'insulation-advice-current-sleeping-rooms-windows',
                'text' => [
                    'nl' => 'Jouw huis is niet optimaal geïsoleerd. Het wordt geadviseerd eerst de ramen van de slaapruimtes te verbeteren.',
                ],
                'type' => Alert::TYPE_WARNING,
                'conditions' => [
                    [
                        [
                            'column' => [
                                'slug->nl' => 'warmtepomp-interesse',
                            ],
                            'operator' => Clause::PASSES,
                            'value' => SubStep::class,
                        ],
                        [
                            'column' => 'interested-in-heat-pump',
                            'operator' => Clause::EQ,
                            'value' => 'yes',
                        ],
                        [
                            'column' => 'fn',
                            'operator' => 'InsulationAdvice',
                            'value' => 'current-sleeping-rooms-windows',
                        ],
                    ]
                ],
            ],
            [
                'short' => 'insulation-advice-current-wall-insulation',
                'text' => [
                    'nl' => 'Jouw huis is niet optimaal geïsoleerd. Er wordt geadviseerd eerst de wallisolatie te verbeteren.',
                ],
                'type' => Alert::TYPE_WARNING,
                'conditions' => [
                    [
                        [
                            'column' => [
                                'slug->nl' => 'warmtepomp-interesse',
                            ],
                            'operator' => Clause::PASSES,
                            'value' => SubStep::class,
                        ],
                        [
                            'column' => 'interested-in-heat-pump',
                            'operator' => Clause::EQ,
                            'value' => 'yes',
                        ],
                        [
                            'column' => 'fn',
                            'operator' => 'InsulationAdvice',
                            'value' => 'current-wall-insulation',
                        ],
                    ]
                ],
            ],
            [
                'short' => 'insulation-advice-current-floor-insulation',
                'text' => [
                    'nl' => 'Jouw huis is niet optimaal geïsoleerd. Er wordt geadviseerd om eerst de vloerisolatie te verbeteren.',
                ],
                'type' => Alert::TYPE_WARNING,
                'conditions' => [
                    [
                        [
                            'column' => [
                                'slug->nl' => 'warmtepomp-interesse',
                            ],
                            'operator' => Clause::PASSES,
                            'value' => SubStep::class,
                        ],
                        [
                            'column' => 'interested-in-heat-pump',
                            'operator' => Clause::EQ,
                            'value' => 'yes',
                        ],
                        [
                            'column' => 'fn',
                            'operator' => 'InsulationAdvice',
                            'value' => 'current-floor-insulation',
                        ],
                    ]
                ],
            ],
            [
                'short' => 'insulation-advice-current-roof-insulation',
                'text' => [
                    'nl' => 'Jouw huis is niet optimaal geïsoleerd. Er wordt geadviseerd om eerst de dakisolatie te verbeteren.',
                ],
                'type' => Alert::TYPE_WARNING,
                'conditions' => [
                    [
                        [
                            'column' => [
                                'slug->nl' => 'warmtepomp-interesse',
                            ],
                            'operator' => Clause::PASSES,
                            'value' => SubStep::class,
                        ],
                        [
                            'column' => 'interested-in-heat-pump',
                            'operator' => Clause::EQ,
                            'value' => 'yes',
                        ],
                        [
                            'column' => 'fn',
                            'operator' => 'InsulationAdvice',
                            'value' => 'current-roof-insulation',
                        ],
                    ]
                ],
            ],
        ];

        foreach ($alerts as $alert) {
            $alert['conditions'] = json_encode($alert['conditions']);
            $alert['text'] = json_encode($alert['text']);
            DB::table('alerts')->updateOrInsert(['short' => $alert['short']], $alert);
        }
    }
}