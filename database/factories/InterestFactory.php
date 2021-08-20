<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\Interest::class, function (Faker $faker) {
    return [
        'name' => json_encode(['nl' => $faker->word]),
        'calculate_value' => $faker->randomFloat(2, 0, 100),
        'order' => $faker->randomNumber(2),
    ];
});
