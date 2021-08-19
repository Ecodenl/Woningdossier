<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\InputSource::class, function (Faker $faker) {
    $name = $faker->word;
    $short = \Illuminate\Support\Str::slug($name);
    return [
        'name' => $name,
        'short' => $short,
        'order' => $faker->randomNumber(2),
    ];
});
