<?php

use Illuminate\Database\Seeder;
use App\Models\InterestedToExecuteMeasure;

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
                'name' => 'Geen actie',
                'calculated_value' => 4,
            ],
            [
                'name' => 'Niet mogelijk',
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
