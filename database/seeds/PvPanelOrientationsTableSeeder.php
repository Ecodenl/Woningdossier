<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PvPanelOrientationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $statuses = [
            [
                'name' => [
                    'nl' => 'noord-oost',
                ],
                'short' => 'n-o',
                'order' => 0,
            ],
            [
                'name' => [
                    'nl' => 'oost-noord-oost',
                ],
                'short' => 'o-n-o',
                'order' => 1,
            ],
            [
                'name' => [
                    'nl' => 'oost',
                ],
                'short' => 'o',
                'order' => 2,
            ],
            [
                'name' => [
                    'nl' => 'oost-zuid-oost',
                ],
                'short' => 'o-z-o',
                'order' => 3,
            ],
            [
                'name' => [
                    'nl' => 'zuid-oost',
                ],
                'short' => 'z-o',
                'order' => 4,
            ],
            [
                'name' => [
                    'nl' => 'zuid-zuid-oost',
                ],
                'short' => 'z-z-o',
                'order' => 5,
            ],
            [
                'name' => [
                    'nl' => 'zuid',
                ],
                'short' => 'z',
                'order' => 6,
            ],
            [
                'name' => [
                    'nl' => 'zuid-zuid-west',
                ],
                'short' => 'z-z-w',
                'order' => 7,
            ],
            [
                'name' => [
                    'nl' => 'zuid-west',
                ],
                'short' => 'z-w',
                'order' => 8,
            ],
            [
                'name' => [
                    'nl' => 'west-zuid-west',
                ],
                'short' => 'w-z-w',
                'order' => 9,
            ],
            [
                'name' => [
                    'nl' => 'west',
                ],
                'short' => 'w',
                'order' => 10,
            ],
            [
                'name' => [
                    'nl' => 'west-noord-west',
                ],
                'short' => 'w-n-w',
                'order' => 11,
            ],
            [
                'name' => [
                    'nl' => 'noord-west',
                ],
                'short' => 'n-w',
                'order' => 12,
            ],
        ];

        foreach ($statuses as $status) {
            DB::table('pv_panel_orientations')->insert([
                'name' => json_encode($status['name']),
                'short' => $status['short'],
                'order' => $status['order'],
            ]);
        }
    }
}
