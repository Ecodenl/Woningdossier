<?php

use Illuminate\Database\Seeder;

class CooperationMeasureApplicationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $measures = [
            [
                'name' => ['nl' => 'Keuken'],
                'info' => ['nl' => 'Keuken'],
                'costs' => ['from' => 5000, 'to' => 10000],
                'savings_money' => 300,
                'extra' => ['icon' => 'icon-kitchen'],
            ],
            [
                'name' => ['nl' => 'Badkamer'],
                'info' => ['nl' => 'Badkamer'],
                'costs' => ['from' => 5000, 'to' => 15000],
                'savings_money' => 5000,
                'extra' => ['icon' => 'icon-bathroom'],
            ],
            [
                'name' => ['nl' => 'Dakkapel'],
                'info' => ['nl' => 'Dakkapel'],
                'costs' => ['from' => 8000, 'to' => 18000],
                'savings_money' => 0,
                'extra' => ['icon' => 'icon-dormer'],
            ],
            [
                'name' => ['nl' => 'Kozijnen'],
                'info' => ['nl' => 'Kozijnen'],
                'costs' => ['from' => 2500, 'to' => 2800],
                'savings_money' => 1400,
                'extra' => ['icon' => 'icon-window-frame'],
            ],
            [
                'name' => ['nl' => 'Schilderwerk'],
                'info' => ['nl' => 'Schilderwerk'],
                'costs' => ['from' => 0, 'to' => 0],
                'savings_money' => 1000,
                'extra' => ['icon' => 'icon-paint-job'],
            ],
            [
                'name' => ['nl' => 'Serre'],
                'info' => ['nl' => 'Serre'],
                'costs' => ['from' => 7000, 'to' => 24000],
                'savings_money' => 1000,
                'extra' => ['icon' => 'icon-sunroom'],
            ],
            [
                'name' => ['nl' => 'Kamer op zolder'],
                'info' => ['nl' => 'Kamer op zolder'],
                'costs' => ['from' => 500, 'to' => 2500],
                'savings_money' => 300,
                'extra' => ['icon' => 'icon-attic-room'],
            ],
        ];

        $cooperations = \App\Models\Cooperation::cursor();
        foreach ($cooperations as $cooperation) {
            $cooperation->cooperationMeasureApplications()->createMany($measures);
        }
    }
}
