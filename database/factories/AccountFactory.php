<?php

use Faker\Generator as Faker;
use Illuminate\Support\Facades\Hash;

$factory->define(\App\Models\Account::class, function (Faker $faker) {
    static $password;

    return [
        'email' => $faker->email,
        'password' => $password ?: Hash::make('secret'),
        'active' => true,
    ];
});
