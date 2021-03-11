<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\Cooperation::class, function (Faker $faker) {
    $name = $faker->randomElement(['Groen', 'CO2', 'Meter op null']);
    return [
        'name' => $name,
        'slug' => \Illuminate\Support\Str::slug($name),
        'website_url' => $faker->url,
        'cooperation_email' => $faker->email,
    ];
});
