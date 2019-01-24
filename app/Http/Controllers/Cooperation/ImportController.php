<?php

namespace App\Http\Controllers\Cooperation;

use App\Helpers\HoomdossierSession;
use App\Http\Controllers\Controller;
use App\Models\Element;
use App\Models\ExampleBuilding;
use App\Models\ExampleBuildingContent;
use App\Models\InputSource;
use App\Services\ToolSettingService;
use Illuminate\Http\Request;

class ImportController extends Controller
{
    public function copy(Request $request)
    {
        // answers that are considered to be empty.
        $consideredEmptyAnswers = ['', "", null, 'null', '0.00', '0.0', 0];

        $desiredInputSourceName = $request->get('input_source');

        $tablesWithBuildingIds = [
            'building_elements' => [
                'whereColumn' => 'element_id'
            ],
            'building_services' => [
                'whereColumn' => 'service_id'
            ],
//            'questions_answers',
//            'building_pv_panels',
            'building_roof_types',
//            'building_heaters',
            'building_features',
//            'building_paintwork_statuses',
//            'user_progresses',
//            'building_insulated_glazings',
//            'building_appliances',
//            'building_user_usages',
//            'devices',
        ];

        $tablesWithUserId = [
            'user_action_plan_advices',
            'user_energy_habits',
            'user_interests',
        ];

        // input sources
        $desiredInputSource = InputSource::findByShort($desiredInputSourceName);
        $residentInputSource = InputSource::findByShort('resident');

        $exampleBuilding  = ExampleBuildingContent::find(89);

        $buildingId = HoomdossierSession::getBuilding();
        foreach ($exampleBuilding->content as $stepSlug => $contents) {
            foreach ($contents as $columnOrTable => $values) {

                \Log::debug('-> '.$stepSlug.' + '.$columnOrTable.' <-');

                if (is_null($values)) {
                    \Log::debug('Skipping '.$columnOrTable.' (empty)');
                    continue;
                }
                if ('user_interest' == $columnOrTable) {
                    \Log::debug('Skipping outdated user interests');
                    continue;
                }
                if ('element' == $columnOrTable && $stepSlug == "floor-insulation") {

                    if (is_array($values)) {
                        foreach ($values as $elementId => $elementValueData) {
                            $extraElement = null;
                            if (is_array($elementValueData)) {
                                if (! array_key_exists('element_value_id', $elementValueData)) {
                                    \Log::debug('Skipping element value as there is no element_value_id');
                                    continue;
                                }
                                $elementValueId = (int) $elementValueData['element_value_id'];
                                if (array_key_exists('extra', $elementValueData)) {
                                    $extraElement = $elementValueData['extra'];
                                }
                            } else {
                                $elementValueId = (int) $elementValueData;
                            }

                            $element = Element::find($elementId);
                            if ($element instanceof Element) {
                                $residentInputQuery = \DB::table('building_elements')
                                    ->where('input_source_id', $residentInputSource->id)
                                    ->where('building_id', $buildingId)
                                    ->where('element_id', $elementId);

                                $residentInput = $residentInputQuery->first();

                                $updateElementId = empty($elementId) ? $residentInput->element_id : $elementId;
                                $updateElementValueId = empty($elementValueId) ? $residentInput->element_value_id : $elementValueId;

                                $extraElementResident = json_decode($residentInput->extra, true);

                                // if there is extra data from the example building and the resident does to
                                // then we filter out the considered empty values from the example building
                                // and merge those into the resident his extra array
                                // else just use the example building one.
                                if (is_array($extraElement) && is_array($extraElementResident)) {

                                    // filter the values which are not worth updating, so we dont lose filled in values over null, 0.0, 0.000 etc
                                    $noNullExtraValues = array_filter($extraElement, function ($value, $key) use ($consideredEmptyAnswers) {
                                        return !in_array($value, $consideredEmptyAnswers, true);
                                    }, ARRAY_FILTER_USE_BOTH);

                                    // merge those toes
                                    $updateElementExtra = array_merge($extraElementResident, $noNullExtraValues);

                                } else {
                                    $updateElementExtra = $extraElement;
                                }

                                $updateElementExtra = json_encode($updateElementExtra);
                                // the array we will use to update the building_elements
                                $updateElement = ['element_id' => $updateElementId, 'element_value_id' => $updateElementValueId, 'extra' => $updateElementExtra];
                                $residentInputQuery->update($updateElement);
                            }
                        }
                    }
                }

            }
        }
        dd();


        // TODO: Only to be used in the coach
        // handle the copy for the tables with a building id.
        foreach ($tablesWithBuildingIds as $tableWithBuildingId) {
            // first delete all the resident his input, we dont need it anyway
            $residentInput = \DB::table($tableWithBuildingId)->where('building_id', HoomdossierSession::getBuilding())
                ->where('input_source_id', $residentInputSource->id)->get();

            // get the coach input values
            $desiredInputSourceValues = \DB::table($tableWithBuildingId)->where('building_id', HoomdossierSession::getBuilding())
                ->where('input_source_id', $desiredInputSource->id)->get();
            dd($residentInput->merge($desiredInputSourceValues));


            // get the coach input values
            $desiredInputSourceValues = \DB::table($tableWithBuildingId)->where('building_id', HoomdossierSession::getBuilding())
                ->where('input_source_id', $desiredInputSource->id)->get();

            // change the input source to the resident
            $desiredInputSourceValues->map(function ($desiredInputSourceValue) use ($residentInputSource) {
                $desiredInputSourceValue->input_source_id = $residentInputSource->id;

                return $desiredInputSourceValue;
            });

            foreach ($desiredInputSourceValues as $desiredInputSourceValue) {
                $desiredInputSourceValue = (array) $desiredInputSourceValue;
                unset($desiredInputSourceValue['id']);

                \DB::table($tableWithBuildingId)->insert($desiredInputSourceValue);
            }
        }

        // handle the copy for the tables with a user id instead of a building id
        foreach ($tablesWithUserId as $tableWithUserId) {
            // first delete all the resident his input, we dont need it anyway
            \DB::table($tableWithUserId)->where('user_id', \Auth::id())
                ->where('input_source_id', $residentInputSource->id)->delete();

            // get the coach input values
            $desiredInputSourceValues = \DB::table($tableWithUserId)->where('user_id', \Auth::id())
                ->where('input_source_id', $desiredInputSource->id)->get();

            // change the input source to the resident
            $desiredInputSourceValues->map(function ($desiredInputSourceValue) use ($residentInputSource) {
                $desiredInputSourceValue->input_source_id = $residentInputSource->id;

                return $desiredInputSourceValue;
            });

            foreach ($desiredInputSourceValues as $desiredInputSourceValue) {
                $desiredInputSourceValue = (array) $desiredInputSourceValue;
                unset($desiredInputSourceValue['id']);

                \DB::table($tableWithUserId)->insert($desiredInputSourceValue);
            }
        }

        ToolSettingService::setChanged(HoomdossierSession::getBuilding(), $desiredInputSource->id, false);
        HoomdossierSession::stopUserComparingInputSources();

        return redirect()->back();
    }
}
