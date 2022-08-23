<?php

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
                'short' => 'heat-source',

                'conditions' => [
                    [
                        [
                            'column' => 'heat-source',
                            'operator' => \App\Helpers\Conditions\Clause::CONTAINS,
                            'value' => 'hr-boiler',
                        ],
                    ],
                ],
                'text' => [
                    'nl' => 'Wartmebron is een HR-boiler',
                ],
                'type' => 'warning',
            ],
            [
                'short' => '50-degree-heat-source',
                'conditions' =>[
                    [
                        [
                            'column' => 'heat-source',
                            'operator' => \App\Helpers\Conditions\Clause::EQ,
                            'value' => 'heat-pump',
                        ],
                        [
                            'column' => 'fifty-degree-test',
                            'operator' => \App\Helpers\Conditions\Clause::EQ,
                            'value' => 'no',
                        ],
                    ],
                ],
                'text' => [
                    'nl' => '50 graden test is niet geslaagd, in combinatie met een warmtepomp is dit een koud huis.',
                ],
                'type' => 'danger',
            ],
            [
                'short' => 'resident-count',
                'conditions' => [
                    [
                        [
                            'column' => 'resident-count',
                            'operator' => \App\Helpers\Conditions\Clause::GT,
                            'value' => '2',
                        ],
                    ],
                ],
                'text' => [
                    'nl' => 'U heeft meer dan 2 personen in uw huis',
                ],
                'type' => 'danger',
            ],
        ];

        foreach ($alerts as $alert) {
            $alert['conditions'] = json_encode($alert['conditions']);
            $alert['text'] = json_encode($alert['text']);
            DB::table('alerts')->updateOrInsert(['short' => $alert['short']], $alert);
        }
    }
}
