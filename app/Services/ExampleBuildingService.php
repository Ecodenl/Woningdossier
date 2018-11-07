<?php

namespace App\Services;

use App\Models\Building;
use App\Models\ExampleBuilding;
use App\Models\InputSource;
use App\Scopes\GetValueScope;

class ExampleBuildingService {

	public static function apply(ExampleBuilding $exampleBuilding, $buildYear, Building $userBuilding){
		$inputSource = InputSource::findByShort('example-building');

		// Clear the current example building data
		$contents = $exampleBuilding->getContentForYear($buildYear);


		// Check if there's already a building for the example building input source
		/*
		$building = Building::withoutGlobalScope(GetValueScope::class)
		            ->where('user_id', $userBuilding->user_id)
		            ->where('input_source_id', $inputSource->id)
					->first();

		if ($building instanceof Building){
			$building->delete();
		}

		$building = new Building();
		*/
		// traverse the contents:
		$exampleData = $contents->content;

		foreach($exampleData as $stepSlug => $stepData){
			foreach($stepData as $columnOrTable => $values){
				if (is_null($values)){
					\Log::debug("Skipping " . $columnOrTable);
					continue;
				}
				if ($columnOrTable == 'user_interest'){
					\Log::debug("Skipping outdated user interests");
					continue;
				}

			}
		}


	}
}