<?php

use Illuminate\Database\Seeder;

class InputSourcesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $inputSources = [
            [
                'name' => 'Bewoner',
                'short' => 'resident',
                'order' => 1,
            ],
            [
                'name' => 'Voorbeeld woning',
                'short' => 'example-building',
                'order' => 3,
            ],
            [
                'name' => 'Coach',
                'short' => 'coach',
                'order' => 2,
            ],
            [
                'name' => 'Coöperatie',
                'short' => 'cooperation',
                'order' => 4,
            ],
            [
                'name' => 'Master',
                'short' => 'master',
                'order' => 5
            ]
        ];

        foreach ($inputSources as $inputSource) {
            \App\Models\InputSource::updateOrCreate(['short' => $inputSource['short']], $inputSource);
        }
    }
}
