<?php

namespace Database\Seeders;

use App\Models\ExperienceAirQualityInHome;
use Illuminate\Database\Seeder;

class ExperienceAirQualityInHomesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
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
