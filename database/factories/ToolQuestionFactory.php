<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\ToolQuestion;
use App\Models\ToolQuestionType;
use Illuminate\Support\Facades\Artisan;
use Faker\Generator as Faker;

$factory->define(ToolQuestion::class, function (Faker $faker) {
    // We can safely seed as it's an updateOrInsert. They should simply always exist
    $toolQuestionType = ToolQuestionType::inRandomOrder()->first();

    if (! $toolQuestionType instanceof ToolQuestionType) {
        Artisan::call('db:seed', ['--class' => ToolQuestionTypesTableSeeder::class, '--force' => true]);
        $toolQuestionType = ToolQuestionType::inRandomOrder()->first();
    }

    return [
        'name' => json_encode(['nl' => $faker->text(60)]),
        'help_text' => json_encode(['nl' => $faker->text(240)]),
        'tool_question_type_id' => $toolQuestionType->id,
        'save_in' => json_encode(['table' => 'building_elements', 'column' => 'element_id']),
        'unit_of_measure' => $faker->randomElement(['m2', 'graden']),
        'coach' => $faker->boolean,
        'resident' => $faker->boolean,
    ];
});
