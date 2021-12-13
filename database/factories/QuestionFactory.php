<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\Question::class, function (Faker $faker) {
    return [
        'type' => 'text',
        'name' => [
            'nl' => $faker->text(80),
        ],
        'required' => $faker->boolean,
        'validation' => [],
    ];
});
