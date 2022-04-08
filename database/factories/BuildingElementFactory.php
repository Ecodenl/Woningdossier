<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\BuildingElement::class, function (Faker $faker) {
    return [
        'building_id' => factory(\App\Models\Building::class),
        'input_source_id' => factory(\App\Models\InputSource::class),
        'element_id' => factory(\App\Models\Element::class),
        'element_value_id' => factory(\App\Models\ElementValue::class),
        'extra' => null,
    ];
});
