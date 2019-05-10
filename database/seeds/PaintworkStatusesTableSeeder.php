<?php

use Illuminate\Database\Seeder;

class PaintworkStatusesTableSeeder extends Seeder
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
                    'nl' => 'Nee',
                ],
                'calculate_value' => 7,
                'order' => 0,
            ],
            [
                'names' => [
                    'nl' => 'Een beetje',
                ],
                'calculate_value' => 3,
                'order' => 1,
            ],
            [
                'names' => [
                    'nl' => 'Ja',
                ],
                'calculate_value' => 1,
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

            \DB::table('paintwork_statuses')->insert([
                'name' => $uuid,
                'calculate_value' => $status['calculate_value'],
                'order' => $status['order'],
            ]);
        }
    }
}
