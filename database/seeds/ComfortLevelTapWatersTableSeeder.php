<?php

use Illuminate\Database\Seeder;

class ComfortLevelTapWatersTableSeeder extends Seeder
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
                    'nl' => 'Standaard',
                ],
                'calculate_value' => 1,
                'order' => 0,
            ],
            [
                'names' => [
                    'nl' => 'Comfort',
                ],
                'calculate_value' => 2,
                'order' => 1,
            ],
            [
                'names' => [
                    'nl' => 'Comfort plus',
                ],
                'calculate_value' => 3,
                'order' => 2,
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

            \DB::table('comfort_level_tap_waters')->insert([
                'name' => $uuid,
                'calculate_value' => $status['calculate_value'],
                'order' => $status['order'],
            ]);
        }
    }
}
