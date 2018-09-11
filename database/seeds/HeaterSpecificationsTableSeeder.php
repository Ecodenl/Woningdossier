<?php

use Illuminate\Database\Seeder;

class HeaterSpecificationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $items = [
            [
                'liters' => 40,
                'savings' => 700,
                'boiler' => 120,
                'collector' => 1.6,
            ],
            [
                'liters' => 48,
                'savings' => 850,
                'boiler' => 120,
                'collector' => 1.6,
            ],
            [
                'liters' => 55,
                'savings' => 975,
                'boiler' => 120,
                'collector' => 1.6,
            ],
            [
                'liters' => 58,
                'savings' => 1000,
                'boiler' => 120,
                'collector' => 1.6,
            ],
            [
                'liters' => 71,
                'savings' => 1200,
                'boiler' => 120,
                'collector' => 1.6,
            ],
            [
                'liters' => 74,
                'savings' => 1200,
                'boiler' => 120,
                'collector' => 1.6,
            ],
            [
                'liters' => 83,
                'savings' => 1390,
                'boiler' => 120,
                'collector' => 2.5,
            ],
            [
                'liters' => 92,
                'savings' => 1430,
                'boiler' => 120,
                'collector' => 2.5,
            ],
            [
                'liters' => 93,
                'savings' => 1430,
                'boiler' => 120,
                'collector' => 2.5,
            ],
            [
                'liters' => 110,
                'savings' => 2330,
                'boiler' => 120,
                'collector' => 3.2,
            ],
            [
                'liters' => 112,
                'savings' => 2315,
                'boiler' => 120,
                'collector' => 3.2,
            ],
            [
                'liters' => 127,
                'savings' => 2680,
                'boiler' => 120,
                'collector' => 3.2,
            ],
            [
                'liters' => 138,
                'savings' => 2805,
                'boiler' => 120,
                'collector' => 3.2,
            ],
            [
                'liters' => 140,
                'savings' => 2811,
                'boiler' => 120,
                'collector' => 3.2,
            ],
            [
                'liters' => 153,
                'savings' => 3025,
                'boiler' => 200,
                'collector' => 3.2,
            ],
            [
                'liters' => 158,
                'savings' => 3035,
                'boiler' => 200,
                'collector' => 3.2,
            ],
            [
                'liters' => 165,
                'savings' => 3050,
                'boiler' => 200,
                'collector' => 3.2,
            ],
            [
                'liters' => 178,
                'savings' => 3070,
                'boiler' => 200,
                'collector' => 3.2,
            ],
            [
                'liters' => 190,
                'savings' => 4065,
                'boiler' => 200,
                'collector' => 4.8,
            ],
            [
                'liters' => 196,
                'savings' => 4080,
                'boiler' => 200,
                'collector' => 4.8,
            ],
            [
                'liters' => 240,
                'savings' => 5850,
                'boiler' => 300,
                'collector' => 6.4,
            ],
        ];

        \DB::table('heater_specifications')->insert($items);
    }
}
