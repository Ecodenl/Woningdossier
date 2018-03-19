<?php

use Illuminate\Database\Seeder;
use App\Models\Motivation;

class MotivationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $motivations = [
            [
                'name' => 'Comfortverbetering',
                'calculate_value' => null,
            ],
            [
                'name' => 'Iets voor het milieu doen',
                'calculate_value' => null,
            ],
            [
                'name' => 'Verlaging van de maandlasten',
                'calculate_value' => null,
            ],
            [
                'name' => 'Goed rendement op de investering',
                'calculate_value' => null,
            ],
        ];

        foreach ($motivations as $motivation) {
            Motivation::create([
                'name' => $motivation['name'],
                'calculate_value' => $motivation['calculate_value'],
            ]);
        }
    }
}
