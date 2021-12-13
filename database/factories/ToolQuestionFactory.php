<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model;
use Faker\Generator as Faker;

$factory->define(\App\Models\ToolQuestion::class, function (Faker $faker) {
    return [
        'name' => ['nl' => $faker->text(60)],
        'help_text' => ['nl' => $faker->text(240)],
        'tool_question_type_id' => \App\Models\ToolQuestionType::inRandomOrder()->first()->id,
        'save_in' => ['table' => 'building_elements', 'column' => 'element_id'],
        'unit_of_measure' => $faker->randomElement(['m2', 'graden']),
        'coach' => $faker->boolean,
        'resident' => $faker->boolean,
    ];
});
