<?php

namespace Database\Seeders;

use App\Models\InputSource;
use Illuminate\Database\Seeder;

class InputSourcesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $inputSources = [
            [
                'name' => 'Bewoner',
                'short' => InputSource::RESIDENT_SHORT,
                'order' => 1,
            ],
            [
                'name' => 'Voorbeeld woning',
                'short' => InputSource::EXAMPLE_BUILDING_SHORT,
                'order' => 3,
            ],
            [
                'name' => 'Coach',
                'short' => InputSource::COACH_SHORT,
                'order' => 2,
            ],
            [
                'name' => 'Coöperatie',
                'short' => InputSource::COOPERATION_SHORT,
                'order' => 4,
            ],
            [
                'name' => 'Master',
                'short' => InputSource::MASTER_SHORT,
                'order' => 5
            ],
            [
                'name' => 'Extern',
                'short' => InputSource::EXTERNAL_SHORT,
                'order' => 6
            ],
        ];

        foreach ($inputSources as $inputSource) {
            \App\Models\InputSource::updateOrCreate(['short' => $inputSource['short']], $inputSource);
        }
    }
}
