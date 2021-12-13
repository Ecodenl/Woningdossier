<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\Step::class, function (Faker $faker) {
    $name = $faker->word();
    return [
        'parent_id' => mt_rand(0, 3) == 0 ? factory(\App\Models\Step::class) : null,
        'name' => $name,
        'slug' => \Illuminate\Support\Str::slug($name),
        'short' => \Illuminate\Support\Str::slug($name),
        'order' => $faker->randomNumber(1),
    ];
});