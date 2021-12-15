<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\Building::class, function (Faker $faker) {
    return [
        'street' => $faker->streetName,
        'number' => $faker->numberBetween(3, 22),
        'city' => $faker->city,
        'postal_code' => $faker->postcode,
        'country_code' => $faker->countryCode,
        'owner' => $faker->boolean,
        'primary' => $faker->boolean,
        'user_id' => factory(\App\Models\User::class),
    ];
});
