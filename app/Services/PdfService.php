<?php

namespace App\Services;

use App\Helpers\PdfHelper;
use App\Models\BuildingCoachStatus;
use App\Models\BuildingElement;
use App\Models\BuildingFeature;
use App\Models\BuildingHeater;
use App\Models\BuildingInsulatedGlazing;
use App\Models\BuildingPaintworkStatus;
use App\Models\BuildingPvPanel;
use App\Models\BuildingRoofType;
use App\Models\BuildingService;
use App\Models\ElementValue;
use App\Models\EnergyLabel;
use App\Models\FacadeDamagedPaintwork;
use App\Models\FacadePlasteredSurface;
use App\Models\FacadeSurface;
use App\Models\MeasureApplication;
use App\Models\PrivateMessage;
use App\Models\RoofType;
use App\Models\User;
use App\Models\UserEnergyHabit;
use App\Models\UserInterest;
use App\Scopes\CooperationScope;
use App\Scopes\GetValueScope;
use Illuminate\Support\Collection;

class PdfService {

    public static function totalReportForUser($user)
    {


            $headers = [
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.created-at'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.status'),

                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.allow-access'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.associated-coaches'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.first-name'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.last-name'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.email'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.phonenumber'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.mobilenumber'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.street'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.house-number'),

                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.zip-code'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.city'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.building-type'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.build-year'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.example-building'),
            ];

        // get the content structure of the whole tool.
        $structure = PdfHelper::getPdfStructure();


        $cooperation = $user->cooperations()->first();

        // build the header structure, we will set those in the csv and use it later on to get the answers from the users.
        // unfortunately we cant array dot the structure since we only need the labels
        foreach ($structure as $stepSlug => $stepStructure) {
            foreach ($stepStructure as $tableWithColumnOrAndId => $contents) {
                if ($tableWithColumnOrAndId == 'calculations') {

                    // because there is a roof type, we have to loop once more..
                    if ($stepSlug == 'roof-insulation') {
                        foreach ($contents as $roofType => $roofTypeContent) {
                            if (array_key_exists('measures', $roofTypeContent)) {
                                foreach ($roofTypeContent['measures'] as $nonTranslatedMeasure => $calculationMeasureContent) {
                                    $contents[$roofType]['measures'][$nonTranslatedMeasure] = $calculationMeasureContent['label'] ?? 'No label found';
                                }
                            }
                        }
                    }
                    if (array_key_exists('measures', $contents)) {
                        foreach ($contents['measures'] as $nonTranslatedMeasure => $calculationMeasureContent) {
                            $contents['measures'][$nonTranslatedMeasure] = $calculationMeasureContent['label'] ?? 'No label found';
                        }
                    }

                    $calculationContents = \Illuminate\Support\Arr::dot($contents, $stepSlug.'.calculation.');

                    $headers = array_merge($headers, $calculationContents);

                } else {
                    $headers[$stepSlug.'.'.$tableWithColumnOrAndId] = $contents['label'];
                }

            }
        }

//        dd($headers);
        $rows[] = $headers;

        // for each user we create a new row.
        $row = [];

        // collect basic info from a user.
        $building   = $user->building;
        $buildingId = $building->id;

        /** @var Collection $conversationRequestsForBuilding */
        $conversationRequestsForBuilding = PrivateMessage::withoutGlobalScope(new CooperationScope)
                                                         ->conversationRequestByBuildingId($building->id)
                                                         ->where('to_cooperation_id', $cooperation->id)->get();

        $createdAt           = $user->created_at;
        $buildingStatus      = BuildingCoachStatus::getCurrentStatusForBuildingId($building->id);
        $allowAccess         = $conversationRequestsForBuilding->contains('allow_access', true) ? 'Ja' : 'Nee';
        $connectedCoaches    = BuildingCoachStatus::getConnectedCoachesByBuildingId($building->id);
        $connectedCoachNames = [];

        // get the names from the coaches and add them to a array
        foreach ($connectedCoaches->pluck('coach_id') as $coachId) {
            array_push($connectedCoachNames, User::find($coachId)->getFullName());
        }
        // implode it.
        $connectedCoachNames = implode($connectedCoachNames, ', ');

        $firstName    = $user->first_name;
        $lastName     = $user->last_name;
        $email        = $user->email;
        $phoneNumber  = "'".$user->phone_number;
        $mobileNumber = $user->mobile;

        $street     = $building->street;
        $number     = $building->number;
        $city       = $building->city;
        $postalCode = $building->postal_code;

