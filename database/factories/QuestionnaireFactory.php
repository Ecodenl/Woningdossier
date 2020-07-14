<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Questionnaire::class, function (Faker $faker) {
    $uuid = \App\Helpers\Str::uuid();

    \App\Models\Translation::create([
        'key' => $uuid,
        'translation' => $faker->text(80),
        'language' => 'nl'
    ]);

    return [
        'cooperation_id' => \App\Models\Cooperation::find(1),
        'step_id' => \App\Models\Step::find(1),
        'order' => 0,
        'name' => $uuid
    ];
});