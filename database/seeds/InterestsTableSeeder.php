<?php

use Illuminate\Database\Seeder;

class InterestsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $interests = [
            [
                'names' => [
                    'nl' => 'Ja, op korte termijn',
                ],
                'calculate_value' => 1,
                'order' => 0,
            ],
            [
                'names' => [
                    'nl' => 'Ja, op termijn',
                ],
                'calculate_value' => 2,
                'order' => 1,
            ],
            [
                'names' => [
                    'nl' => 'Meer informatie gewenst',
                ],
                'calculate_value' => 3,
                'order' => 2,
            ],
            [
                'names' => [
                    'nl' => 'Geen actie',
                ],
                'calculate_value' => 4,
                'order' => 3,
            ],
            [
                'names' => [
                    'nl' => 'Niet mogelijk',
                ],
                'calculate_value' => 5,
                'order' => 4,
            ],
        ];

        foreach ($interests as $interest) {
            $uuid = \App\Helpers\Str::uuid();
            foreach ($interest['names'] as $locale => $name) {
                \DB::table('translations')->insert([
                    'key'         => $uuid,
                    'language'    => $locale,
                    'translation' => $name,
                ]);
            }

            \DB::table('interests')->insert([
                'name' => $uuid,
                'calculate_value' => $interest['calculate_value'],
                'order' => $interest['order'],
            ]);
        }
    }
}
