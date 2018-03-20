<?php

use Illuminate\Database\Seeder;

class VentilationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $ventilations = [
            [
                'name' => 'Natuurlijk',
                'calculate_value' => 1,
            ],
            [
                'name' => 'Mechanisch',
                'calculate_value' => 2,
            ],
            [
                'name' => 'Gebalanceerd',
                'calculate_value' => 3,
            ],
            [
                'name' => 'Decentraal mechanisch',
                'calculate_value' => 4,
            ],
            [
                'name' => 'Vraaggestuurd',
                'calculate_value' => 5,
            ],
        ];

        foreach ($ventilations as $ventilation) {
            \App\Models\Ventilation::create([
               'name' => $ventilation['name'],
               'calculate_value' => $ventilation['calculate_value'],
            ]);
        }
    }
}
