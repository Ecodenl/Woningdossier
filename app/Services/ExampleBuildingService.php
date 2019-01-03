<?php

namespace App\Services;

use App\Models\Building;
use App\Models\BuildingElement;
use App\Models\BuildingFeature;
use App\Models\BuildingHeater;
use App\Models\BuildingInsulatedGlazing;
use App\Models\BuildingPaintworkStatus;
use App\Models\BuildingPvPanel;
use App\Models\BuildingRoofType;
use App\Models\BuildingService;
use App\Models\BuildingType;
use App\Models\Element;
use App\Models\ElementValue;
use App\Models\ExampleBuilding;
use App\Models\InputSource;
use App\Models\PaintworkStatus;
use App\Models\Service;
use App\Models\ServiceValue;
use App\Models\WoodRotStatus;
use App\Scopes\GetValueScope;
use Symfony\Component\Debug\Debug;

class ExampleBuildingService {

	public static function apply(ExampleBuilding $exampleBuilding, $buildYear, Building $userBuilding){
		$inputSource = InputSource::findByShort('example-building');

		// Clear the current example building data
		self::log("Lookup " . $exampleBuilding->name . " for " . $buildYear);
		$contents = $exampleBuilding->getContentForYear($buildYear);

		// traverse the contents:
		$exampleData = $contents->content;

		self::log("Applying Example Building " . $exampleBuilding->name . " (" . $exampleBuilding->id . ", " . $contents->build_year . ")");

		self::clearExampleBuilding($userBuilding);

		$features = [];

		foreach($exampleData as $stepSlug => $stepData){
			self::log("=====");
			self::log("Processing " . $stepSlug);
			self::log("=====");


			foreach($stepData as $columnOrTable => $values){
				self::log("-> " . $stepSlug . " + " . $columnOrTable . " <-");

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

							$element = Element::find($elementId);
							if ($element instanceof Element){

								$buildingElement = new BuildingElement([ 'extra' => $extra, ]);
								$buildingElement->inputSource()->associate($inputSource);
								$buildingElement->element()->associate($element);
								$buildingElement->building()->associate( $userBuilding );

								if (!is_null($elementValueId)) {
									$elementValue = $element->values()->where( 'id', $elementValueId )->first();

									if ( $elementValue instanceof ElementValue ) {
										$buildingElement->elementValue()->associate( $elementValue );
									}
								}

								$buildingElement->save();
								self::log( "Saving building element " . json_encode( $buildingElement->toArray() ) );
							}
						}
					}
				}
				if ($columnOrTable == 'service'){
					// process elements
					if (is_array($values)){
						foreach($values as $serviceId => $serviceValueData){
							$extra = null;
							if (is_array($serviceValueData)){
								if (!array_key_exists('service_value_id', $serviceValueData)){
									self::log("Skipping service value as there is no service_value_id");
									continue;
								}
								$serviceValueId = (int) $serviceValueData['service_value_id'];
								if (array_key_exists('extra', $serviceValueData)){
									$extra = $serviceValueData['extra'];
								}
							}
							else {
								$serviceValueId = (int) $serviceValueData;
							}
							if (!is_null($serviceValueId)){
								$service = Service::find($serviceId);
								if ($service instanceof Service){
									$serviceValue = $service->values()->where('id', $serviceValueId)->first();
									if ($serviceValue instanceof ServiceValue){
										$buildingService = new BuildingService([ 'extra' => $extra, ]);
										$buildingService->inputSource()->associate($inputSource);
										$buildingService->service()->associate($service);
										$buildingService->serviceValue()->associate($serviceValue);
										$buildingService->building()->associate($userBuilding);
										$buildingService->save();
										self::log("Saving building service " . json_encode($buildingService->toArray()));
									}
								}
							}
						}
					}
				}
				if($columnOrTable == 'building_features'){
					$features = array_replace_recursive($features, $values);

				}
				if ($columnOrTable == 'building_paintwork_statuses'){
					$statusId = array_get($values, 'paintwork_status_id');
					$woodRotStatusId = array_get($values, 'wood_rot_status_id');

					if (empty($statusId) || empty($woodRotStatusId)){
						self::log("Skipping paintwork status as the paint or wood rot (or both) status is empty");
						continue;
					}

					$buildingPaintworkStatus = new BuildingPaintworkStatus($values);

					$buildingPaintworkStatus->inputSource()->associate($inputSource);
					$buildingPaintworkStatus->building()->associate($userBuilding);
					$buildingPaintworkStatus->save();

					//continue;
				}
				if ($columnOrTable == 'building_insulated_glazings'){
					foreach($values as $measureApplicationId => $glazingData){
						$glazingData['measure_application_id'] = $measureApplicationId;

						$buildingInsulatedGlazing = new BuildingInsulatedGlazing($glazingData);

						$buildingInsulatedGlazing->inputSource()->associate($inputSource);
						$buildingInsulatedGlazing->building()->associate($userBuilding);
						$buildingInsulatedGlazing->save();

						self::log("Saving building insulated glazing " . json_encode($buildingInsulatedGlazing->toArray()));
					}
				}
				if ($columnOrTable == 'building_roof_types'){
					foreach($values as $roofTypeId => $buildingRoofTypeData) {
						$buildingRoofTypeData['roof_type_id'] = $roofTypeId;

						$buildingRoofType = new BuildingRoofType( $buildingRoofTypeData );
						$buildingRoofType->inputSource()->associate($inputSource);
						$buildingRoofType->building()->associate($userBuilding);
						$buildingRoofType->save();

						self::log("Saving building rooftype " . json_encode($buildingRoofType->toArray()));
					}
				}
				if ($columnOrTable == 'building_pv_panels'){
					$buildingPvPanels = new BuildingPvPanel($values);
					$buildingPvPanels->inputSource()->associate($inputSource);
					$buildingPvPanels->building()->associate($userBuilding);
					$buildingPvPanels->save();

					self::log("Saving building pv_panels " . json_encode($buildingPvPanels->toArray()));
				}
				if ($columnOrTable == 'building_heaters'){
					$buildingHeater = new BuildingHeater($values);
					$buildingHeater->inputSource()->associate($inputSource);
					$buildingHeater->building()->associate($userBuilding);
					$buildingHeater->save();

					self::log("Saving building heater " . json_encode($buildingHeater->toArray()));
				}
			}
		}

		self::log("processing features " . json_encode($features));
		$buildingFeatures = new BuildingFeature($features);
		$buildingFeatures->buildingType()->associate($exampleBuilding->buildingType);
		$buildingFeatures->inputSource()->associate($inputSource);
		$buildingFeatures->building()->associate($userBuilding);
		$buildingFeatures->save();
		self::log("Saving building features " . json_encode($buildingFeatures->toArray()));

		//dd($exampleData);

	}

	public static function clearExampleBuilding(Building $building){
		$inputSource = InputSource::findByShort('example-building');

		return BuildingDataService::clearBuildingFromInputSource($building, $inputSource);
	}

	protected static function log($text){
		\Log::debug(__CLASS__ . " " . $text);
	}
}