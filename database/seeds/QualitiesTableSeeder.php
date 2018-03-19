<?php

use Illuminate\Database\Seeder;
use App\Models\Quality;

class QualitiesTableSeeder extends Seeder
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
                'name' => 'Onbekend',
                'calculate_value' => 1,
            ],
            [
                'name' => 'Geen isolatie',
                'calculate_value' => 2,
            ],
            [
                'name' => 'Matige isolatie (tot 8 cm isolatie)',
                'calculate_value' => 3,
            ],
            [
                'name' => 'Goede isolatie (8 tot 20 cm isolatie)',
                'calculate_value' => 4,
            ],
            [
                'name' => 'Zeer goede isolatie (meer dan 20 cm isolatie)',
                'calculate_value' => 5,
            ],
            [
                'name' => 'Niet van toepassing',
                'calculate_value' => 6,
            ],
        ];
        foreach ($qualities as $quality) {
            Quality::create([
                'name' => $quality['name'],
                'calculate_value' => $quality['calculate_value']
            ]);
        }
    }
}
