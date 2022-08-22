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
                'conditions' =>
                    [
                        [
                            'column' => 'heat-source',
                            'operator' => \App\Helpers\Conditions\Clause::CONTAINS,
                            'value' => 'hr-boiler',
                        ],
                    ],
                'text' => [
                    'nl' => 'Wartmebron is een HR-boiler',
                ],
                'type' => 'warning',
            ],
            [
                'conditions' =>
                    [
                        [
                            'column' => 'heat-source',
                            'operator' => \App\Helpers\Conditions\Clause::EQ,
                            'value' => 'hr-boiler',
                        ],
                        [
                            'column' => 'heat-source',
                            'operator' => \App\Helpers\Conditions\Clause::EQ,
                            'value' => 'heat-pump',
                        ],
                    ],
                'text' => [
                    'nl' => '50 graden test is niet geslaagd, in combinatie met een warmtepomp is dit een koud huis.',
                ],
                'type' => 'danger',
            ],
            [
                'conditions' =>
                    [
                        [
                            'column' => 'resident-count',
                            'operator' => \App\Helpers\Conditions\Clause::GT,
                            'value' => '2',
                        ],
                    ],
                'text' => [
                    'nl' => 'U heeft meer dan 2 personen in uw huis',
                ],
                'type' => 'danger',
            ],
        ];
    }
}
