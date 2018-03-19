<?php

use Illuminate\Database\Seeder;
use App\Models\ExperienceAirQualityInHome;

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
            'Anders: uitleg'
        ];

        foreach ($airQualities as $airQuality) {
            ExperienceAirQualityInHome::create([
                'name' => $airQuality,
            ]);
        }
    }
}
