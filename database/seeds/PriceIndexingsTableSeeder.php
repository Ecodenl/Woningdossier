<?php

use Illuminate\Database\Seeder;

class PriceIndexingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $indexes = [
            [
                'short' => 'gas',
                'names' => [
                    'nl' => 'Gas',
                ],
                'percentage' => 5.00,
            ],
            [
                'short' => 'electricity',
                'names' => [
                    'nl' => 'Elektra',
                ],
                'percentage' => 2.00,
            ],
            [
                'short' => 'common',
                'names' => [
                    'nl' => 'Algemeen',
                ],
                'percentage' => 2.00,
            ],
        ];

        foreach ($indexes as $index) {
            $uuid = \App\Helpers\Str::uuid();

            foreach ($index['names'] as $locale => $name) {
                \DB::table('translations')->insert([
                    'key'         => $uuid,
                    'language'    => $locale,
                    'translation' => $name,
                ]);
            }

            \DB::table('price_indexings')->insert([
                'short' => $index['short'],
                'name' => $uuid,
                'percentage' => $index['percentage'],
            ]);
        }
    }
}
