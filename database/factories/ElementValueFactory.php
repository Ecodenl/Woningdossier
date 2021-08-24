<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\ElementValue::class, function (Faker $faker) {
    return [
        'element_id' => factory(\App\Models\Element::class),
        'value' => json_encode(['nl' => $faker->word]),
        'calculate_value' => $faker->randomFloat(2, 0, 100),
        'order' => $faker->randomNumber(2),
    ];
});
