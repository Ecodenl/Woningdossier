<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\ServiceType::class, function (Faker $faker) {
    return [
        'name' => json_encode(['nl' => $faker->word]),
        'iso' => "M" . $faker->randomDigit,
    ];
});
