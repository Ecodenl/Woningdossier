<?php

namespace App\Services;

use App\Models\Building;
use App\Models\BuildingElement;
use App\Models\BuildingFeature;
use App\Models\BuildingInsulatedGlazing;
use App\Models\BuildingPaintworkStatus;
use App\Models\BuildingType;
use App\Models\Element;
use App\Models\ElementValue;
use App\Models\ExampleBuilding;
use App\Models\InputSource;
use App\Models\PaintworkStatus;
use App\Models\WoodRotStatus;
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

		$features = [];

		/**
		 * Note the continue statements!!
		 * This makes us * not * doing if-elseif-elseif-elseif...-else
		 */

		dd($exampleData);

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
				if ($columnOrTable == 'building_paintwork_statuses'){
					$statusId = array_get($values, 'paintwork_status_id');
					$woodRotStatusId = array_get($values, 'wood_rot_status_id');

					if (empty($statusId) || empty($woodRotStatusId)){
						self::log("Skipping paintwork status as the paint or wood rot (or both) status is empty");
						continue;
					}

					$buildingPaintworkStatus = new BuildingPaintworkStatus($values);

					/*
					$buildingPaintworkStatus->last_painted_year = array_get($values, 'last_painted_year');

					$statusId = array_get($values, 'paintwork_status_id');
					if (!is_null($statusId)){
						$status = PaintworkStatus::find($statusId);
						if ($status instanceof PaintworkStatus){
							$buildingPaintworkStatus->paintworkStatus()->associate($status);
						}
					}

					$woodRotStatusId = array_get($values, 'wood_rot_status_id');
					if (!is_null($woodRotStatusId)){
						$woodRotStatus = WoodRotStatus::find($woodRotStatusId);
						if ($woodRotStatus instanceof WoodRotStatus){
							$buildingPaintworkStatus->woodRotStatus()->associate($woodRotStatus);
						}
					}
					*/

					$buildingPaintworkStatus->inputSource()->associate($inputSource);
					$buildingPaintworkStatus->building()->associate($userBuilding);
					$buildingPaintworkStatus->save();

					continue;
				}
				if ($columnOrTable == 'building_insulated_glazings'){
					foreach($values as $measureApplicationId => $glazingData){
						$glazingData['measure_application_id'] = $measureApplicationId;

						$buildingInsulatedGlazing = new BuildingInsulatedGlazing($glazingData);

						$buildingInsulatedGlazing->inputSource()->associate($inputSource);
						$buildingInsulatedGlazing->building()->associate($userBuilding);
						$buildingInsulatedGlazing->save();

						continue;
					}
				}


				// wall-insulation
				// wall_surface => building_features
				// cavity_wall => building_features
				// facade_plastered_painted => building_features
				// facade_damaged_paintwork_id => building_features
				// wall_joints => building_features
				// contaminated_wall_joints => building_features
				if($stepSlug == 'wall-insulation' && in_array($columnOrTable, ['wall_surface', 'cavity_wall', 'facade_plastered_painted', 'facade_damaged_paintwork_id', 'wall_joints', 'contaminated_wall_joints'])){
					// we already know that values is filled
					$features[$columnOrTable] = $values;

					continue;
				}
				if($stepSlug == 'floor-insulation' && in_array($columnOrTable, ['floor_surface',])){
					$features[$columnOrTable] = $values;

					continue;
				}

				self::log("unknown element: " . $columnOrTable);


			}
		}

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