<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Questionnaire::class, function (Faker $faker) {
    return [
        'cooperation_id' => factory(\App\Models\Cooperation::class),
        'step_id' => factory(\App\Models\Step::class),
        'order' => 0,
        'name' => [
            'nl' => $faker->text(80),
        ],
    ];
});
