<?php

namespace App\Services;

use App\Models\Building;
use App\Models\BuildingElement;
use App\Models\BuildingFeature;
use App\Models\BuildingType;
use App\Models\Element;
use App\Models\ElementValue;
use App\Models\ExampleBuilding;
use App\Models\InputSource;
use App\Scopes\GetValueScope;
use Symfony\Component\Debug\Debug;

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

		self::log("Applying Example Building " . $exampleBuilding->name . " (" . $exampleBuilding->id . ")");

		self::clearExampleBuilding($userBuilding);

		// todo belongs to "todo temporary"
		$features = [];

		/**
		 * Note the continue statements!!
		 * This makes us * not * doing if-elseif-elseif-elseif...-else
		 */

		foreach($exampleData as $stepSlug => $stepData){
			self::log("=====");
			self::log("Processing " . $stepSlug);
			self::log("=====");
			foreach($stepData as $columnOrTable => $values){
				if (is_null($values)){
					self::log("Skipping " . $columnOrTable . " (empty)");
					continue;
				}
				if ($columnOrTable == 'user_interest'){
					self::log("Skipping outdated user interests");
					continue;
				}
				if ($columnOrTable == 'element'){
					// process elements
					if (is_array($values)){
						foreach($values as $elementId => $elementValueData){
							$extra = null;
							if (is_array($elementValueData)){
								if (!array_key_exists('element_value_id', $elementValueData)){
									self::log("Skipping element value as there is no element_value_id");
									continue;
								}
								$elementValueId = (int) $elementValueData['element_value_id'];
								if (array_key_exists('extra', $elementValueData)){
									$extra = $elementValueData['extra'];
								}
							}
							else {
								$elementValueId = (int) $elementValueData;
							}
							if (!is_null($elementValueId)){
								$element = Element::find($elementId);
								if ($element instanceof Element){
									$elementValue = $element->values()->where('id', $elementValueId)->first();
									if ($elementValue instanceof ElementValue){
										$buildingElement = new BuildingElement([ 'extra' => $extra, ]);
										$buildingElement->inputSource()->associate($inputSource);
										$buildingElement->element()->associate($element);
										$buildingElement->elementValue()->associate($elementValue);
										$buildingElement->building()->associate($userBuilding);
										$buildingElement->save();
										self::log("Saving building element " . json_encode($buildingElement->toArray()));
									}
								}
							}
						}
					}
					continue;
				}
				// todo temporary
				// wall-insulation
				// wall_surface => building_features
				// cavity_wall => building_features
				// facade_plastered_painted => building_features
				// facade_damaged_paintwork_id => building_features
				// wall_joints => building_features
				// contaminated_wall_joints => building_features
				if(in_array($columnOrTable, ['wall_surface', 'cavity_wall', 'facade_plastered_painted', 'facade_damaged_paintwork_id', 'wall_joints', 'contaminated_wall_joints'])){
					// we already know that values is filled
					$features[$columnOrTable] = $values;
				}



			}
		}

		// todo belongs to "todo temporary"
		$buildingFeatures = new BuildingFeature($features);
		$buildingFeatures->buildingType()->associate($exampleBuilding->buildingType);
		$buildingFeatures->inputSource()->associate($inputSource);
		$buildingFeatures->building()->associate($userBuilding);
		$buildingFeatures->save();
		self::log("Saving building features " . json_encode($buildingFeatures->toArray()));

		//dd($exampleData);

	}

	public static function clearBuildingFromInputSource(Building $building, InputSource $inputSource){
		self::log("Clearing data from input source '" . $inputSource->name . "'");

		// Delete all building elements
		$building->buildingElements()->withoutGlobalScope(GetValueScope::class)->where('input_source_id', $inputSource->id)->delete();
		$building->buildingFeatures()->withoutGlobalScope(GetValueScope::class)->where('input_source_id', $inputSource->id)->delete();

		return true;
	}

	public static function clearExampleBuilding(Building $building){
		$inputSource = InputSource::findByShort('example-building');

		return self::clearBuildingFromInputSource($building, $inputSource);
	}

	protected static function log($text){
		\Log::debug(__CLASS__ . " " . $text);
	}
}