<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\Question::class, function (Faker $faker) {
    $uuid = \App\Helpers\Str::uuid();

    \App\Models\Translation::create([
        'key' => $uuid,
        'translation' => $faker->text(80),
        'language' => 'nl',
    ]);

    return [
        'type' => 'text',
        'name' => $uuid,
        'required' => $faker->boolean,
        'validation' => [],
    ];
});
