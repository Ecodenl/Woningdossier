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
                'order' => 2,
            ],
            [
                'name' => 'Coach',
                'short' => 'coach',
                'order' => 3,
            ],
            [
                'name' => 'Coöperatie',
                'short' => 'cooperation',
                'order' => 4,
            ],
        ];

        foreach ($inputSources as $inputSource) {
            \App\Models\InputSource::firstOrCreate($inputSource);
        }
    }
}
