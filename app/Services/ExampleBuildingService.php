<?php

namespace App\Services;

use App\Events\ExampleBuildingChanged;
use App\Helpers\KeyFigures\Heater\KeyFigures as HeaterKeyFigures;
use App\Helpers\KeyFigures\PvPanels\KeyFigures as SolarPanelsKeyFigures;
use App\Helpers\KeyFigures\RoofInsulation\Temperature;
use App\Helpers\ToolHelper;
use App\Helpers\Translation;
use App\Models\Building;
use App\Models\BuildingElement;
use App\Models\BuildingFeature;
use App\Models\BuildingHeater;
use App\Models\BuildingHeating;
use App\Models\BuildingInsulatedGlazing;
use App\Models\BuildingPaintworkStatus;
use App\Models\BuildingPvPanel;
use App\Models\BuildingRoofType;
use App\Models\BuildingService;
use App\Models\Element;
use App\Models\ElementValue;
use App\Models\EnergyLabel;
use App\Models\ExampleBuilding;
use App\Models\ExampleBuildingContent;
use App\Models\InputSource;
use App\Models\Service;
use App\Models\UserEnergyHabit;
use App\Scopes\GetValueScope;

class ExampleBuildingService
{
    public static function apply(ExampleBuilding $exampleBuilding, $buildYear, Building $userBuilding)
    {
        $inputSource = InputSource::findByShort('example-building');

        // Clear the current example building data
        self::log('Lookup '.$exampleBuilding->name.' for '.$buildYear);
        $contents = $exampleBuilding->getContentForYear($buildYear);
        if (! $contents instanceof ExampleBuildingContent) {
            // There's nothing to apply
            self::log('No data to apply');

            return;
        }

        $boilerService = Service::where('short', 'boiler')->first();

        // used for throwing the event at the end
        $oldExampleBuilding = $userBuilding->exampleBuilding;

        // traverse the contents:
        $exampleData = $contents->content;

//        dd($exampleData);
        self::log('Applying Example Building '.$exampleBuilding->name.' ('.$exampleBuilding->id.', '.$contents->build_year.')');

        self::clearExampleBuilding($userBuilding);

        $features = [];

        foreach ($exampleData as $stepSlug => $dataForStep) {
            self::log('=====');
            self::log('Processing '.$stepSlug);
            self::log('=====');

            foreach ($dataForStep as $subStep => $subStepData) {
                foreach ($subStepData as $columnOrTable => $values) {
                    self::log('-----> ' . $stepSlug . ' - ' . $columnOrTable);

                    if (is_null($values)) {
                        self::log('Skipping ' . $columnOrTable . ' (empty)');
                        continue;
                    }
                    if ('user_energy_habits' == $columnOrTable) {
                        $habits = UserEnergyHabit::create($values);
                        $habits->inputSource()->associate($inputSource);
                        $habits->user()->associate($userBuilding->user);
                        $habits->save();
                    }
                    if ('element' == $columnOrTable) {
                        // process elements
                        if (is_array($values)) {
                            foreach ($values as $elementId => $elementValueData) {
                                $extra = null;
                                $elementValues = [];
                                if (is_array($elementValueData)) {
                                    if (!array_key_exists('element_value_id', $elementValueData)) {
                                        // perhaps a nested array (e.g. wood elements)
                                        foreach ($elementValueData as $elementValueDataItem) {
                                            if (is_array($elementValueDataItem) && array_key_exists('element_value_id', $elementValueDataItem)) {
                                                $d = ['element_value_id' => (int)$elementValueDataItem['element_value_id']];
                                                if (array_key_exists('extra', $elementValueDataItem)) {
                                                    $d['extra'] = $elementValueDataItem['extra'];
                                                }
                                                $elementValues[] = $d;
                                            } else {
                                                $elementValues[] = ['element_value_id' => (int)$elementValueDataItem];
                                            }
                                        }
                                    } else {
                                        if (array_key_exists('element_value_id', $elementValueData)) {
                                            $d = ['element_value_id' => (int)$elementValueData['element_value_id']];
                                            if (array_key_exists('extra', $elementValueData)) {
                                                $d['extra'] = $elementValueData['extra'];
                                            }
                                            $elementValues[] = $d;
                                        } else {
                                            $elementValues[] = ['element_value_id' => (int)$elementValueData];
                                        }
                                    }
                                } else {
                                    $elementValues[] = ['element_value_id' => (int)$elementValueData];
                                }

                                $element = Element::find($elementId);
                                if ($element instanceof Element) {
                                    foreach ($elementValues as $elementValue) {
                                        $extra = array_key_exists('extra', $elementValue) ? $elementValue['extra'] : null;
                                        $buildingElement = new BuildingElement(['extra' => $extra]);
                                        $buildingElement->inputSource()->associate($inputSource);
                                        $buildingElement->element()->associate($element);
                                        $buildingElement->building()->associate($userBuilding);

                                        if (isset($elementValue['element_value_id'])) {
                                            $elementValue = $element->values()->where('id',
                                                $elementValue['element_value_id'])->first();

                                            if ($elementValue instanceof ElementValue) {
                                                $buildingElement->elementValue()->associate($elementValue);
                                            }
                                        }

                                        $buildingElement->save();
                                        self::log('Saving building element ' . json_encode($buildingElement->toArray()));
                                    }
                                }
                            }
                        }
                    }
                    if ('service' == $columnOrTable) {
                        // process elements
                        if (is_array($values)) {
                            foreach ($values as $serviceId => $serviceValueData) {
                                $extra = null;
                                // note: in the case of solar panels the service_value_id can be null!!
                                if (is_array($serviceValueData)) {
                                    if (!array_key_exists('service_value_id', $serviceValueData)) {
                                        self::log('Service ID ' . $serviceId . ': no service_value_id -> service_value_id set to NULL');
                                        $serviceValueId = null;
                                    } else {
                                        $serviceValueId = (int)$serviceValueData['service_value_id'];
                                    }
                                    if (array_key_exists('extra', $serviceValueData)) {
                                        $extra = $serviceValueData['extra'];
                                    }
                                } else {
                                    $serviceValueId = (int)$serviceValueData;
                                }
                                $service = Service::find($serviceId);
                                if ($service instanceof Service) {
                                    // try to obtain a existing service
                                    $existingBuildingService = BuildingService::withoutGlobalScope(GetValueScope::class)
                                        ->forMe()
                                        ->where('input_source_id', $inputSource->id)
                                        ->where('service_id', $serviceId)->first();

                                    // see if it already exists, if so we need to add data to that service

                                    // this is for example the case with the hr boiler, data is added on general-data and on the hr page itself
                                    // but this can only be saved under one row, so we have to update it
                                    if ($existingBuildingService instanceof BuildingService) {
                                        $buildingService = $existingBuildingService;
                                    } else {
                                        $buildingService = new BuildingService();
                                        $buildingService->inputSource()->associate($inputSource);
                                        $buildingService->service()->associate($service);
                                        $buildingService->building()->associate($userBuilding);
                                    }

                                    if (is_array($extra)) {
                                        if ($boilerService->id == $serviceId) {
                                            $extra = ['date' => $extra['date']];
                                        }
                                        $buildingService->extra = $extra;
                                    }

                                    if (!is_null($serviceValueId)) {
                                        $serviceValue = $service->values()->where('id', $serviceValueId)->first();
                                        $buildingService->serviceValue()->associate($serviceValue);
                                    }

                                    $buildingService->save();

                                    self::log('Saving building service ' . json_encode($buildingService->toArray()));
                                }
                            }
                        }
                    }
                    if ('building_features' == $columnOrTable) {
                        $features = array_replace_recursive($features, $values);
                    }
                    if ('building_paintwork_statuses' == $columnOrTable) {
                        $statusId = array_get($values, 'paintwork_status_id');
                        $woodRotStatusId = array_get($values, 'wood_rot_status_id');

                        if (empty($statusId) || empty($woodRotStatusId)) {
                            self::log('Skipping paintwork status as the paint or wood rot (or both) status is empty');
                            continue;
                        }

                        $buildingPaintworkStatus = new BuildingPaintworkStatus($values);

                        $buildingPaintworkStatus->inputSource()->associate($inputSource);
                        $buildingPaintworkStatus->building()->associate($userBuilding);
                        $buildingPaintworkStatus->save();

                        //continue;
                    }
                    if ('building_insulated_glazings' == $columnOrTable) {
                        foreach ($values as $measureApplicationId => $glazingData) {
                            $glazingData['measure_application_id'] = $measureApplicationId;

                            //todo: so the insulated_glazing_id is non existent in the table, this is a typo and should be fixed in the tool structure
                            $glazingData['insulating_glazing_id'] = $glazingData['insulated_glazing_id'];

                            $buildingInsulatedGlazing = new BuildingInsulatedGlazing($glazingData);

                            $buildingInsulatedGlazing->inputSource()->associate($inputSource);
                            $buildingInsulatedGlazing->building()->associate($userBuilding);
                            $buildingInsulatedGlazing->save();

                            self::log('Saving building insulated glazing ' . json_encode($buildingInsulatedGlazing->toArray()));
                        }
                    }
                    if ('building_roof_types' == $columnOrTable) {
                        foreach ($values as $roofTypeId => $buildingRoofTypeData) {
                            $buildingRoofTypeData['roof_type_id'] = $roofTypeId;

                            if (isset($buildingRoofTypeData['roof_surface']) && (int)$buildingRoofTypeData['roof_surface'] > 0) {
                                $buildingRoofType = new BuildingRoofType($buildingRoofTypeData);
                                $buildingRoofType->inputSource()->associate($inputSource);
                                $buildingRoofType->building()->associate($userBuilding);
                                $buildingRoofType->save();

                                self::log('Saving building rooftype ' . json_encode($buildingRoofType->toArray()));
                            } else {
                                self::log('Not saving building rooftype because surface is 0');
                            }
                        }
                    }
                    if ('building_pv_panels' == $columnOrTable) {
                        $buildingPvPanels = new BuildingPvPanel($values);
                        $buildingPvPanels->inputSource()->associate($inputSource);
                        $buildingPvPanels->building()->associate($userBuilding);
                        $buildingPvPanels->save();

                        self::log('Saving building pv_panels ' . json_encode($buildingPvPanels->toArray()));
                    }
                    if ('building_heaters' == $columnOrTable) {
                        $buildingHeater = new BuildingHeater($values);
                        $buildingHeater->inputSource()->associate($inputSource);
                        $buildingHeater->building()->associate($userBuilding);
                        $buildingHeater->save();

                        self::log('Saving building heater ' . json_encode($buildingHeater->toArray()));
                    }
                }
            }
        }

        self::log('processing features '.json_encode($features));
        $buildingFeatures = new BuildingFeature($features);
        $buildingFeatures->buildingType()->associate($exampleBuilding->buildingType);
        $buildingFeatures->inputSource()->associate($inputSource);
        $buildingFeatures->building()->associate($userBuilding);
        $buildingFeatures->save();
        self::log('Saving building features '.json_encode($buildingFeatures->toArray()));

        ExampleBuildingChanged::dispatch($userBuilding, $oldExampleBuilding, $exampleBuilding);
    }

    public static function clearExampleBuilding(Building $building)
    {
        /** @var InputSource $inputSource */
        $inputSource = InputSource::findByShort('example-building');

        return BuildingDataService::clearBuildingFromInputSource($building, $inputSource);
    }

    protected static function log($text)
    {
        \Log::debug(__CLASS__.' '.$text);
    }


    /*
    protected static function createOptions(Collection $collection, $value = 'name', $id = 'id', $nullPlaceholder = true)
    {
        $options = [];
        if ($nullPlaceholder) {
            $options[''] = '-';
        }
        foreach ($collection as $item) {
            $options[$item->$id] = $item->$value;
        }

        return $options;
    }
    */
}
