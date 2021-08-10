<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model;
use Faker\Generator as Faker;

$factory->define(\App\Models\CustomMeasureApplication::class, function (Faker $faker) {
    return [
        'name' => ['nl' => $faker->randomElement(['Vloertje', 'Bakstel', 'Nieuwe lampen'])],
        'info' => ['nl' => $faker->randomElement(['Vloertje', 'Bakstel', 'Nieuwe lampen'])],
        'extra' => ['icon' => 'icon-tools'],
    ];
});
