<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\ToolQuestion;
use App\Models\ToolQuestionType;
use Faker\Generator as Faker;

$factory->define(ToolQuestion::class, function (Faker $faker) {
    return [
        'name' => json_encode(['nl' => $faker->text(60)]),
        'help_text' => json_encode(['nl' => $faker->text(240)]),
        'save_in' => null,
        'unit_of_measure' => $faker->randomElement(['m2', 'graden']),
        'coach' => $faker->boolean,
        'resident' => $faker->boolean,
    ];
});
