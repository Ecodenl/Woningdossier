<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CooperationMeasureApplicationsTableSeeder extends Seeder
{
    const MEASURES = [
        [
            'name' => ['nl' => 'Keuken'],
            'info' => ['nl' => 'Keuken'],
            'costs' => ['from' => 5000, 'to' => 10000],
            'savings_money' => 0,
            'extra' => ['icon' => 'icon-kitchen'],
            'is_extensive_measure' => false,
            'is_deletable' => true,
        ],
        [
            'name' => ['nl' => 'Badkamer'],
            'info' => ['nl' => 'Badkamer'],
            'costs' => ['from' => 5000, 'to' => 15000],
            'savings_money' => 0,
            'extra' => ['icon' => 'icon-bathroom'],
            'is_extensive_measure' => false,
            'is_deletable' => true,
        ],
        [
            'name' => ['nl' => 'Dakkapel'],
            'info' => ['nl' => 'Dakkapel'],
            'costs' => ['from' => 8000, 'to' => 18000],
            'savings_money' => 0,
            'extra' => ['icon' => 'icon-dormer'],
            'is_extensive_measure' => false,
            'is_deletable' => true,
        ],
        [
            'name' => ['nl' => 'Kozijnen'],
            'info' => ['nl' => 'Kozijnen'],
            'costs' => ['from' => 2500, 'to' => 2800],
            'savings_money' => 0,
            'extra' => ['icon' => 'icon-window-frame'],
            'is_extensive_measure' => false,
            'is_deletable' => true,
        ],
        [
            'name' => ['nl' => 'Schilderwerk'],
            'info' => ['nl' => 'Schilderwerk'],
            'costs' => ['from' => 0, 'to' => 0],
            'savings_money' => 0,
            'extra' => ['icon' => 'icon-paint-job'],
            'is_extensive_measure' => false,
            'is_deletable' => true,
        ],
        [
            'name' => ['nl' => 'Serre'],
            'info' => ['nl' => 'Serre'],
            'costs' => ['from' => 7000, 'to' => 24000],
            'savings_money' => 0,
            'extra' => ['icon' => 'icon-sunroom'],
            'is_extensive_measure' => false,
            'is_deletable' => true,
        ],
        [
            'name' => ['nl' => 'Kamer op zolder'],
            'info' => ['nl' => 'Kamer op zolder'],
            'costs' => ['from' => 500, 'to' => 2500],
            'savings_money' => 0,
            'extra' => ['icon' => 'icon-attic-room'],
            'is_extensive_measure' => false,
            'is_deletable' => true,
        ],
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $cooperations = \App\Models\Cooperation::cursor();
        foreach ($cooperations as $cooperation) {
            $cooperation->cooperationMeasureApplications()->createMany(static::MEASURES);
        }
    }
}
