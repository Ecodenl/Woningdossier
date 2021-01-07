<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\Account::class, function (Faker $faker) {
    static $password;

    return [
        'email' => $faker->email,
        'password' => $password ?: bcrypt('secret'),
        'active' => true,
    ];
});
