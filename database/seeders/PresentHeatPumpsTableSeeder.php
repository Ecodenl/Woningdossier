<?php

namespace Database\Seeders;

use App\Models\PresentHeatPump;
use Illuminate\Database\Seeder;

class PresentHeatPumpsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $presentHeatPumps = [
            [
                'name' => 'Geen',
                'calculate_value' => 1,
            ],
            [
                'name' => 'Hybride warmtepomp met buitenlucht als warmtebron',
                'calculate_value' => 2,
            ],
            [
                'name' => 'Volledige warmtepomp met buitenlucht als warmtebron',
                'calculate_value' => 2,
            ],
            [
                'name' => 'Volledige warmtepomp met bodemenergie als warmtebron',
                'calculate_value' => 3,
            ],
        ];

        foreach ($presentHeatPumps as $presentHeatPump) {
            PresentHeatPump::create([
                'name' => $presentHeatPump['name'],
                'calculate_value' => $presentHeatPump['calculate_value'],
            ]);
        }
    }
}
