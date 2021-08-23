<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\BuildingHeater::class, function (Faker $faker) {
    return [
        'building_id' => factory(\App\Models\Building::class),
        'input_source_id' => factory(\App\Models\InputSource::class),
        'pv_panel_orientation_id' => factory(\App\Models\PvPanelOrientation::class),
        'angle' => 10 * mt_rand(1, 9),
    ];
});
