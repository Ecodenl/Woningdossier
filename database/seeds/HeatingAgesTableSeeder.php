<?php

use Illuminate\Database\Seeder;

class HeatingAgesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $heatingAges = [
            [
                'name' => 'Aanwezig, recent vervangen',
                'calculate_value' => 1,
            ],
            [
                'name' => 'Aaanwezig, tussen 6 en 13 jaar oud',
                'calculate_value' => 1,
            ],
            [
                'name' => 'Aanwezig, ouder dan 13 jaar',
                'calculate_value' => 2,
            ],
            [
                'name' => 'Niet aanwezig',
                'calculate_value' => 2,
            ],
            [
                'name' => 'Onbekend',
                'calculate_value' => 2,
            ],
        ];

        foreach ($heatingAges as $heatingAge) {
            DB::table('central_heating_ages')->insert($heatingAge);
        }
    }
}
