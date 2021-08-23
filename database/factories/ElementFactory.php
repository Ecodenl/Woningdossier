<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\Element::class, function (Faker $faker) {
    $name = $faker->word;
    $short = \Illuminate\Support\Str::slug($name);
    return [
        'name' => json_encode(['nl' => $name]),
        'short' => $short,
        'service_type_id' => factory(\App\Models\ServiceType::class),
        'order' => $faker->randomNumber(2),
        'info' => json_encode(['nl' => $faker->sentence]),
    ];
});
