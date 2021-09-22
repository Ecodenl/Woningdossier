<?php

namespace App\Services;

use App\Events\ExampleBuildingChanged;
use App\Helpers\StepHelper;
use App\Models\Building;
use App\Models\BuildingElement;
use App\Models\BuildingFeature;
use App\Models\BuildingService;
use App\Models\Considerable;
use App\Models\Element;
use App\Models\ElementValue;
use App\Models\ExampleBuilding;
use App\Models\ExampleBuildingContent;
use App\Models\InputSource;
use App\Models\Service;
use App\Models\Step;
use App\Models\ToolQuestion;
use App\Models\ToolQuestionCustomValue;
use App\Models\UserEnergyHabit;
use App\Models\UserInterest;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

class ExampleBuildingService
{
    /**
     * Apply an example building on the given building.
     *
     *
     *
     * @param  ExampleBuilding  $exampleBuilding
     * @param  int  $buildYear  Build year for selecting the appropriate example building content
     * @param  Building  $building  Target building to apply to
     * @param  InputSource|null  $inputSource
     * @param  InputSource|null  $initiatingInputSource  The input source starting this action.
     */
    public static function apply(
        ExampleBuilding $exampleBuilding,
        $buildYear,
        Building $building,
        ?InputSource $inputSource = null,
        ?InputSource $initiatingInputSource = null
    ) {
        $inputSource = $inputSource ?? InputSource::findByShort(
                'example-building'
            );
        // unless stated differently: compare to master input values
        $initiatingInputSource = $initiatingInputSource ?? InputSource::findByShort(
                InputSource::MASTER_SHORT
            );
        //self::log($exampleBuilding->id . ", " . $buildYear . ", " . $building->id . ", " . $inputSource->name . ", " . $initiatingInputSource->name);
        $buildingOwner = $building->user;

        // Clear the current example building data
        self::log(
            'Lookup '.$exampleBuilding->name.' for '.$buildYear." (".$inputSource->name.")"
        );
        $contents = $exampleBuilding->getContentForYear($buildYear);
//        dd($exampleBuilding->name, $contents, $buildYear);
        if ( ! $contents instanceof ExampleBuildingContent) {
            // There's nothing to apply
            self::log('No data to apply');

            return;
        }

        $boilerService = Service::where('short', 'boiler')->first();

        // used for throwing the event at the end
        $oldExampleBuilding = $building->exampleBuilding;

        // traverse the contents:
        $exampleData = $contents->content;

        // new: merge-like behavior
        if ($exampleBuilding->isSpecific()) {
            $genericExampleBuilding = ExampleBuilding::generic()->where(
                'building_type_id',
                $exampleBuilding->building_type_id,
            )->first();
            self::log(
                "Example building is specific. Generic counterpart is ".$genericExampleBuilding->name
            );
            $genericContent = $genericExampleBuilding->getContentForYear(
                $buildYear
            );
            if ($genericContent instanceof ExampleBuildingContent) {
                self::log("We merge the contents");
                $exampleData = array_replace_recursive(
                    $exampleData,
                    $genericContent->content
                );
            }
        }

        self::log(
            'Applying Example Building '.$exampleBuilding->name.' ('.$exampleBuilding->id.', '.$contents->build_year.') for input source '.$inputSource->name
        );

        $oldFeatures = [];
        $oldHabits = [];

        // important!
        // A generic example building can be set, while the rest of the
        // quick scan isn't filled yet. Thing is: if nothing has been changed
        // by the user just yet (so the user is filling the tool for the first
        // time from front to back) data will have been set from the first
        // (generic) example building. When selecting a specific example
        // building, we want to override the values ONLY if nothing has been
        // changed further in the tool by the user. Therefore we check if
        // no substeps > 4 have been completed.
        $userAlreadyStartedFilling = $building->completedSubSteps()
                                              ->forInputSource(
                                                  $initiatingInputSource
                                              )
                                              ->where('sub_step_id', '>', 4)
                                              ->count() > 0;

        // Don't do this for the example building, otherwise it might get the old values from other input sources
        // which is unwanted.
        if ($inputSource->short !== InputSource::EXAMPLE_BUILDING) {
            self::log(
                "User already started filling in the tool. We merge that data."
            );
            // Save the features for later. We merge this with the example building contents.
            // Note that $oldFeatures *might* be null in the highly unlikely case.
            /** @var BuildingFeature|null $currentInputSourceFeatures */
            $currentInputSourceFeatures = $building->buildingFeatures(
            )->forInputSource($initiatingInputSource)->first();

            if ($currentInputSourceFeatures instanceof BuildingFeature) {
                if ($userAlreadyStartedFilling) {
                    $oldFeatures = $currentInputSourceFeatures->attributesToArray(
                    );
                    // filter out null values
                    $oldFeatures = array_filter(
                        $oldFeatures,
                        fn($item) => ! is_null($item)
                    );
                } else {
                    $oldFeatures = [
                        'build_year' => $currentInputSourceFeatures->build_year,
                        'surface'    => $currentInputSourceFeatures->surface,
                    ];
                }
            }

            // Copy over some habits
            $currentHabits = $buildingOwner->energyHabit()->forInputSource($initiatingInputSource)->first();
            if ($currentHabits instanceof UserEnergyHabit){
                $oldHabits = $currentHabits->only('amount_gas', 'amount_electricity', 'amount_water');
            }
        }

        self::clearExampleBuilding($building, $inputSource);

        $features = [];

        Log::debug($exampleBuilding);

        foreach ($exampleData as $stepSlug => $dataForStep) {
            self::log('=====');
            self::log('Processing '.$stepSlug);
            self::log('=====');

            foreach ($dataForStep as $subStep => $subStepData) {
                foreach ($subStepData as $columnOrTable => $values) {
                    self::log('-----> '.$stepSlug.' - '.$columnOrTable);

                    if (is_null($values)) {
                        self::log('Skipping '.$columnOrTable.' (empty)');
                        continue;
                    }

                    if ('user_energy_habits' == $columnOrTable) {
                        $values = array_replace_recursive($values, $oldHabits);
                        $buildingOwner->energyHabit()
                                ->forInputSource($inputSource)
                                ->updateOrCreate(
                            ['input_source_id' => $inputSource->id],
                            $values
                        );
                    }
                    if ('element' == $columnOrTable) {
                        // process elements
                        if (is_array($values)) {
                            foreach ($values as $elementId => $elementValueData) {
                                $extra         = null;
                                $elementValues = [];
                                if (is_array($elementValueData)) {
                                    if ( ! array_key_exists(
                                        'element_value_id',
                                        $elementValueData
                                    )) {
                                        // perhaps a nested array (e.g. wood elements)
                                        foreach ($elementValueData as $elementValueDataItem) {
                                            if (is_array(
                                                    $elementValueDataItem
                                                ) && array_key_exists(
                                                    'element_value_id',
                                                    $elementValueDataItem
                                                )) {
                                                $d = ['element_value_id' => (int)$elementValueDataItem['element_value_id']];
                                                if (array_key_exists(
                                                    'extra',
                                                    $elementValueDataItem
                                                )) {
                                                    $d['extra'] = $elementValueDataItem['extra'];
                                                }
                                                $elementValues[] = $d;
                                            } else {
                                                $elementValues[] = ['element_value_id' => (int)$elementValueDataItem];
                                            }
                                        }
                                    } else {
                                        if (array_key_exists(
                                            'element_value_id',
                                            $elementValueData
                                        )) {
                                            $d = ['element_value_id' => (int)$elementValueData['element_value_id']];
                                            if (array_key_exists(
                                                'extra',
                                                $elementValueData
                                            )) {
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
                                        $extra           = array_key_exists(
                                            'extra',
                                            $elementValue
                                        ) ? $elementValue['extra'] : null;
                                        $buildingElement = new BuildingElement(
                                            ['extra' => $extra]
                                        );
                                        $buildingElement->inputSource(
                                        )->associate($inputSource);
                                        $buildingElement->element()->associate(
                                            $element
                                        );
                                        $buildingElement->building()->associate(
                                            $building
                                        );

                                        if (isset($elementValue['element_value_id'])) {
                                            $elementValue = $element->values(
                                            )->where(
                                                'id',
                                                $elementValue['element_value_id']
                                            )->first();

                                            if ($elementValue instanceof ElementValue) {
                                                $buildingElement->elementValue(
                                                )->associate($elementValue);
                                            }
                                        }

                                        $buildingElement->save();
                                        self::log(
                                            'Update or creating building element '.json_encode(
                                                $buildingElement->toArray()
                                            )
                                        );
                                    }
                                }
                            }
                        }
                    }

                    if ('building_ventilations' == $columnOrTable) {
                        $building
                            ->buildingVentilations()
                            ->forInputSource($inputSource)
                            ->updateOrCreate(
                                ['input_source_id' => $inputSource->id],
                                $values
                            );
                    }

                    if ('service' == $columnOrTable) {
                        // process elements
                        if (is_array($values)) {
                            foreach ($values as $serviceId => $serviceValueData) {
                                $extra = null;
                                // note: in the case of solar panels the service_value_id can be null!!
                                if (is_array($serviceValueData)) {
                                    if ( ! array_key_exists(
                                        'service_value_id',
                                        $serviceValueData
                                    )) {
                                        self::log(
                                            'Service ID '.$serviceId.': no service_value_id -> service_value_id set to NULL'
                                        );
                                        $serviceValueId = null;
                                    } else {
                                        $serviceValueId = (int)$serviceValueData['service_value_id'];
                                    }
                                    if (array_key_exists(
                                        'extra',
                                        $serviceValueData
                                    )) {
                                        $extra = $serviceValueData['extra'];
                                    }
                                } else {
                                    $serviceValueId = (int)$serviceValueData;
                                }
                                $service = Service::find($serviceId);
                                if ($service instanceof Service) {
                                    // try to obtain an existing service
                                    $existingBuildingService = BuildingService::forMe(
                                        $building->user
                                    )
                                                                              ->forInputSource(
                                                                                  $inputSource
                                                                              )
                                                                              ->where(
                                                                                  'service_id',
                                                                                  $serviceId
                                                                              )
                                                                              ->first(
                                                                              );

                                    // see if it already exists, if so we need to add data to that service

                                    // this is for example the case with the hr boiler, data is added on general-data and on the hr page itself
                                    // but this can only be saved under one row, so we have to update it
                                    if ($existingBuildingService instanceof BuildingService) {
                                        $buildingService = $existingBuildingService;
                                    } else {
                                        $buildingService = new BuildingService(
                                        );
                                        $buildingService->inputSource(
                                        )->associate($inputSource);
                                        $buildingService->service()->associate(
                                            $service
                                        );
                                        $buildingService->building()->associate(
                                            $building
                                        );
                                    }

                                    if (is_array($extra)) {
                                        if ($boilerService->id == $serviceId) {
                                            $extra = ['date' => $extra['date']];
                                        }
                                        $buildingService->extra = $extra;
                                    }

                                    if ( ! is_null($serviceValueId)) {
                                        $serviceValue = $service->values(
                                        )->where('id', $serviceValueId)->first(
                                        );
                                        $buildingService->serviceValue(
                                        )->associate($serviceValue);
                                    }

                                    $buildingService->save();

                                    self::log(
                                        'Update or creating building service '.json_encode(
                                            $buildingService->toArray()
                                        )
                                    );
                                }
                            }
                        }
                    }
                    if ('building_features' == $columnOrTable) {
                        $features = array_replace_recursive($features, $values);
                        if(empty($features['surface'] ?? null)){
                            unset($features['surface']);
                        }
                        if (empty($features['build_year'] ?? null)){
                            unset($features['build_year']);
                        }
                    }
                    if ('building_paintwork_statuses' == $columnOrTable) {
                        $statusId        = Arr::get(
                            $values,
                            'paintwork_status_id'
                        );
                        $woodRotStatusId = Arr::get(
                            $values,
                            'wood_rot_status_id'
                        );

                        if (empty($statusId) || empty($woodRotStatusId)) {
                            self::log(
                                'Skipping paintwork status as the paint or wood rot (or both) status is empty'
                            );
                            continue;
                        }

                        $building->currentPaintworkStatus()->forInputSource(
                            $inputSource
                        )->updateOrCreate(
                            ['input_source_id' => $inputSource->id],
                            $values
                        );
                        //continue;
                    }
                    if ('building_insulated_glazings' == $columnOrTable) {
                        foreach ($values as $measureApplicationId => $glazingData) {
                            $glazingData['measure_application_id'] = $measureApplicationId;

                            // the value was stored inside the insulated_glazing_id key, however this changed to insulating_glazing_id.
                            // recent updated example buildings will have the new key, old ones wont.
                            // so if the insulating_glazing_id does not exist, we will set the old one.
                            if ( ! array_key_exists(
                                'insulating_glazing_id',
                                $glazingData
                            )) {
                                $glazingData['insulating_glazing_id'] = $glazingData['insulated_glazing_id'];
                            }

                            $building->currentInsulatedGlazing(
                            )->forInputSource($inputSource)->updateOrCreate(
                                [
                                    'input_source_id' => $inputSource->id,
                                    'measure_application_id' => $glazingData['measure_application_id'],
                                ],
                                $glazingData
                            );

                            self::log(
                                'Update or creating building insulated glazing '.json_encode(
                                    $building->currentInsulatedGlazing()
                                             ->forInputSource($inputSource)
                                             ->where('measure_application_id', '=', $glazingData['measure_application_id'])
                                             ->first()
                                             ->toArray()
                                )
                            );
                        }
                    }
                    if ('building_roof_types' == $columnOrTable) {
                        foreach ($values as $roofTypeId => $buildingRoofTypeData) {
                            $buildingRoofTypeData['roof_type_id'] = $roofTypeId;

                            if (isset($buildingRoofTypeData['roof_surface']) && (int)$buildingRoofTypeData['roof_surface'] > 0) {
                                $building->roofTypes()->forInputSource(
                                    $inputSource
                                )->updateOrCreate(
                                    ['input_source_id' => $inputSource->id],
                                    $buildingRoofTypeData
                                );

                                self::log(
                                    'Update or creating building rooftype '.json_encode(
                                        $building->roofTypes()->forInputSource(
                                            $inputSource
                                        )->first()->toArray()
                                    )
                                );
                            } else {
                                self::log(
                                    'Not saving building rooftype because surface is 0'
                                );
                            }
                        }
                    }
                    if ('building_pv_panels' == $columnOrTable) {

                        $toolQuestion = ToolQuestion::findByShort('has-solar-panels');
                        if ((int) ($values['number'] ?? 0) > 0){
                            /** @var ToolQuestion $toolQuestion */
                            // set to  yes
                            $toolQuestionCustomValue = $toolQuestion->toolQuestionCustomValues()->where('short', '=', 'yes')->first();
                            $building->toolQuestionAnswers()
                                     ->forInputSource($inputSource)
                                     ->updateOrCreate([
                                         'tool_question_id' => $toolQuestion->id,
                                         'input_source_id' => $inputSource->id,
                                     ], [
                                         'tool_question_custom_value_id' => $toolQuestionCustomValue->id,
                                         'answer' => $toolQuestionCustomValue->short,
                                     ]);
                        }

                        $building->pvPanels()->forInputSource(
                            $inputSource
                        )->updateOrCreate(
                            ['input_source_id' => $inputSource->id],
                            $values
                        );
                        self::log(
                            'Update or creating building pv_panels '.json_encode(
                                $building->pvPanels()->forInputSource(
                                    $inputSource
                                )->first()->toArray()
                            )
                        );
                    }
                    if ('building_heaters' == $columnOrTable) {
                        $building->heater()->forInputSource(
                            $inputSource
                        )->updateOrCreate(
                            ['input_source_id' => $inputSource->id],
                            $values
                        );
                        self::log(
                            'Update or creating building heater '.json_encode(
                                $building->heater()->forInputSource(
                                    $inputSource
                                )->first()->toArray()
                            )
                        );
                    }
                    if ('considerables' == $columnOrTable){
                        foreach($values as $modelClass => $modelConsideration){
                            foreach($modelConsideration as $id => $considering){
                                $considering = ($considering == 1);
                                self::log("Building " . $building->id . " Setting consideration for user " . $building->user->id . " for " . $modelClass . " (" . $id . ") to " . ((int) $considering));
                                $userConsideration = new Considerable([
                                    'input_source_id' => $inputSource->id,
                                    'considerable_type' => $modelClass,
                                    'considerable_id' => $id,
                                    'is_considering' => $considering,
                                ]);
                                $building->user->considerables($modelClass)
                                    ->forInputSource($inputSource)
                                    ->save($userConsideration);
                            }
                        }
                    }
                    // deprecated in favor of considerables.
                    if ('user_interests' == $columnOrTable){
                        foreach($values as $modelClass => $modelInterest){
                            foreach($modelInterest as $id => $interest){
                                $interestId = $interest['interest_id'] ?? null;

                                if (!is_null($interestId)){
                                    self::log("Building " . $building->id . " Setting interest for user " . $building->user->id . " for " . $modelClass . " (" . $id . ") to " . $interestId);
                                    $userInterest = new UserInterest([
                                        'input_source_id' => $inputSource->id,
                                        'interested_in_type' => $modelClass,
                                        'interested_in_id' => $id,
                                        'interest_id' => $interestId,
                                    ]);
                                    $building->user->userInterests()
                                                   ->allInputSources()
                                                   ->save($userInterest);
                                }
                            }
                        }
                    }
                    if ('tool_question_answers' == $columnOrTable) {
                        foreach ($values as $questionShort => $answers) {
                            if ( ! is_array($answers)) {
                                $answers = [$answers];
                            }
                            /** @var ToolQuestion $toolQuestion */
                            $toolQuestion = ToolQuestion::findByShort(
                                $questionShort
                            );
                            if ($toolQuestion instanceof ToolQuestion) {
                                foreach ($answers as $answer) {
                                    $customValue = $toolQuestion->toolQuestionCustomValues(
                                    )->where('short', '=', $answer)->first();
                                    $input       = ['answer' => $answer];
                                    if ($customValue instanceof ToolQuestionCustomValue) {
                                        $input['tool_question_custom_value_id'] = $customValue->id;
                                    }

                                    $building->toolQuestionAnswers()
                                             ->forInputSource($inputSource)
                                             ->updateOrCreate(
                                                 [
                                                     'input_source_id'  => $inputSource->id,
                                                     'tool_question_id' => $toolQuestion->id,
                                                 ],
                                                 $input
                                             );
                                }
                            }
                        }
                    }
                }
            }
        }

//        self::log('processing features '.json_encode($features));

        // replace particular features with the old features.
        $features = array_replace_recursive($features, $oldFeatures);

        $buildingFeatures = new BuildingFeature($features);
        $buildingFeatures->buildingType()->associate(
            $exampleBuilding->buildingType
        );
        $buildingFeatures->inputSource()->associate($inputSource);
        $buildingFeatures->building()->associate($building);
        $buildingFeatures->save();

        self::log(
            'Update or creating building features '.json_encode(
                $buildingFeatures->toArray()
            )
        );

        // Get all expert tool steps and complete them for this building + input source
        $stepsToComplete = Step::whereDoesntHave('parentStep')
                               ->whereDoesntHave('subSteps')
                               ->get();

        foreach ($stepsToComplete as $stepToComplete) {
            StepHelper::complete($stepToComplete, $building, $inputSource);
        }

        ExampleBuildingChanged::dispatch(
            $building,
            $oldExampleBuilding,
            $exampleBuilding
        );
    }

    public static function clearExampleBuilding(
        Building $building,
        ?InputSource $inputSource = null
    ) {
        /** @var InputSource $inputSource */
        $inputSource = $inputSource ?? InputSource::findByShort(
                'example-building'
            );

        Log::debug("Clearing example building for input source " . $inputSource->short);

        return BuildingDataService::clearBuildingFromInputSource(
            $building,
            $inputSource
        );
    }

    protected static function log($text)
    {
        Log::debug(__CLASS__.' '.$text);
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
