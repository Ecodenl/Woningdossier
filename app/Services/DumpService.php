<?php

namespace App\Services;

use App\Calculations\FloorInsulation;
use App\Calculations\Heater;
use App\Calculations\HighEfficiencyBoiler;
use App\Calculations\InsulatedGlazing;
use App\Calculations\RoofInsulation;
use App\Calculations\SolarPanel;
use App\Calculations\WallInsulation;
use App\Helpers\Arr;
use App\Helpers\FileFormats\CsvHelper;
use App\Helpers\NumberFormatter;
use App\Helpers\ToolHelper;
use App\Helpers\Translation;
use App\Models\Building;
use App\Models\BuildingCoachStatus;
use App\Models\BuildingElement;
use App\Models\BuildingFeature;
use App\Models\buildingHeater;
use App\Models\BuildingHeating;
use App\Models\BuildingInsulatedGlazing;
use App\Models\BuildingPaintworkStatus;
use App\Models\BuildingPvPanel;
use App\Models\BuildingRoofType;
use App\Models\BuildingService;
use App\Models\Cooperation;
use App\Models\Element;
use App\Models\ElementValue;
use App\Models\EnergyLabel;
use App\Models\FacadeDamagedPaintwork;
use App\Models\FacadePlasteredSurface;
use App\Models\FacadeSurface;
use App\Models\InputSource;
use App\Models\MeasureApplication;
use App\Models\PrivateMessage;
use App\Models\Question;
use App\Models\Questionnaire;
use App\Models\QuestionOption;
use App\Models\Role;
use App\Models\RoofTileStatus;
use App\Models\RoofType;
use App\Models\Service;
use App\Models\Step;
use App\Models\User;
use App\Models\UserActionPlanAdvice;
use App\Models\UserEnergyHabit;
use App\Models\UserInterest;
use App\Scopes\CooperationScope;
use App\Scopes\GetValueScope;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class DumpService
{
    /**
     * Method to generate a total dump from a user for a specific input source.
     * This dump collects all possible data for a given user for the tool and returns it in an array.
     *
     * @param User $user
     * @param InputSource $inputSource
     * @param bool $anonymized
     * @param bool $withTranslationsForColumns
     * @return array
     */
    public static function totalDump(User $user, InputSource $inputSource, bool $anonymized, bool $withTranslationsForColumns = true): array
    {

        $cooperation = $user->cooperation;
        // get the content structure of the whole tool.
        $structure = ToolHelper::getToolStructure();
        $rows = [];

        if ($anonymized) {
            $headers = [
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.created-at'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.status'),

                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.zip-code'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.city'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.building-type'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.build-year'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.example-building'),
            ];
        } else {
            $headers = [
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.created-at'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.status'),

                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.allow-access'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.associated-coaches'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.first-name'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.last-name'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.email'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.phonenumber'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.street'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.house-number'),

                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.zip-code'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.city'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.building-type'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.build-year'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.example-building'),
            ];
        }


        $leaveOutTheseDuplicates = [
            // hoofddak
            'roof-insulation.building_features.roof_type_id',
            // bewoners, gasverbruik en type ketel
            'high-efficiency-boiler.user_energy_habits.resident_count',
            'high-efficiency-boiler.user_energy_habits.amount_gas',
            // had to be added according to the pdf feedback, so now it will be displayed in the general data and on the high efficiency boiler page.
//            'high-efficiency-boiler.service.5.service_value_id',
            // elektriciteitsverbruik
            'solar-panels.user_energy_habits.amount_electricity',
            // comfort niveau
            'heater.user_energy_habits.water_comfort_id',
            'heater.calculation.production_heat.help',
        ];

        // build the header structure, we will set those in the csv and use it later on to get the answers from the users.
        // unfortunately we cant array dot the structure since we only need the labels
        foreach ($structure as $stepSlug => $stepStructure) {
            // building-detail contains data that is already present in the columns above
            if (!in_array($stepSlug, ['building-detail'])) {
                foreach ($stepStructure as $tableWithColumnOrAndId => $contents) {
                    if ($tableWithColumnOrAndId == 'calculations') {

                        // If you want to go ahead and translate in a different namespace, do it here
                        $deeperContents = \Illuminate\Support\Arr::dot($contents, $stepSlug . '.calculation.');

                        $headers = array_merge($headers, $deeperContents);

                    } else {
                        $headers[$stepSlug . '.' . $tableWithColumnOrAndId] = str_replace([
                            '&euro;', '€'
                        ], ['euro', 'euro'], $contents['label']);
                    }
                }
            }
        }

        foreach ($leaveOutTheseDuplicates as $leaveOut) {
            unset($headers[$leaveOut]);
        }

        if ($withTranslationsForColumns) {
            $rows['translations-for-columns'] = $headers;
        }

        // create a row where we will store the user data
        $row = [];

        // collect basic info from a user.
        $building = $user->building;
        $buildingId = $building->id;

        /** @var Collection $conversationRequestsForBuilding */
        $conversationRequestsForBuilding = PrivateMessage::withoutGlobalScope(new CooperationScope)
            ->conversationRequestByBuildingId($building->id)
            ->where('to_cooperation_id', $cooperation->id)->get();

        $createdAt = optional($user->created_at)->format('Y-m-d');
        $buildingStatus = $building->getMostRecentBuildingStatus()->status->name;
        $allowAccess = $conversationRequestsForBuilding->contains('allow_access', true) ? 'Ja' : 'Nee';
        $connectedCoaches = BuildingCoachStatus::getConnectedCoachesByBuildingId($building->id);
        $connectedCoachNames = [];

        // get the names from the coaches and add them to a array
        foreach ($connectedCoaches->pluck('coach_id') as $coachId) {
            array_push($connectedCoachNames, User::find($coachId)->getFullName());
        }
        // implode it.
        $connectedCoachNames = implode($connectedCoachNames, ', ');

        $firstName = $user->first_name;
        $lastName = $user->last_name;
        $email = $user->account->email;
        $phoneNumber = CsvHelper::escapeLeadingZero($user->phone_number);

        $street = $building->street;
        $number = $building->number;
        $city = $building->city;
        $postalCode = $building->postal_code;


        // get the building features from the resident
        $buildingFeature = $building
            ->buildingFeatures()
            ->forInputSource($inputSource)
            ->first();

        $buildingType = $buildingFeature->buildingType->name ?? '';
        $buildYear = $buildingFeature->build_year ?? '';
        $exampleBuilding = optional($building->exampleBuilding)->isSpecific() ? $building->exampleBuilding->name : '';

        // set the personal userinfo
        if ($anonymized) {
            // set the personal userinfo
            $row[$building->id] = [
                $createdAt, $buildingStatus, $postalCode, $city,
                $buildingType, $buildYear, $exampleBuilding,
            ];
        } else {
            $row[$building->id] = [
                $createdAt, $buildingStatus, $allowAccess, $connectedCoachNames,
                $firstName, $lastName, $email, $phoneNumber,
                $street, $number, $postalCode, $city,
                $buildingType, $buildYear, $exampleBuilding,
            ];
        }

        $calculateData = static::getCalculateData($user, $inputSource);

        // one correction because of bad headers
        if (isset($calculateData['heater']['production_heat']) && !is_array($calculateData['heater']['production_heat'])) {
            if (!isset($calculateData['heater']['production_heat']['title'])) {
                $calculateData['heater']['production_heat'] = ['title' => $calculateData['heater']['production_heat']];
            }
        }


        // loop through the headers
        foreach ($headers as $tableWithColumnOrAndIdKey => $translatedInputName) {
            if (is_string($tableWithColumnOrAndIdKey)) {

                // explode it so we can do stuff with it.
                $tableWithColumnOrAndId = explode('.', $tableWithColumnOrAndIdKey);

                // collect some basic info
                // which will apply to (most) cases.
                $step = $tableWithColumnOrAndId[0];
                $table = $tableWithColumnOrAndId[1];
                $columnOrId = $tableWithColumnOrAndId[2];

                $maybe1 = isset($tableWithColumnOrAndId[3]) ? $tableWithColumnOrAndId[3] : '';
                $maybe2 = isset($tableWithColumnOrAndId[4]) ? $tableWithColumnOrAndId[4] : '';
                //dump("Step: " . $step . " | table: " . $table . " | column or ID: " . $columnOrId . " | column: " . $maybe1 . " | costs or year: " . $maybe2);

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
                    $column = $columnOrId;
                    $costsOrYear = $tableWithColumnOrAndId[3] ?? null;

                    switch ($step) {
                        case 'roof-insulation':
                            $roofCategory = $tableWithColumnOrAndId[2];
                            $column = $tableWithColumnOrAndId[3];
                            $costsOrYear = $tableWithColumnOrAndId[4] ?? null;

                            $calculationResult = is_null($costsOrYear) ? $calculateData['roof-insulation'][$roofCategory][$column] ?? '' : $calculateData['roof-insulation'][$roofCategory][$column][$costsOrYear] ?? '';
                            break;
                        default:
                            $calculationResult = is_null($costsOrYear) ? $calculateData[$step][$column] : $calculateData[$step][$column][$costsOrYear] ?? '';
                            break;
                    }

                    $calculationResult = self::formatFieldOutput($column, $calculationResult, $maybe1, $maybe2);

                    //dump("calculationResult: " . $calculationResult . " for step " . $step);

                    $row[$buildingId][$tableWithColumnOrAndIdKey] = $calculationResult ?? '';
                }

                // handle the building_features table and its columns.
                if ($table == 'building_features') {
                    if ($buildingFeature instanceof BuildingFeature) {

                        switch ($columnOrId) {
                            case 'roof_type_id':
                                $row[$buildingId][$tableWithColumnOrAndIdKey] = $buildingFeature->roofType instanceof RoofType ? $buildingFeature->roofType->name : '';
                                break;

                            case 'building_type_id':
                            case 'build_year':
                                //$row[$buildingId][$tableWithColumnOrAndIdKey] = $buildingFeature->buildingType->name ?? '';
                                break;
                            case 'energy_label_id':
                                $row[$buildingId][$tableWithColumnOrAndIdKey] = $buildingFeature->energyLabel instanceof EnergyLabel ? $buildingFeature->energyLabel->name : '';
                                break;
                            case 'facade_damaged_paintwork_id':
                                $row[$buildingId][$tableWithColumnOrAndIdKey] = $buildingFeature->damagedPaintwork instanceof FacadeDamagedPaintwork ? $buildingFeature->damagedPaintwork->name : '';
                                break;
                            case 'facade_plastered_painted':
                                $possibleAnswers = [
                                    1 => \App\Helpers\Translation::translate('general.options.yes.title'),
                                    2 => \App\Helpers\Translation::translate('general.options.no.title'),
                                    3 => \App\Helpers\Translation::translate('general.options.unknown.title'),
                                ];

                                $row[$buildingId][$tableWithColumnOrAndIdKey] = $possibleAnswers[$buildingFeature->facade_plastered_painted] ?? '';
                                break;
                            case 'facade_plastered_surface_id':
                                $row[$buildingId][$tableWithColumnOrAndIdKey] = $buildingFeature->plasteredSurface instanceof FacadePlasteredSurface ? $buildingFeature->plasteredSurface->name : '';
                                break;
                            case 'monument':
                            case 'cavity_wall':
                                $possibleAnswers = [
                                    1 => \App\Helpers\Translation::translate('general.options.yes.title'),
                                    2 => \App\Helpers\Translation::translate('general.options.no.title'),
                                    0 => \App\Helpers\Translation::translate('general.options.unknown.title'),
                                ];
                                $row[$buildingId][$tableWithColumnOrAndIdKey] = $possibleAnswers[$buildingFeature->monument] ?? '';
                                break;
                            case 'wall_joints':
                                $row[$buildingId][$tableWithColumnOrAndIdKey] = $buildingFeature->wallJoints instanceof FacadeSurface ? $buildingFeature->wallJoints->name : '';
                                break;
                            case 'contaminated_wall_joints':
                                $row[$buildingId][$tableWithColumnOrAndIdKey] = $buildingFeature->contaminatedWallJoints instanceof FacadeSurface ? $buildingFeature->contaminatedWallJoints->name : '';
                                break;
                            case 'window_surface':
                                $row[$buildingId][$tableWithColumnOrAndIdKey] = NumberFormatter::format($buildingFeature->$columnOrId, 2) ?? '';
                                break;
                            default:
                                // the column does not need a relationship, so just get the column
                                $row[$buildingId][$tableWithColumnOrAndIdKey] = $buildingFeature->$columnOrId ?? '';
                                break;
                        }
                    } else {
                        $row[$buildingId][$tableWithColumnOrAndIdKey] = '';
                    }
                }

                // handle the building_roof_types table and its columns.
                if ($table == 'building_roof_types') {
                    $roofTypeId = $columnOrId;
                    //$column     = $tableWithColumnOrAndId[3];
                    $column = $maybe1;

                    $buildingRoofType = BuildingRoofType::where('roof_type_id', $roofTypeId)
                        ->where($whereUserOrBuildingId)
                        ->forInputSource($inputSource)
                        ->first();

                    if ($buildingRoofType instanceof BuildingRoofType) {
                        switch ($column) {
                            case 'element_value_id':
                                $row[$buildingId][$tableWithColumnOrAndIdKey] = $buildingRoofType->elementValue instanceof ElementValue ? $buildingRoofType->elementValue->value : '';
                                break;
                            case 'building_heating_id':
                                $row[$buildingId][$tableWithColumnOrAndIdKey] = $buildingRoofType->buildingHeating instanceof BuildingHeating ? $buildingRoofType->buildingHeating->name : '';
                                break;
                            default:
                                // check if we need to get data from the extra column
                                if (stristr($tableWithColumnOrAndIdKey, 'extra')) {
                                    $extraKey = explode('extra.', $tableWithColumnOrAndIdKey)[1];
                                    if (in_array($extraKey, ['tiles_condition', 'measure_application_id'])) {
                                        $row[$buildingId][$tableWithColumnOrAndIdKey] = $buildingRoofType->extra[$extraKey] ?? '';

                                        if (!empty($buildingRoofType->extra[$extraKey]) && $extraKey == 'tiles_condition') {
                                            $status = RoofTileStatus::find((int)$row[$buildingId][$tableWithColumnOrAndIdKey]);
                                            $row[$buildingId][$tableWithColumnOrAndIdKey] = ($status instanceof RoofTileStatus) ? $status->name : '';
                                        }
                                        if ($extraKey == 'measure_application_id') {
//                                            dd($extraKey, $buildingRoofType, $buildingRoofType->extra[$extraKey], $buildingRoofType->extra[$extraKey] == null && $extraKey == 'measure_application_id');
                                        }
                                        // The measure application id, in this case. can be 0, this means the option: "niet" has been chosen the option is not saved as a measure application
                                        if ($extraKey == 'measure_application_id') {

                                            $measureApplication = MeasureApplication::find((int)$row[$buildingId][$tableWithColumnOrAndIdKey]);
                                            $row[$buildingId][$tableWithColumnOrAndIdKey] = $measureApplication instanceof MeasureApplication ? $measureApplication->measure_name : __('roof-insulation.measure-application.no.title');
                                        }
                                    } else {
                                        // literal
                                        $row[$buildingId][$tableWithColumnOrAndIdKey] = $buildingRoofType->extra[$extraKey] ?? '';
                                    }
                                } else {
                                    $row[$buildingId][$tableWithColumnOrAndIdKey] = $buildingRoofType->$column ?? '';
                                }
                                break;

                        }
                    } else {
                        $row[$buildingId][$tableWithColumnOrAndIdKey] = '';
                    }
                }

                // handle the user_interest table and its columns.
                if (in_array($table, ['user_interest', 'user_interests'])) {
                    if ($step == 'insulated-glazing') {
                        $interestInType = 'measure_application';
                        $interestInId = $tableWithColumnOrAndId[2];
                    } else {
                        $interestInType = $columnOrId;
                        $interestInId = $tableWithColumnOrAndId[3];
                    }

                    $userInterest = UserInterest::where($whereUserOrBuildingId)
                        ->where('interested_in_id', $interestInId)
                        ->where('interested_in_type', $interestInType)
                        ->forInputSource($inputSource)
                        ->first();


                    $row[$buildingId][$tableWithColumnOrAndIdKey] = $userInterest->interest->name ?? '';
                }

                // handle the element and service tables.
                if (in_array($table, ['element', 'service'])) {
                    $whereUserOrBuildingId = [['building_id', '=', $buildingId]];
                    $elementOrServiceId = $columnOrId;
                    switch ($table) {
                        case 'element':
                            /** @var BuildingElement $element */
                            $buildingElement = BuildingElement::where($whereUserOrBuildingId)
                                ->where('element_id', $elementOrServiceId)
                                ->forInputSource($inputSource)
                                ->first();

                            if ($buildingElement instanceof BuildingElement) {
                                // check if we need to get data from the extra column
                                if (stristr($tableWithColumnOrAndIdKey, 'extra')) {
                                    $extraKey = explode('extra.', $tableWithColumnOrAndIdKey)[1];

                                    $row[$buildingId][$tableWithColumnOrAndIdKey] = is_array($buildingElement->extra) ? self::translateExtraValueIfNeeded($buildingElement->extra[$extraKey]) ?? '' : '';
                                } else {
                                    $row[$buildingId][$tableWithColumnOrAndIdKey] = $buildingElement->elementValue->value ?? '';
                                }
                            } else {
                                // always set defaults
                                $row[$buildingId][$tableWithColumnOrAndIdKey] = '';
                            }
                            break;
                        case 'service':
                            $buildingService = BuildingService::where($whereUserOrBuildingId)
                                ->where('service_id', $elementOrServiceId)
                                ->forInputSource($inputSource)
                                ->first();
                            if ($buildingService instanceof BuildingService) {

                                // check if we need to get data from the extra column
                                if (stristr($tableWithColumnOrAndIdKey, 'extra')) {
                                    $extraKey = explode('extra.', $tableWithColumnOrAndIdKey)[1];
                                    $extraIsArray = is_array($buildingService->extra);

                                    // if is array, try to get the answer from the extra column, does the key not exist set a default value.
                                    $row[$buildingId][$tableWithColumnOrAndIdKey] = $extraIsArray ? $buildingService->extra[$extraKey] ?? '' : '';


                                } else {
                                    $row[$buildingId][$tableWithColumnOrAndIdKey] = $buildingService->serviceValue->value ?? '';
                                }
                            } else {
                                // always set defaults
                                $row[$buildingId][$tableWithColumnOrAndIdKey] = '';
                            }

                    }
                }

                // handle the building_insulated_glazing table and its columns.
                if ($table == 'building_insulated_glazings') {
                    $measureApplicationId = $columnOrId;
                    $column = $tableWithColumnOrAndId[3];

                    /** @var BuildingInsulatedGlazing $buildingInsulatedGlazing */
                    $buildingInsulatedGlazing = BuildingInsulatedGlazing::where($whereUserOrBuildingId)
                        ->where('measure_application_id', $measureApplicationId)
                        ->forInputSource($inputSource)
                        ->first();

                    if ($buildingInsulatedGlazing instanceof BuildingInsulatedGlazing) {
                        switch ($column) {
                            case 'insulated_glazing_id':
                                $row[$buildingId][$tableWithColumnOrAndIdKey] = $buildingInsulatedGlazing->insulatedGlazing->name ?? '';
                                break;
                            case 'building_heating_id':
                                $row[$buildingId][$tableWithColumnOrAndIdKey] = $buildingInsulatedGlazing->buildingHeating->name ?? '';
                                break;
                            default:
                                $row[$buildingId][$tableWithColumnOrAndIdKey] = $buildingInsulatedGlazing->$column ?? '';
                                break;

                        }
                    } else {
                        $row[$buildingId][$tableWithColumnOrAndIdKey] = '';
                    }
                }

                // handle the building_pv_panels table and its column
                if ($table == 'building_pv_panels') {
                    $column = $columnOrId;

                    /** @var BuildingPvPanel $buildingPvPanel */
                    $buildingPvPanel = BuildingPvPanel::where($whereUserOrBuildingId)
                        ->forInputSource($inputSource)
                        ->first();

                    if ($buildingPvPanel instanceof BuildingPvPanel) {
                        switch ($column) {
                            case 'pv_panel_orientation_id':
                                $row[$buildingId][$tableWithColumnOrAndIdKey] = $buildingPvPanel->orientation->name ?? '';
                                break;
                            default:
                                $row[$buildingId][$tableWithColumnOrAndIdKey] = $buildingPvPanel->$column ?? '';
                                break;
                        }
                    } else {
                        $row[$buildingId][$tableWithColumnOrAndIdKey] = '';
                    }
                }

                // handle the building_heaters table and its column
                if ($table == 'building_heaters') {
                    $column = $columnOrId;

                    /** @var buildingHeater $buildingHeater */
                    $buildingHeater = BuildingHeater::where($whereUserOrBuildingId)
                        ->forInputSource($inputSource)
                        ->first();

                    if ($buildingHeater instanceof BuildingHeater) {
                        switch ($column) {
                            case 'pv_panel_orientation_id':
                                $row[$buildingId][$tableWithColumnOrAndIdKey] = $buildingHeater->orientation->name ?? '';
                                break;
                            default:
                                $row[$buildingId][$tableWithColumnOrAndIdKey] = $buildingHeater->$column ?? '';
                                break;
                        }
                    } else {
                        $row[$buildingId][$tableWithColumnOrAndIdKey] = '';
                    }
                }

                // handle the user_energy_habits table and its column
                if ($table == 'user_energy_habits') {
                    $column = $columnOrId;

                    /** @var UserEnergyHabit $userEnergyHabit */
                    $userEnergyHabit = UserEnergyHabit::where($whereUserOrBuildingId)
                        ->forInputSource($inputSource)
                        ->first();

                    if ($userEnergyHabit instanceof UserEnergyHabit) {
                        switch ($column) {
                            case 'cook_gas':
                                $radiobuttonsYesNo = [
                                    1 => __('woningdossier.cooperation.radiobutton.yes'),
                                    2 => __('woningdossier.cooperation.radiobutton.no'),
                                ];
                                $row[$buildingId][$tableWithColumnOrAndIdKey] = $radiobuttonsYesNo[$userEnergyHabit->cook_gas] ?? '';
                                break;
                            case 'water_comfort_id':
                                $row[$buildingId][$tableWithColumnOrAndIdKey] = $userEnergyHabit->comfortLevelTapWater->name ?? '';
                                break;
                            case 'heating_first_floor':
                                $row[$buildingId][$tableWithColumnOrAndIdKey] = $userEnergyHabit->heatingFirstFloor->name ?? '';
                                break;
                            case 'heating_second_floor':
                                $row[$buildingId][$tableWithColumnOrAndIdKey] = $userEnergyHabit->heatingSecondFloor->name ?? '';
                                break;
                            default:
                                $row[$buildingId][$tableWithColumnOrAndIdKey] = $userEnergyHabit->$column ?? '';
                                break;
                        }
                    } else {
                        $row[$buildingId][$tableWithColumnOrAndIdKey] = '';
                    }
                }

                // handle the building_paintwork_statuses table and its column
                if ($table == 'building_paintwork_statuses') {
                    $column = $columnOrId;

                    /** @var BuildingPaintworkStatus $buildingPaintworkStatus */
                    $buildingPaintworkStatus = BuildingPaintworkStatus::where($whereUserOrBuildingId)
                        ->forInputSource($inputSource)
                        ->first();

                    if ($buildingPaintworkStatus instanceof BuildingPaintworkStatus) {
                        switch ($column) {
                            case 'paintwork_status_id':
                                $row[$buildingId][$tableWithColumnOrAndIdKey] = $buildingPaintworkStatus->paintworkStatus->name ?? '';
                                break;
                            case 'wood_rot_status_id':
                                $row[$buildingId][$tableWithColumnOrAndIdKey] = $buildingPaintworkStatus->woodRotStatus->name ?? '';
                                break;
                            default:
                                $row[$buildingId][$tableWithColumnOrAndIdKey] = $buildingPaintworkStatus->$column ?? '';
                                break;
                        }
                    } else {
                        $row[$buildingId][$tableWithColumnOrAndIdKey] = '';
                    }
                }
            }
        }

        $rows['user-data'] = $row[$buildingId];
        $rows['calculations'] = $calculateData;

        return $rows;
    }

    /**
     * Return the calculate data for each step, for a user, with its given inputsource.
     *
     *
     * @param User $user
     * @param InputSource $inputSource
     * @return array
     */
    public static function getCalculateData(User $user, InputSource $inputSource): array
    {
        // collect some info about their building
        $building = $user->building;


        /** @var BuildingFeature $buildingFeature */
        $buildingFeature = $building->buildingFeatures()->forInputSource($inputSource)->first();
        $buildingElements = $building->buildingElements()->forInputSource($inputSource)->get();
        $buildingPaintworkStatus = $building->currentPaintworkStatus()->forInputSource($inputSource)->first();
        $buildingRoofTypes = $building->roofTypes()->forInputSource($inputSource)->get();
        $buildingServices = $building->buildingServices()->forInputSource($inputSource)->get();
        $buildingPvPanels = $building->pvPanels()->forInputSource($inputSource)->first();

        $buildingHeater = $building->heater()->forInputSource($inputSource)->first();

//        dd($user);
        $userEnergyHabit = $user->energyHabit()->forInputSource($inputSource)->first();
//
//        dd($userEnergyHabit, $user->energyHabit()->get());
//
        $wallInsulationElement = Element::where('short', 'wall-insulation')->first();
        $woodElements = Element::where('short', 'wood-elements')->first();
        $frames = Element::where('short', 'frames')->first();
        $crackSealing = Element::where('short', 'crack-sealing')->first();
        $floorInsulationElement = Element::where('short', 'floor-insulation')->first();
        $crawlspaceElement = Element::where('short', 'crawlspace')->first();

        $boilerService = Service::where('short', 'boiler')->first();
        $solarPanelService = Service::where('short', 'total-sun-panels')->first();
        $heaterService = Service::where('short', 'sun-boiler')->first();

        // handle stuff for the wall insulation
        $wallInsulationBuildingElement = $buildingElements->where('element_id', $wallInsulationElement->id)->first();

        // handle the stuff for the insulated glazing
        // the user interest on the insulated glazing
        // key = measure_application_id
        // val = interest_id
        $userInterestsForInsulatedGlazing = $user
            ->interests()
            ->forInputSource($inputSource)
            ->where('interested_in_type', 'measure_application')
            ->select('interested_in_id', 'interest_id')
            ->get()
            ->pluck('interest_id', 'interested_in_id')
            ->toArray();

        /** @var Collection $buildingInsulatedGlazings */
        $buildingInsulatedGlazings = $building
            ->currentInsulatedGlazing()
            ->forInputSource($inputSource)
            ->select('measure_application_id', 'insulating_glazing_id', 'building_heating_id', 'm2', 'windows')
            ->get();


        // build the right structure for the calculation
        $buildingInsulatedGlazingArray = [];
        foreach ($buildingInsulatedGlazings as $buildingInsulatedGlazing) {
            $buildingInsulatedGlazingArray[$buildingInsulatedGlazing->measure_application_id] = [
                'insulating_glazing_id' => $buildingInsulatedGlazing->insulating_glazing_id,
                'building_heating_id' => $buildingInsulatedGlazing->building_heating_id,
                'm2' => $buildingInsulatedGlazing->m2,
                'windows' => $buildingInsulatedGlazing->windows,
            ];
        }


        // handle the wood / frame / crack sealing elements for the insulated glazing
        $buildingElementsArray = [];

        $buildingWoodElement = $buildingElements->where('element_id', $woodElements->id)->pluck('element_value_id')->toArray();
        $buildingElementsArray[$woodElements->short][$woodElements->id] = array_combine($buildingWoodElement, $buildingWoodElement) ?? null;

        $buildingFrameElement = $buildingElements->where('element_id', $frames->id)->first();
        $buildingElementsArray[$frames->id][$frames->short] = $buildingFrameElement->element_value_id ?? null;

        $buildingCrackSealingElement = $buildingElements->where('element_id', $crackSealing->id)->first();
        $buildingElementsArray[$crackSealing->id][$crackSealing->short] = $buildingCrackSealingElement->element_value_id ?? null;


        $buildingPaintworkStatusesArray = [
            'last_painted_year' => $buildingPaintworkStatus->last_painted_year ?? null,
            'paintwork_status_id' => $buildingPaintworkStatus->paintwork_status_id ?? null,
            'wood_rot_status_id' => $buildingPaintworkStatus->wood_rot_status_id ?? null,
        ];

        // handle the stuff for the floor insulation.
        $floorInsulationElementValueId = $buildingElements->where('element_id', $floorInsulationElement->id)->first()->element_value_id ?? null;
        $buildingCrawlspaceElement = $buildingElements->where('element_id', $crawlspaceElement->id)->first();

        $floorInsulationBuildingElements = [
            'crawlspace' => $buildingCrawlspaceElement->extra['has_crawlspace'] ?? null,
            $crawlspaceElement->id => [
                'extra' => $buildingCrawlspaceElement->extra['access'] ?? null,
                'element_value_id' => $buildingCrawlspaceElement->element_value_id ?? null,
            ],
        ];

        $floorBuildingFeatures = [
            'floor_surface' => $buildingFeature->floor_surface ?? null,
            'insulation_surface' => $buildingFeature->insulation_surface ?? null,
        ];

        // now lets handle the roof insulation stuff.
        $buildingRoofTypesArray = ['id' => []];

        /** @var BuildingRoofType $buildingRoofType */
        foreach ($buildingRoofTypes as $buildingRoofType) {
            $short = $buildingRoofType->roofType->short;
            $buildingRoofTypesArray[$short] = [
                'element_value_id' => $buildingRoofType->element_value_id,
                'roof_surface' => $buildingRoofType->roof_surface,
                'insulation_roof_surface' => $buildingRoofType->insulation_roof_surface,
                'extra' => $buildingRoofType->extra,
                'measure_application_id' => $buildingRoofType->extra['measure_application_id'] ?? null,
                'building_heating_id' => $buildingRoofType->building_heating_id,
            ];
            $buildingRoofTypesArray['id'][] = $buildingRoofType->roofType->id;

            // if the roof is a flat roof OR the tiles_condition is empty: remove it!!
            // this is needed as the tiles condition has a different type of calculation
            // than bitumen has
            if (array_key_exists('tiles_condition', $buildingRoofTypesArray[$short]['extra'])) {
                if ($short == 'flat' || empty($buildingRoofTypesArray[$short]['extra']['tiles_condition'])) {
                    unset($buildingRoofTypesArray[$short]['extra']['tiles_condition']);
                }
            }
        }

        // now we handle the hr boiler stuff
        $buildingBoilerService = $buildingServices->where('service_id', $boilerService->id)->first();

        $buildingBoilerArray = [
            $boilerService->id => [
                'service_value_id' => $buildingBoilerService->service_value_id ?? null,
                'extra' => $buildingBoilerService->extra['date'] ?? null,
            ],
        ];

        // handle the solar panel stuff.

        // get the user interests for the solar panels keyed by type
        $userInterestsForSolarPanels = $user
            ->interests()
            ->forInputSource($inputSource)
            ->where('interested_in_type', 'service')
            ->where('interested_in_id', $solarPanelService->id)
            ->select('interested_in_id', 'interest_id', 'interested_in_type')
            ->get()
            ->keyBy('interested_in_type')->map(function ($item) {
                return [$item['interested_in_id'] => $item['interest_id']];
            })->toArray();

        // handle the heater stuff
        $userInterestsForHeater = $user
            ->interests()
            ->forInputSource($inputSource)
            ->where('interested_in_type', 'service')
            ->where('interested_in_id', $heaterService->id)
            ->select('interested_in_id', 'interest_id', 'interested_in_type')
            ->get()
            ->keyBy('interested_in_type')->map(function ($item) {
                return [$item['interested_in_id'] => $item['interest_id']];
            })->toArray();

        $wallInsulationSavings = WallInsulation::calculate($building, $inputSource, $userEnergyHabit, [
            'cavity_wall' => $buildingFeature->cavity_wall ?? null,
            'element' => [$wallInsulationElement->id => $wallInsulationBuildingElement->element_value_id ?? null],
            'insulation_wall_surface' => $buildingFeature->insulation_wall_surface ?? null,
            'wall_joints' => $buildingFeature->wall_joints ?? null,
            'contaminated_wall_joints' => $buildingFeature->contaminated_wall_joints ?? null,
            'facade_plastered_painted' => $buildingFeature->facade_plastered_painted ?? null,
            'facade_plastered_surface_id' => $buildingFeature->facade_plastered_surface_id ?? null,
            'facade_damaged_paintwork_id' => $buildingFeature->facade_damaged_paintwork_id ?? null,
        ]);


        $insulatedGlazingSavings = InsulatedGlazing::calculate($building, $inputSource, $userEnergyHabit, [
            'user_interests' => $userInterestsForInsulatedGlazing,
            'building_insulated_glazings' => $buildingInsulatedGlazingArray,
            'building_elements' => $buildingElementsArray,
            'window_surface' => $buildingFeature->window_surface ?? null,
            'building_paintwork_statuses' => $buildingPaintworkStatusesArray,
        ]);

        $floorInsulationSavings = FloorInsulation::calculate($building, $inputSource, $userEnergyHabit, [
            'element' => [$floorInsulationElement->id => $floorInsulationElementValueId],
            'building_elements' => $floorInsulationBuildingElements,
            'building_features' => $floorBuildingFeatures,
        ]);

        $roofInsulationSavings = RoofInsulation::calculate($building, $inputSource, $userEnergyHabit, [
            'building_roof_types' => $buildingRoofTypesArray,
        ]);

        $highEfficiencyBoilerSavings = HighEfficiencyBoiler::calculate($userEnergyHabit, [
            'building_services' => $buildingBoilerArray,
            'habit' => [
                'amount_gas' => $userEnergyHabit->amount_gas ?? null,
            ],
        ]);

        $solarPanelSavings = SolarPanel::calculate($building, [
            'building_pv_panels' => $buildingPvPanels instanceOf BuildingPvPanel ? $buildingPvPanels->toArray() : [],
            'user_energy_habits' => [
                'amount_electricity' => $userEnergyHabit->amount_electricity ?? null,
            ],
            'interest' => $userInterestsForSolarPanels,
        ]);

        $heaterSavings = Heater::calculate($building, $userEnergyHabit, [
            'building_heaters' => [
                $buildingHeater instanceof BuildingHeater ? $buildingHeater->toArray() : [],
            ],
            'user_energy_habits' => [
                'water_comfort_id' => $userEnergyHabit->water_comfort_id ?? null,
            ],
            'interest' => $userInterestsForHeater,
        ]);

        return [
            'wall-insulation' => $wallInsulationSavings,
            'insulated-glazing' => $insulatedGlazingSavings,
            'floor-insulation' => $floorInsulationSavings,
            'roof-insulation' => $roofInsulationSavings,
            'high-efficiency-boiler' => $highEfficiencyBoilerSavings,
            'solar-panels' => $solarPanelSavings,
            'heater' => $heaterSavings,
        ];
    }

    protected static function formatFieldOutput($column, $value, $maybe1, $maybe2)
    {
        //dump("formatFieldOutput (" . $column . ", " . $value . ", " . $maybe1 . ", " . $maybe2 . ")");
        $decimals = 0;
        $shouldRound = false;

        if (self::isYear($column) || self::isYear($maybe1, $maybe2)) {
            return $value;
        }

        if (!is_numeric($value)) {
            return $value;
        }

        if (in_array($column, ['interest_comparable',])) {
            $decimals = 1;
        }
        if ($column == 'specs' && $maybe1 == 'size_collector') {
            $decimals = 1;
        }
        if ($column == 'paintwork' && $maybe1 == 'costs') {
            /// round the cost for paintwork
            $shouldRound = true;
        }

        return self::formatOutput($column, $value, $decimals, $shouldRound);
    }

    /**
     * Format the output of the given column and value.
     *
     * @param string $column
     * @param mixed $value
     * @param  int $decimals
     * @param  bool $shouldRound
     *
     * @return float|int|string
     */
    protected static function formatOutput($column, $value, $decimals = 0, $shouldRound = false)
    {
        //dump("formatOutput (" . $column . ", " . $value . ", " . $decimals . ", " . $shouldRound . ")");

        if (in_array($column, ['percentage_consumption',]) ||
            stristr($column, 'savings_') !== false ||
            stristr($column, 'cost')) {
            $value = NumberFormatter::round($value);
        }
        if ($shouldRound) {
            $value = NumberFormatter::round($value);
        }
        // We should let Excel do the separation of thousands
        return number_format($value, $decimals, ",", "");
        //return NumberFormatter::format($value, $decimals, $shouldRound);
    }

    protected static function translateExtraValueIfNeeded($value)
    {
        if (in_array($value, ['yes', 'no', 'unknown'])) {
            $key = 'general.options.%s.title';
            return Translation::translate(sprintf($key, $value));
        }
    }

    /**
     * Returns whether or not two (optional!) columns contain a year or not
     *
     * @param string $column
     * @param string $extraValue
     *
     * @return bool
     */
    protected static function isYear($column, $extraValue = '')
    {
        if (!is_null($column)) {
            if (stristr($column, 'year') !== false) {
                return true;
            }
            if ($column == 'extra') {
                return in_array($extraValue, [
                    'year',
                ]);
            }
        }

        return false;
    }
}
