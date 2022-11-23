<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

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
                'name' => [
                    'nl' => 'Gas',
                ],
                'percentage' => 5.00,
            ],
            [
                'short' => 'electricity',
                'name' => [
                    'nl' => 'Elektra',
                ],
                'percentage' => 2.00,
            ],
            [
                'short' => 'common',
                'name' => [
                    'nl' => 'Algemeen',
                ],
                'percentage' => 2.00,
            ],
        ];

        foreach ($indexes as $index) {
            DB::table('price_indexings')->insert([
                'name' => json_encode($index['name']),
                'short' => $index['short'],
                'percentage' => $index['percentage'],
            ]);
        }
    }
}
