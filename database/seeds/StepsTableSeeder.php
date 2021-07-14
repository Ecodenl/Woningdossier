<?php

use Illuminate\Database\Seeder;

class StepsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $steps = [
            [
                'slug' => 'building-data',
                'short' => 'building-data',
                'name' => [
                    'en' => 'Building data',
                    'nl' => 'Woninggegevens',
                ],
                'order' => 1,
            ],
            [
                'slug' => 'usage',
                'short' => 'usage',
                'name' => [
                    'en' => 'Usage',
                    'nl' => 'Gebruik',
                ],
                'order' => 2,
            ],
            [
                'slug' => 'living-requirements',
                'short' => 'living-requirements',
                'name' => [
                    'en' => 'Living requirements',
                    'nl' => 'Woonwensen',
                ],
                'order' => 3,
            ],
            [
                'slug' => 'residential-status',
                'short' => 'residential-status',
                'name' => [
                    'en' => 'Residential status',
                    'nl' => 'Woonstatus',
                ],
                'order' => 4,
            ],
        ];

        foreach ($steps as $step) {

            if (! DB::table('steps')->where('slug', $step['slug'])->first() instanceof stdClass) {
                $insertStepData = [
                    'slug' => $step['slug'],
                    'short' => $step['short'],
                    'name' => json_encode($step['name']),
                    'order' => $step['order'],
                ];

                \DB::table('steps')->insert($insertStepData);
            }
        }

        $allCooperations = \App\Models\Cooperation::all();

        $steps = \App\Models\Step::all();

        foreach ($allCooperations as $cooperation) {
            foreach ($steps as $step) {
                $cooperation->steps()->attach($step);
                $cooperation->steps()->updateExistingPivot($step->id, ['order' => $step->order]);
            }
        }
    }
}
