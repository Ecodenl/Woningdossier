<?php

use Illuminate\Database\Seeder;
use App\Models\CentralHeatingAge;

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
                'name' => 'Aanwezig, recent vervange',
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
            CentralHeatingAge::create([
                'name' => $heatingAge['name'],
                'calculate_value' => $heatingAge['calculate_value'],
            ]);
        }
    }
}
