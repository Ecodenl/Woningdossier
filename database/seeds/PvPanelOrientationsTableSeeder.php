<?php

use Illuminate\Database\Seeder;

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
                'names' => [
                    'nl' => 'noord-oost',
                ],
                'short' => 'n-o',
                'order' => 0,
            ],
            [
                'names' => [
                    'nl' => 'oost-noord-oost',
                ],
                'short' => 'o-n-o',
                'order' => 1,
            ],
            [
                'names' => [
                    'nl' => 'oost',
                ],
                'short' => 'o',
                'order' => 2,
            ],
            [
                'names' => [
                    'nl' => 'oost-zuid-oost',
                ],
                'short' => 'o-z-o',
                'order' => 3,
            ],
            [
                'names' => [
                    'nl' => 'zuid-oost',
                ],
                'short' => 'z-o',
                'order' => 4,
            ],
            [
                'names' => [
                    'nl' => 'zuid-zuid-oost',
                ],
                'short' => 'z-z-o',
                'order' => 5,
            ],
            [
                'names' => [
                    'nl' => 'zuid',
                ],
                'short' => 'z',
                'order' => 6,
            ],
            [
                'names' => [
                    'nl' => 'zuid-zuid-west',
                ],
                'short' => 'z-z-w',
                'order' => 7,
            ],
            [
                'names' => [
                    'nl' => 'zuid-west',
                ],
                'short' => 'z-w',
                'order' => 8,
            ],
            [
                'names' => [
                    'nl' => 'west-zuid-west',
                ],
                'short' => 'w-z-w',
                'order' => 9,
            ],
            [
                'names' => [
                    'nl' => 'west',
                ],
                'short' => 'w',
                'order' => 10,
            ],
            [
                'names' => [
                    'nl' => 'west-noord-west',
                ],
                'short' => 'w-n-w',
                'order' => 11,
            ],
            [
                'names' => [
                    'nl' => 'noord-west',
                ],
                'short' => 'n-w',
                'order' => 12,
            ],
        ];

        foreach ($statuses as $status) {
            $uuid = \App\Helpers\Str::uuid();
            foreach ($status['names'] as $locale => $name) {
                \DB::table('translations')->insert([
                    'key'         => $uuid,
                    'language'    => $locale,
                    'translation' => $name,
                ]);
            }

            \DB::table('pv_panel_orientations')->insert([
                'name' => $uuid,
                'short' => $status['short'],
                'order' => $status['order'],
            ]);
        }
    }
}
