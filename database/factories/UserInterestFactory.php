<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\UserInterest::class, function (Faker $faker) {
    // TODO: Make factory for measure application and remove Element model, so it's more realistic
    $model = $faker->randomElement([
//        \App\Models\MeasureApplication::class,
        \App\Models\Step::class,
        \App\Models\Element::class,
    ]);

    return [
        'user_id' => factory(\App\Models\User::class),
        'input_source_id' => factory(\App\Models\InputSource::class),
        'interested_in_type' => $model,
        'interested_in_id' => factory($model),
        'interest_id' => factory(\App\Models\Interest::class),
    ];
});
