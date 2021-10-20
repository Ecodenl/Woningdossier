<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model;
use Faker\Generator as Faker;

$factory->define(\App\Models\BuildingVentilation::class, function (Faker $faker) {
    return [
        'how' => $faker->randomElements(["windows", "windows-doors", "other"]),
        'living_situation' => $faker->randomElements(["dry-laundry", "fireplace", "combustion-device"]),
    ];
});
