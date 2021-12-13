<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\PvPanelOrientation::class, function (Faker $faker) {
    $name = $faker->word;

    return [
        'name' => json_encode(['nl' => $name]),
        'short' => strtolower($name[0]),
        'order' => $faker->randomNumber(2),
    ];
});
