<?php

namespace Database\Seeders;

use App\Models\InterestedToExecuteMeasure;
use Illuminate\Database\Seeder;

class InterestedToExecuteMeasuresTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $interestedToExecuteMeasures = [
            [
                'name' => 'Ja, op korte termijn',
                'calculated_value' => 1,
            ],
            [
                'name' => 'Ja, op termijn',
                'calculated_value' => 2,
            ],
            [
                'name' => 'Meer informatie gewenst',
                'calculated_value' => 3,
            ],
            [
                'name' => 'Nee, geen interesse',
                'calculated_value' => 4,
            ],
            [
                'name' => 'Nee, niet mogelijk',
                'calculated_value' => 5,
            ],
        ];

        foreach ($interestedToExecuteMeasures as $interestedToExecuteMeasure) {
            InterestedToExecuteMeasure::create([
                'name' => $interestedToExecuteMeasure['name'],
                'calculate_value' => $interestedToExecuteMeasure['calculated_value'],
            ]);
        }
    }
}
