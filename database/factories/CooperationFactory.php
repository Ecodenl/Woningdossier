<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\Cooperation::class, function (Faker $faker) {
    return [
        'name' => $faker->randomElement(['Groen', 'CO2']),
        'slug' => $faker->randomElement(['Groen', 'CO2']),
        'website_url' => $faker->url,
        'cooperation_email' => $faker->email,
    ];
});
