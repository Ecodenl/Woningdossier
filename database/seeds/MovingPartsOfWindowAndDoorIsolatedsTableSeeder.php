<?php

use Illuminate\Database\Seeder;

class MovingPartsOfWindowAndDoorIsolatedsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $qualities = [
            [
                'name' => 'Ja, in goede staat',
                'calculate_value' => '1'
            ],
            [
                'name' => 'Ja, in slecte staat',
                'calculate_value' => '2'
            ],
            [
                'name' => 'Nee',
                'calculate_value' => '3'
            ],
            [
                'name' => 'Onbekend',
                'calculate_value' => '4'
            ],
        ];

        foreach ($qualities as $quality) {
            \App\Models\MovingPartsOfWindowAndDoorIsolated::create($quality);
        }
    }
}
