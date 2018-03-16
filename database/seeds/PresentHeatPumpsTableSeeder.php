<?php

use Illuminate\Database\Seeder;
use App\Models\PresentHeatPump;

class PresentHeatPumpsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $presentHeatPumps = [
            [
                'name' => 'Geen',
                'calculated_value' => 1,
            ],
            [
                'name' => 'Hybride warmtepomp met buitenlucht als warmtebron',
                'calculated_value' => 2
            ],
            [
                'name' => 'Volledige warmtepomp met buitenlucht als warmtebron',
                'calculated_value' => 2
            ],
            [
                'name' => 'Volledige warmtepomp met bodemenergie als warmtebron',
                'calculated_value' => 3
            ],
        ];

        foreach ($presentHeatPumps as $presentHeatPump) {
            PresentHeatPump::create([
                'name' => $presentHeatPump['name'],
                'calculated_value' => $presentHeatPump['calculated_value']
            ]);
        }
    }
}
