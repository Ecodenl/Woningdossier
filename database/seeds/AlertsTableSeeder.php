<?php

use App\Models\Alert;
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
                            'column' => 'fn',
                            'value' => 'InsulationCalculation',
                        ],
                    ],
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
                            'column' => 'boiler-setting-comfort-heat',
                            'operator' => Clause::EQ,
                            'value' => 'temp-high',
                        ],
                    ],
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
