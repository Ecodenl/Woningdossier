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
        [
            'name' => ['nl' => 'Gevelisolatie'],
            'info' => ['nl' => 'Gevelisolatie'],
            'costs' => ['from' => 0, 'to' => 0],
            'savings_money' => 0,
            'extra' => ['icon' => 'icon-wall-insulation-good'],
            'is_extensive_measure' => true,
            'is_deletable' => false,
        ],
        [
            'name' => ['nl' => 'Vloerisolatie'],
            'info' => ['nl' => 'Vloerisolatie'],
            'costs' => ['from' => 0, 'to' => 0],
            'savings_money' => 0,
            'extra' => ['icon' => 'icon-floor-insulation-good'],
            'is_extensive_measure' => true,
            'is_deletable' => false,
        ],
        [
            'name' => ['nl' => 'Dakisolatie'],
            'info' => ['nl' => 'Dakisolatie'],
            'costs' => ['from' => 0, 'to' => 0],
            'savings_money' => 0,
            'extra' => ['icon' => 'icon-roof-insulation-good'],
            'is_extensive_measure' => true,
            'is_deletable' => false,
        ],
        [
            'name' => ['nl' => 'Isolatieglas'],
            'info' => ['nl' => 'Isolatieglas'],
            'costs' => ['from' => 0, 'to' => 0],
            'savings_money' => 0,
            'extra' => ['icon' => 'icon-glass-hr'],
            'is_extensive_measure' => true,
            'is_deletable' => false,
        ],
        [
            'name' => ['nl' => 'Warmtepomp'],
            'info' => ['nl' => 'Warmtepomp'],
            'costs' => ['from' => 0, 'to' => 0],
            'savings_money' => 0,
            'extra' => ['icon' => 'icon-heat-pump'],
            'is_extensive_measure' => true,
            'is_deletable' => false,
        ],
        [
            'name' => ['nl' => 'Vloerverwarming'],
            'info' => ['nl' => 'Vloerverwarming'],
            'costs' => ['from' => 0, 'to' => 0],
            'savings_money' => 0,
            'extra' => ['icon' => 'icon-floor-heating'],
            'is_extensive_measure' => true,
            'is_deletable' => false,
        ],
        [
            'name' => ['nl' => 'Zonneboiler'],
            'info' => ['nl' => 'Zonneboiler'],
            'costs' => ['from' => 0, 'to' => 0],
            'savings_money' => 0,
            'extra' => ['icon' => 'icon-sun-boiler-hot-water'],
            'is_extensive_measure' => true,
            'is_deletable' => false,
        ],
        [
            'name' => ['nl' => 'Zonnepanelen'],
            'info' => ['nl' => 'Zonnepanelen'],
            'costs' => ['from' => 0, 'to' => 0],
            'savings_money' => 0,
            'extra' => ['icon' => 'icon-solar-panels'],
            'is_extensive_measure' => true,
            'is_deletable' => false,
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