        // get the building features from the resident
        $buildingFeatures = $building
            ->buildingFeatures()
            ->withoutGlobalScope(GetValueScope::class)
            ->residentInput()
            ->first();

        $buildingType    = $buildingFeatures->buildingType->name ?? '';
        $buildYear       = $buildingFeatures->build_year ?? '';
        $exampleBuilding = $building->exampleBuilding->name ?? '';

        $row = [
            $createdAt, $buildingStatus, $allowAccess, $connectedCoachNames,
            $firstName, $lastName, $email, $phoneNumber, $mobileNumber,
            $street, $number, $postalCode, $city,
            $buildingType, $buildYear, $exampleBuilding,
        ];

        $i = 0;
        $calculateData = CsvService::getCalculateData($building, $user);

        // loop through the headers
        foreach ($headers as $tableWithColumnOrAndIdKey => $translatedInputName) {
            if (is_string($tableWithColumnOrAndIdKey)) {


                // explode it so we can do stuff with it.
                $tableWithColumnOrAndId = explode('.', $tableWithColumnOrAndIdKey);

                // collect some basic info
                // which will apply to (most) cases.
                $step       = $tableWithColumnOrAndId[0];
                $table      = $tableWithColumnOrAndId[1];
                $columnOrId = $tableWithColumnOrAndId[2];

                // determine what column we need to query on to get the results for the user.
                /* @note this will work in most cases, if not the variable will be set again in a specific case. */
                if (\Schema::hasColumn($table, 'building_id')) {
                    $whereUserOrBuildingId = [['building_id', '=', $buildingId]];
                } else {
                    $whereUserOrBuildingId = [['user_id', '=', $user->id]];
                }




                // handle the calculation table.
                // No its not a table, but we treat it as in the structure array.
                if ($table == 'calculation') {
                    // works in most cases, otherwise they will be renamed etc.

                    // ?? null for dev, should be removed.

                    $calculationResultType = $tableWithColumnOrAndId[2];
                    $column      = $tableWithColumnOrAndId[3] ?? null;
                    $benefitType = $tableWithColumnOrAndId[4] ?? null;

                    // the benefitType can be costs, size_collector
                    $benefitTypeHasToBeTranslated = !empty($benefitType) && !in_array($benefitType, ['costs', 'year']);

                    switch ($step) {
                        // roof insulation is a bit different, we have to deal with the roof type.
                        case 'roof-insulation':
                            $roofCategory = $tableWithColumnOrAndId[2];
                            $calculationResultType = $tableWithColumnOrAndId[3];
                            $column = $tableWithColumnOrAndId[4] ?? null;
                            $benefitType = $tableWithColumnOrAndId[5] ?? null;


                            // the benefitType can be costs, size_collector
                            $benefitTypeHasToBeTranslated = !empty($benefitType) && !in_array($benefitType, ['costs', 'year']);

                            if ($benefitTypeHasToBeTranslated) {
//                                dd($tableWithColumnOrAndId, $column, $benefitType, $translatedInputName);
                                $row[$step]['calculation'][$roofCategory][$calculationResultType][$translatedInputName] = $calculateData[$step][$roofCategory][$column][$benefitType] ?? '';
                            } else {
                                $row[$step]['calculation'][$roofCategory][$calculationResultType][$translatedInputName] = $calculateData[$step][$roofCategory][$column] ?? '';
                            }

                            break;
                        default:

                            if ($benefitTypeHasToBeTranslated) {
                                $row[$step]['calculation'][$calculationResultType][$translatedInputName] = $calculateData[$step][$column][$benefitType] ?? '';
                            } else {
                                $row[$step]['calculation'][$calculationResultType][$translatedInputName] = $calculateData[$step][$column] ?? '';
                            }


                            break;

                    }

                }

                // handle the building_features table and its columns.
                if ($table == 'building_features') {

                    $buildingFeature = BuildingFeature::withoutGlobalScope(GetValueScope::class)->where($whereUserOrBuildingId)->first();

                    if ($buildingFeature instanceof BuildingFeature) {

                        switch ($columnOrId) {
                            case 'roof_type_id':
                                $row[$step]['filled-data'][$translatedInputName] = $buildingFeature->roofType instanceof RoofType ? $buildingFeature->roofType->name : '';
                                break;
                            case 'building_type_id':
                                $row[$step][$columnOrId] = $buildingFeature->buildingType->name ?? '';
                                break;
                            case 'energy_label_id':
                                $row[$step]['filled-data'][$translatedInputName] = $buildingFeature->energyLabel instanceof EnergyLabel ? $buildingFeature->energyLabel->name : '';
                                break;
                            case 'facade_damaged_paintwork_id':
                                $row[$step]['filled-data'][$translatedInputName] = $buildingFeature->damagedPaintwork instanceof FacadeDamagedPaintwork ? $buildingFeature->damagedPaintwork->name : '';
                                break;
                            case 'facade_plastered_painted':
                                $possibleAnswers = [
                                    1 => \App\Helpers\Translation::translate('general.options.yes.title'),
                                    2 => \App\Helpers\Translation::translate('general.options.no.title'),
                                    3 => \App\Helpers\Translation::translate('general.options.unknown.title'),
                                ];

                                $row[$step]['filled-data'][$translatedInputName] = $possibleAnswers[$buildingFeature->facade_plastered_painted] ?? '';
                                break;
                            case 'facade_plastered_surface_id':
                                $row[$step]['filled-data'][$translatedInputName] = $buildingFeature->plasteredSurface instanceof FacadePlasteredSurface ? $buildingFeature->plasteredSurface->name : '';
                                break;
                            case 'monument':
                                $possibleAnswers                              = [
                                    1 => \App\Helpers\Translation::translate('general.options.yes.title'),
                                    2 => \App\Helpers\Translation::translate('general.options.no.title'),
                                    0 => \App\Helpers\Translation::translate('general.options.unknown.title'),
                                ];
                                $row[$step]['filled-data'][$translatedInputName] = $possibleAnswers[$buildingFeature->monument] ?? '';
                                break;
                            case 'wall_joints':
                                $row[$step]['filled-data'][$translatedInputName] = $buildingFeature->wallJoints instanceof FacadeSurface ? $buildingFeature->wallJoints->name : '';
                                break;
                            case 'contaminated_wall_joints':
                                $row[$step]['filled-data'][$translatedInputName] = $buildingFeature->contaminatedWallJoints instanceof FacadeSurface ? $buildingFeature->contaminatedWallJoints->name : '';
                                break;
                            default:
                                // the column does not need a relationship, so just get the column
                                $row[$step]['filled-data'][$translatedInputName] = $buildingFeature->$columnOrId ?? '';
                                break;
                        }
                    } else {
                        $row[$step]['filled-data'][$translatedInputName] = '';
                    }
                }

                // handle the building_roof_types table and its columns.
                if ($table == 'building_roof_types') {
                    $roofTypeId = $columnOrId;
                    $column     = $tableWithColumnOrAndId[3];

                    $buildingRoofType = BuildingRoofType::withoutGlobalScope(GetValueScope::class)
                                                        ->where('roof_type_id', $roofTypeId)
                                                        ->where($whereUserOrBuildingId)
                                                        ->first();

                    if ($buildingRoofType instanceof BuildingRoofType) {

                        switch ($column) {
                            case 'element_value_id':
                                $row[$step]['filled-data'][$translatedInputName] = $buildingRoofType->elementValue instanceof ElementValue ? $buildingRoofType->elementValue->value : '';
                                break;
                            case 'building_heating_id':
                                $row[$step]['filled-data'][$translatedInputName] = $buildingRoofType->heating instanceof buildingHeater ? $buildingRoofType->heating->name : '';
                                break;
                            case 'extra.measure_application_id':
                                $extraIsArray                                 = is_array($buildingRoofType->extra);
                                $measureApplicationId                         = $extraIsArray ? $buildingRoofType->extra['measure_application_id'] ?? null : null;
                                $row[$step]['filled-data'][$translatedInputName] = is_null($measureApplicationId) ? '' : MeasureApplication::find($measureApplicationId)->measure_name;
                                break;
                            default:
                                // check if we need to get data from the extra column
                                if (stristr($tableWithColumnOrAndIdKey, 'extra')) {
                                    $extraKey                                     = explode('extra.',
                                        $tableWithColumnOrAndIdKey)[1];
                                    $row[$step]['filled-data'][$translatedInputName] = $buildingRoofType->extra[$extraKey] ?? '';
                                } else {
                                    $row[$step]['filled-data'][$translatedInputName] = $buildingRoofType->$column ?? '';
                                }
                                break;

                        }
                    } else {
                        $row[$step]['filled-data'][$translatedInputName] = '';
                    }
                }

                // handle the user_interest table and its columns.
                if (in_array($table, ['user_interest', 'user_interests'])) {
                    if ($step == 'insulated-glazing') {
                        $interestInType = 'measure_application';
                        $interestInId   = $tableWithColumnOrAndId[2];
                    } else {
                        $interestInType = $columnOrId;
                        $interestInId   = $tableWithColumnOrAndId[3];
                    }

                    $userInterest = UserInterest::withoutGlobalScope(GetValueScope::class)
                                                ->where($whereUserOrBuildingId)
                                                ->where('interested_in_id', $interestInId)
                                                ->where('interested_in_type', $interestInType)
                                                ->residentInput()->first();


                    $row[$step]['filled-data'][$translatedInputName] = $userInterest->interest->name ?? '';
                }

                // handle the element and service tables.
                if (in_array($table, ['element', 'service'])) {
                    $whereUserOrBuildingId = [['building_id', '=', $buildingId]];
                    $elementOrServiceId    = $columnOrId;
                    switch ($table) {
                        case 'element':
                            /** @var BuildingElement $element */
                            $buildingElement = BuildingElement::withoutGlobalScope(GetValueScope::class)
                                                              ->where($whereUserOrBuildingId)
                                                              ->where('element_id', $elementOrServiceId)
                                                              ->residentInput()->first();

                            if ($buildingElement instanceof BuildingElement) {
                                // check if we need to get data from the extra column
                                if (stristr($tableWithColumnOrAndIdKey, 'extra')) {
                                    $extraKey = explode('extra.', $tableWithColumnOrAndIdKey)[1];

                                    $row[$step]['filled-data'][$translatedInputName] = is_array($buildingElement->extra) ? $buildingElement->extra[$extraKey] ?? '' : '';
                                } else {
                                    $row[$step]['filled-data'][$translatedInputName] = $buildingElement->elementValue->value ?? '';
                                }
                            } else {
                                // always set defaults
                                $row[$step]['filled-data'][$translatedInputName] = '';
                            }
                            break;
                        case 'service':

                            $buildingService = BuildingService::withoutGlobalScope(GetValueScope::class)
                                                              ->where($whereUserOrBuildingId)
                                                              ->where('service_id', $elementOrServiceId)
                                                              ->residentInput()->first();
                            if ($buildingService instanceof BuildingService) {

                                // check if we need to get data from the extra column
                                if (stristr($tableWithColumnOrAndIdKey, 'extra')) {
                                    $extraKey     = explode('extra.', $tableWithColumnOrAndIdKey)[1];
                                    $extraIsArray = is_array($buildingService->extra);

                                    // if is array, try to get the answer from the extra column, does the key not exist set a default value.
                                    $row[$step]['filled-data'][$translatedInputName] = $extraIsArray ? $buildingService->extra[$extraKey] ?? '' : '';
                                } else {
                                    $row[$step]['filled-data'][$translatedInputName] = $buildingService->serviceValue->value ?? '';
                                }
                            } else {
                                // always set defaults
                                $row[$step]['filled-data'][$translatedInputName] = '';
                            }

                    }
                }

                // handle the building_insulated_glazing table and its columns.
                if ($table == 'building_insulated_glazings') {
                    $measureApplicationId = $columnOrId;
                    $column               = $tableWithColumnOrAndId[3];

                    /** @var BuildingInsulatedGlazing $buildingInsulatedGlazing */
                    $buildingInsulatedGlazing = BuildingInsulatedGlazing::withoutGlobalScope(GetValueScope::class)
                                                                        ->where($whereUserOrBuildingId)
                                                                        ->where('measure_application_id',
                                                                            $measureApplicationId)
                                                                        ->residentInput()->first();

                    if ($buildingInsulatedGlazing instanceof BuildingInsulatedGlazing) {
                        switch ($column) {
                            case 'insulated_glazing_id':
                                $row[$step]['filled-data'][$translatedInputName] = $buildingInsulatedGlazing->insulatedGlazing->name ?? '';
                                break;
                            case 'building_heating_id':
                                $row[$step]['filled-data'][$translatedInputName] = $buildingInsulatedGlazing->buildingHeating->name ?? '';
                                break;
                            default:
                                $row[$step]['filled-data'][$translatedInputName] = $buildingInsulatedGlazing->$column ?? '';
                                break;

                        }
                    } else {
                        $row[$step]['filled-data'][$translatedInputName] = '';
                    }
                }

                // handle the building_pv_panels table and its column
                if ($table == 'building_pv_panels') {
                    $column = $columnOrId;

                    /** @var BuildingPvPanel $buildingPvPanel */
                    $buildingPvPanel = BuildingPvPanel::withoutGlobalScope(GetValueScope::class)
                                                      ->where($whereUserOrBuildingId)
                                                      ->residentInput()->first();

                    if ($buildingPvPanel instanceof BuildingPvPanel) {
                        switch ($column) {
                            case 'pv_panel_orientation_id':
                                $row[$step]['filled-data'][$translatedInputName] = $buildingPvPanel->orientation->name ?? '';
                                break;
                            default:
                                $row[$step]['filled-data'][$translatedInputName] = $buildingPvPanel->$column ?? '';
                                break;
                        }
                    } else {
                        $row[$step]['filled-data'][$translatedInputName] = '';
                    }
                }

                // handle the building_heaters table and its column
                if ($table == 'building_heaters') {
                    $column = $columnOrId;

                    /** @var buildingHeater $buildingHeater */
                    $buildingHeater = BuildingHeater::withoutGlobalScope(GetValueScope::class)
                                                    ->where($whereUserOrBuildingId)
                                                    ->residentInput()->first();

                    if ($buildingHeater instanceof BuildingHeater) {
                        switch ($column) {
                            case 'pv_panel_orientation_id':
                                $row[$step]['filled-data'][$translatedInputName] = $buildingHeater->orientation->name ?? '';
                                break;
                            default:
                                $row[$step]['filled-data'][$translatedInputName] = $buildingHeater->$column ?? '';
                                break;
                        }
                    } else {
                        $row[$step]['filled-data'][$translatedInputName] = '';
                    }
                }

                // handle the user_energy_habits table and its column
                if ($table == 'user_energy_habits') {
                    $column = $columnOrId;

                    /** @var UserEnergyHabit $userEnergyHabit */
                    $userEnergyHabit = UserEnergyHabit::withoutGlobalScope(GetValueScope::class)
                                                      ->where($whereUserOrBuildingId)
                                                      ->residentInput()->first();

                    if ($userEnergyHabit instanceof UserEnergyHabit) {
                        switch ($column) {
                            case 'cook_gas':
                                $radiobuttonsYesNo = [
                                    1 => __('woningdossier.cooperation.radiobutton.yes'),
                                    2 => __('woningdossier.cooperation.radiobutton.no'),
                                ];
                                $row[$step]['filled-data'][$translatedInputName] = $radiobuttonsYesNo[$userEnergyHabit->cook_gas] ?? '';
                                break;
                            case 'water_comfort_id':
                                $row[$step]['filled-data'][$translatedInputName] = $userEnergyHabit->comfortLevelTapWater->name ?? '';
                                break;
                            case 'heating_first_floor':
                                $row[$step]['filled-data'][$translatedInputName] = $userEnergyHabit->heatingFirstFloor->name ?? '';
                                break;
                            case 'heating_second_floor':
                                $row[$step]['filled-data'][$translatedInputName] = $userEnergyHabit->heatingSecondFloor->name ?? '';
                                break;
                            default:
                                $row[$step]['filled-data'][$translatedInputName] = $userEnergyHabit->$column ?? '';
                                break;
                        }
                    } else {
                        $row[$step]['filled-data'][$translatedInputName] = '';
                    }
                }

                // handle the building_paintwork_statuses table and its column
                if ($table == 'building_paintwork_statuses') {
                    $column = $columnOrId;

                    /** @var BuildingPaintworkStatus $buildingPaintworkStatus */
                    $buildingPaintworkStatus = BuildingPaintworkStatus::withoutGlobalScope(GetValueScope::class)
                                                                      ->where($whereUserOrBuildingId)
                                                                      ->residentInput()->first();

                    if ($buildingPaintworkStatus instanceof BuildingPaintworkStatus) {
                        switch ($column) {
                            case 'paintwork_status_id':
                                $row[$step]['filled-data'][$translatedInputName] = $buildingPaintworkStatus->paintworkStatus->name ?? '';
                                break;
                            case 'wood_rot_status_id':
                                $row[$step]['filled-data'][$translatedInputName] = $buildingPaintworkStatus->woodRotStatus->name ?? '';
                                break;
                            default:
                                $row[$step]['filled-data'][$translatedInputName] = $buildingPaintworkStatus->$column ?? '';
                                break;
                        }
                    } else {
                        $row[$step]['filled-data'][$translatedInputName] = '';
                    }
                }
            }
        }

        // no need to merge headers with the rows, we always set defaults so the count will always be the same.
        $rows[] = $row;


        dd('bier', $row);
        return $rows;
    }


}