<?php

use Illuminate\Database\Seeder;
use App\Models\PresentShowerWtw;

class PresentShowerWtwsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $presentShowerWtws = [

            [
                'name' => 'Geen',
                'calculated_value' => 1,
            ],
            [
                'name' => 'Aanwezig',
                'calculated_value' => 2,
            ],
        ];

        foreach ($presentShowerWtws as $presentShowerWtw) {
            PresentShowerWtw::create([
                'name' => $presentShowerWtw['name'],
                'calculated_value' => $presentShowerWtw['calculated_value'],
            ]);
        }
    }
}
