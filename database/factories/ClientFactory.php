<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model;
use Faker\Generator as Faker;


$factory->define(\App\Models\Client::class, function (Faker $faker) {
    $name = $faker->randomElement(['groenezang', 'meteropnull', 'greeni', 'neutraallicht', 'geenplastic', 'groenenergieentech']);
    return [
        'name' => $name,
        'short' => \Illuminate\Support\Str::slug($name),
    ];
});
