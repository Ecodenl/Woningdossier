<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\InputSource::class, function (Faker $faker) {
    $name = $faker->word;
    $short = \Illuminate\Support\Str::slug($name);

    $order = DB::table('input_sources')->max('order') + 1;
    return [
        'name' => $name,
        'short' => $short,
        'order' => $order,
    ];
});
