<?php

namespace App\Services;

use App\Models\Building;
use App\Models\ExampleBuilding;
use App\Models\InputSource;

class ExampleBuildingService {

	public static function apply(ExampleBuilding $exampleBuilding, $buildYear, Building $building){
		$inputSource = InputSource::findByShort('example-building');

		// Clear the current example building data
		$contents = $exampleBuilding->getContentForYear($buildYear);

		dd($contents);


	}
}