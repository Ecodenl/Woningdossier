<?php

use App\Models\ExperienceAirQualityInHome;
use Illuminate\Database\Seeder;

class ExperienceAirQualityInHomesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $airQualities = [
            'Goed',
            'Matig',
            'Slecht',
            'Geen mening',
            'Anders: uitleg',
        ];

        foreach ($airQualities as $airQuality) {
            ExperienceAirQualityInHome::create([
                'name' => $airQuality,
            ]);
        }
    }
}
