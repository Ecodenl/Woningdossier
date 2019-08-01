<?php

namespace App\Services;

use App\Helpers\KeyFigures\Heater\KeyFigures as HeaterKeyFigures;
use App\Helpers\KeyFigures\PvPanels\KeyFigures as SolarPanelsKeyFigures;
use App\Helpers\KeyFigures\RoofInsulation\Temperature;
use App\Helpers\ToolHelper;
use App\Helpers\Translation;
use App\Models\BuildingCoachStatus;
use App\Models\BuildingElement;
use App\Models\BuildingFeature;
use App\Models\BuildingHeater;
use App\Models\BuildingHeating;
use App\Models\BuildingInsulatedGlazing;
use App\Models\BuildingPaintworkStatus;
use App\Models\BuildingPvPanel;
use App\Models\BuildingRoofType;
use App\Models\BuildingService;
use App\Models\BuildingType;
use App\Models\ComfortLevelTapWater;
use App\Models\Element;
use App\Models\ElementValue;
use App\Models\EnergyLabel;
use App\Models\FacadeDamagedPaintwork;
use App\Models\FacadePlasteredSurface;
use App\Models\FacadeSurface;
use App\Models\InsulatingGlazing;
use App\Models\Interest;
use App\Models\MeasureApplication;
use App\Models\PaintworkStatus;
use App\Models\PrivateMessage;
use App\Models\PvPanelOrientation;
use App\Models\RoofTileStatus;
use App\Models\RoofType;
use App\Models\Service;
use App\Models\Step;
use App\Models\User;
use App\Models\UserEnergyHabit;
use App\Models\UserInterest;
use App\Models\WoodRotStatus;
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
        $structure = self::getPdfStructure();


        $cooperation = $user->cooperations()->first();

        // build the header structure, we will set those in the csv and use it later on to get the answers from the users.
        // unfortunately we cant array dot the structure since we only need the labels
        foreach ($structure as $stepSlug => $stepStructure) {
            if ($stepSlug == 'wall-insulation') {
                dd($stepStructure);
            }
            $step = Step::whereSlug($stepSlug)->first();
            foreach ($stepStructure as $tableWithColumnOrAndId => $contents) {
                if ($tableWithColumnOrAndId == 'calculations') {

                    // we will dot the array, map it so we can add the step name to it
                    $deeperContents = array_map(function ($content) use ($step) {
                        return $content;
                    }, \Illuminate\Support\Arr::dot($contents, $stepSlug.'.calculation.'));

                    $headers = array_merge($headers, $deeperContents);

                } else {
                    $headers[$stepSlug.'.'.$tableWithColumnOrAndId] = $contents['label'];
                }

            }
        }

        $rows[] = $headers;

        // for each user we create a new row.
        $row = [];

        // collect basic info from a user.
        $building   = $user->buildings()->first();
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
                    $column      = $columnOrId;
                    $costsOrYear = $tableWithColumnOrAndId[3] ?? null;
                    switch ($step) {
                        case 'roof-insulation':
                            $roofCategory = $tableWithColumnOrAndId[2];
                            $column       = $tableWithColumnOrAndId[3];
                            $costsOrYear  = $tableWithColumnOrAndId[4] ?? null;

                            $calculationResult = is_null($costsOrYear) ? $calculateData['roof-insulation'][$roofCategory][$column] ?? '' : $calculateData['roof-insulation'][$roofCategory][$column][$costsOrYear] ?? '';
                            break;
                        default:
                            $i++;
                            if (count($calculateData[$step][$column]) == 1) {
                                $row[$step]['calculation']['indicative-costs'][$translatedInputName] = $calculateData[$step][$column] ?? 'bier';
                            } else {
                                $row[$step]['calculation']['costs'][$column] = $calculateData[$step][$column] ?? 'bier';
                            }
                            if ($i == 11) {

                                dd($row[$step], $calculateData, $translatedInputName, $column);
                            }
//                            } else {

//                                dump($translatedInputName.' content: '. $column);
//                                dump($calculateData[$step][$column] ?? 'bier');
//                                dump($calculateData[$step]);
//                                $row[$step]['calculation']['maintenance-measures'][$translatedInputName] = $calculateData[$step][$column];
//                            }
                            break;

//
//                            'calculations' => [
//                            'savings_gas'         => Translation::translate('wall-insulation.costs.gas.title'),
//                            'savings_co2'         => Translation::translate('wall-insulation.costs.co2.title'),
//                            'savings_money'       => Translation::translate('general.costs.savings-in-euro.title'),
//                            'cost_indication'     => Translation::translate('general.costs.indicative-costs.title'),
//                            'interest_comparable' => Translation::translate('general.costs.comparable-rent.title'),
//
//                                'repair_joint'    => [
//                                    'costs' => Translation::translate('wall-insulation.taking-into-account.repair-joint.title'),
//                                    'year'  => Translation::translate('wall-insulation.taking-into-account.repair-joint.year.title'),
//                                ],
//                            ]
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


        dd($row);
        return $rows;
    }

    public static function getPdfStructure()
    {
        // General data

        // General data - Elements (that are not queried later on step basis)
        $livingRoomsWindows   = Element::where('short', 'living-rooms-windows')->first();
        $sleepingRoomsWindows = Element::where('short', 'sleeping-rooms-windows')->first();
        // General data - Services (that are not queried later on step basis)
        $heatpumpHybrid = Service::where('short', 'hybrid-heat-pump')->first();
        $heatpumpFull   = Service::where('short', 'full-heat-pump')->first();
        $ventilation    = Service::where('short', 'house-ventilation')->first();

        // Wall insulation
        $wallInsulation          = Element::where('short', 'wall-insulation')->first();
        $facadeDamages           = FacadeDamagedPaintwork::orderBy('order')->get();
        $surfaces                = FacadeSurface::orderBy('order')->get();
        $facadePlasteredSurfaces = FacadePlasteredSurface::orderBy('order')->get();
        $energyLabels            = EnergyLabel::all();

        // Insulated glazing
        $insulatedGlazings = InsulatingGlazing::all();
        $heatings          = BuildingHeating::where('calculate_value', '<', 5)->get(); // we don't want n.v.t.
        $crackSealing      = Element::where('short', 'crack-sealing')->first();
        $frames            = Element::where('short', 'frames')->first();
        $woodElements      = Element::where('short', 'wood-elements')->first();
        $paintworkStatuses = PaintworkStatus::orderBy('order')->get();
        $woodRotStatuses   = WoodRotStatus::orderBy('order')->get();

        // Floor insulation
        /** @var Element $floorInsulation */
        $floorInsulation = Element::where('short', 'floor-insulation')->first();
        $crawlspace      = Element::where('short', 'crawlspace')->first();

        // Roof insulation
        $roofInsulation   = Element::where('short', 'roof-insulation')->first();
        $roofTypes        = RoofType::all();
        $roofTileStatuses = RoofTileStatus::orderBy('order')->get();
        // Same as RoofInsulationController->getMeasureApplicationsAdviceMap()
        $roofInsulationMeasureApplications = [
            'flat'    => [
                Temperature::ROOF_INSULATION_FLAT_ON_CURRENT => MeasureApplication::where('short',
                    'roof-insulation-flat-current')->first(),
                Temperature::ROOF_INSULATION_FLAT_REPLACE    => MeasureApplication::where('short',
                    'roof-insulation-flat-replace-current')->first(),
            ],
            'pitched' => [
                Temperature::ROOF_INSULATION_PITCHED_INSIDE        => MeasureApplication::where('short',
                    'roof-insulation-pitched-inside')->first(),
                Temperature::ROOF_INSULATION_PITCHED_REPLACE_TILES => MeasureApplication::where('short',
                    'roof-insulation-pitched-replace-tiles')->first(),
            ],
        ];

        // High efficiency boiler
        // NOTE: building element hr-boiler tells us if it's there
        $hrBoiler = Service::where('short', 'hr-boiler')->first();
        $boiler   = Service::where('short', 'boiler')->first();

        // Solar panels
        $solarPanels                 = Service::where('short', 'total-sun-panels')->first();
        $solarPanelsOptionsPeakPower = ['' => '-'] + SolarPanelsKeyFigures::getPeakPowers();
        $solarPanelsOptionsAngle     = ['' => '-'] + SolarPanelsKeyFigures::getAngles();

        $heater             = Service::where('short', 'sun-boiler')->first();
        $heaterOptionsAngle = ['' => '-'] + HeaterKeyFigures::getAngles();


        $comfortLevelsTapWater = ComfortLevelTapWater::all();

        $buildingTypes = BuildingType::all();
        $buildingHeatings = BuildingHeating::all();
        $boilerTypes = $boiler->values()->orderBy('order')->get();

        // Common
        $interests       = Interest::orderBy('order')->get();
        $interestOptions = ToolHelper::createOptions($interests);

        $structure = [
            'building-detail' => [
                'building_features.building_type_id' => [
                    'label' => Translation::translate('building-detail.building-type.what-type.title'),
                    'type' => 'select',
                    'options' => $buildingTypes
                ],
                'building_features.build_year' => [
                    'label' => Translation::translate('building-detail.building-type.what-building-year.title'),
                    'type' => 'text',
                ],
            ],

            'general-data' => [
                'building_features.surface'                     => [
                    'label' => Translation::translate('general-data.building-type.what-user-surface.title'),
                    'type'  => 'text',
                    'unit'  => Translation::translate('general.unit.square-meters.title'),
                ],
                'building_features.building_layers'             => [
                    'label' => Translation::translate('general-data.building-type.how-much-building-layers.title'),
                    'type'  => 'text',
                ],
                'building_features.roof_type_id'                => [
                    'label'   => Translation::translate('general-data.building-type.type-roof.title'),
                    'type'    => 'select',
                    'options' => ToolHelper::createOptions($roofTypes),
                ],
                'building_features.energy_label_id'             => [
                    'label'   => Translation::translate('general-data.building-type.current-energy-label.title'),
                    'type'    => 'select',
                    'options' => ToolHelper::createOptions($energyLabels),
                ],
                'building_features.monument'                    => [
                    'label'   => Translation::translate('general-data.building-type.is-monument.title'),
                    'type'    => 'select',
                    'options' => [
                        1 => __('woningdossier.cooperation.radiobutton.yes'),
                        2 => __('woningdossier.cooperation.radiobutton.no'),
                        0 => __('woningdossier.cooperation.radiobutton.unknown'),
                    ],
                ],
                // elements and services
                'element.'.$livingRoomsWindows->id              => [
                    //'label' => Translation::translate('general.change-interested.title', ['item' => $livingRoomsWindows->name]),
                    'label'   => $livingRoomsWindows->name,
                    'type'    => 'select',
                    'options' => ToolHelper::createOptions($livingRoomsWindows->values()->orderBy('order')->get(), 'value'),
                ],
                'element.'.$sleepingRoomsWindows->id            => [
                    //'label' => Translation::translate('general.change-interested.title', ['item' => $livingRoomsWindows->name]),
                    'label'   => $sleepingRoomsWindows->name,
                    'type'    => 'select',
                    'options' => ToolHelper::createOptions($sleepingRoomsWindows->values()->orderBy('order')->get(), 'value'),
                ],

                'element.'.$wallInsulation->id                  => [
                    'label'   => Translation::translate('wall-insulation.intro.filled-insulation.title'),
                    'type'    => 'select',
                    'options' => ToolHelper::createOptions($wallInsulation->values()->orderBy('order')->get(), 'value'),
                ],

                'element.'.$floorInsulation->id                    => [
                    'label'   => Translation::translate('floor-insulation.floor-insulation.title'),
                    'type'    => 'select',
                    'options' => ToolHelper::createOptions($floorInsulation->values()->orderBy('order')->get(), 'value'),
                ],
                'element.'.$roofInsulation->id   => [
                    'label'   => $roofInsulation->name,
                    'type'    => 'select',
                    'options' => ToolHelper::createOptions($roofInsulation->values()->orderBy('order')->get(), 'value'),
                ],

                // services
                'service.'.$heatpumpHybrid->id                  => [
                    'label'   => $heatpumpHybrid->name,
                    'type'    => 'select',
                    'options' => ToolHelper::createOptions($heatpumpHybrid->values()->orderBy('order')->get(), 'value'),
                ],
                'service.'.$heatpumpFull->id                    => [
                    'label'   => $heatpumpFull->name,
                    'type'    => 'select',
                    'options' => ToolHelper::createOptions($heatpumpFull->values()->orderBy('order')->get(), 'value'),
                ],
                'service.'.$heater->id                     => [
                    'label'   => $heater->name,
                    'type'    => 'select',
                    'options' => ToolHelper::createOptions($heater->values()->orderBy('order')->get(), 'value'),
                ],
                // no separate page.
                //                'user_interest.service.'.$heatpumpFull->id      => [
                //                    'label' => Translation::translate('general.change-interested.title', ['item' => $livingRoomsWindows->name]),
                //                    'label'   => $heatpumpFull->name.': '.Translation::translate('general.interested-in-improvement.title'),
                //                    'type'    => 'select',
                //                    'options' => $interestOptions,
                //                ],

                'service.'.$hrBoiler->id                        => [
                    'label'   => $hrBoiler->name,
                    'type'    => 'select',
                    'options' => ToolHelper::createOptions($hrBoiler->values()->orderBy('order')->get(), 'value'),
                ],

                'service.'.$boiler->id.'.service_value_id'      => [
                    'label'   => Translation::translate('boiler.boiler-type.title'),
                    'type'    => 'select',
                    'options' => ToolHelper::createOptions($boiler->values()->orderBy('order')->get(), 'value'),
                ],
                'service.'.$solarPanels->id.'.extra.value'      => [
                    'label' => $solarPanels->name,
                    'type'  => 'text',
                    'unit'  => Translation::translate('general.unit.pieces.title'),
                ],

                'service.'.$solarPanels->id.'.extra.year'       => [
                    'label' => Translation::translate('general-data.energy-saving-measures.solar-panels.if-yes.title'),
                    'type'  => 'text',
                    'unit'  => Translation::translate('general.unit.year.title'),
                ],

                // ventilation
                'service.'.$ventilation->id.'.service_value_id' => [
                    'label'   => $ventilation->name,
                    'type'    => 'select',
                    'options' => ToolHelper::createOptions($ventilation->values()->orderBy('order')->get(), 'value'),
                ],
                // no separate page.
                //                'user_interest.service.'.$ventilation->id       => [
                //                    'label' => Translation::translate('general.change-interested.title', ['item' => $livingRoomsWindows->name]),
                //                    'label'   => $ventilation->name.': '.Translation::translate('general.interested-in-improvement.title'),
                //                    'type'    => 'select',
                //                    'options' => $interestOptions,
                //                ],
                'service.'.$ventilation->id.'.extra.year'       => [
                    'label' => Translation::translate('general-data.energy-saving-measures.house-ventilation.if-mechanic.title'),
                    'type'  => 'text',
                    'unit'  => Translation::translate('general.unit.year.title'),
                ],

                'user_energy_habits.resident_count' => [
                    'label' => Translation::translate('general-data.data-about-usage.total-citizens.title'),
                    'type' => 'text'
                ],
                // habits
                'user_energy_habits.cook_gas'                   => [
                    'label'   => Translation::translate('general-data.data-about-usage.cooked-on-gas.title'),
                    'type'    => 'select',
                    'options' => [
                        1 => __('woningdossier.cooperation.radiobutton.yes'),
                        2 => __('woningdossier.cooperation.radiobutton.no'),
                    ],
                ],

                'user_energy_habits.thermostat_high' => [
                    'label' => Translation::translate('general-data.data-about-usage.thermostat-highest.title'),
                    'type' => 'text',
                    'unit' => Translation::translate('general.unit.degrees.title'),
                ],

                'user_energy_habits.thermostat_low' => [
                    'label' => Translation::translate('general-data.data-about-usage.thermostat-lowest.title'),
                    'type' => 'text',
                    'unit' => Translation::translate('general.unit.degrees.title'),
                ],

                'user_energy_habits.hours_high' => [
                    'label' => Translation::translate('general-data.data-about-usage.max-hours-thermostat-highest.title'),
                    'type' => 'text',
                    'unit' => Translation::translate('general.unit.hours.title'),
                ],

                'user_energy_habits.heating_first_floor' => [
                    'label' => Translation::translate('general-data.data-about-usage.situation-first-floor.title'),
                    'type' => 'select',
                    'options' => $buildingHeatings
                ],

                'user_energy_habits.heating_second_floor' => [
                    'label' => Translation::translate('general-data.data-about-usage.situation-second-floor.title'),
                    'type' => 'select',
                    'options' => $buildingHeatings
                ],

                'user_energy_habits.water_comfort_id' => [
                    'label' => Translation::translate('general-data.data-about-usage.comfortniveau-warm-tapwater.title'),
                    'type' => 'select',
                    'options' => $comfortLevelsTapWater
                ],

                'user_energy_habits.amount_electricity'         => [
                    'label' => Translation::translate('general-data.data-about-usage.electricity-consumption-past-year.title'),
                    'type'  => 'text',
                    'unit'  => Translation::translate('general.unit.cubic-meters.title'),
                ],
                'user_energy_habits.amount_gas'                 => [
                    'label' => Translation::translate('general-data.data-about-usage.gas-usage-past-year.title'),
                    'type'  => 'text',
                    'unit'  => Translation::translate('general.unit.cubic-meters.title'),
                ],


                // user interests
            ],

            'wall-insulation' => [
                'user_interest.element.'.$wallInsulation->id    => [
                    //'label' => Translation::translate('general.change-interested.title', ['item' => $livingRoomsWindows->name]),
                    'label'   => $wallInsulation->name.': '.Translation::translate('general.interested-in-improvement.title'),
                    'type'    => 'select',
                    'options' => $interestOptions,
                ],
                'building_features.cavity_wall'                 => [
                    'label'   => Translation::translate('wall-insulation.intro.has-cavity-wall.title'),
                    'type'    => 'select',
                    'options' => [
                        0 => __('woningdossier.cooperation.radiobutton.unknown'),
                        1 => __('woningdossier.cooperation.radiobutton.yes'),
                        2 => __('woningdossier.cooperation.radiobutton.no'),
                    ],
                ],
                'building_features.facade_plastered_painted'    => [
                    'label'   => Translation::translate('wall-insulation.intro.is-facade-plastered-painted.title'),
                    'type'    => 'select',
                    'options' => [
                        1 => __('woningdossier.cooperation.radiobutton.yes'),
                        2 => __('woningdossier.cooperation.radiobutton.no'),
                        3 => __('woningdossier.cooperation.radiobutton.mostly'),
                    ],
                ],
                'building_features.facade_plastered_surface_id' => [
                    'label'   => Translation::translate('wall-insulation.intro.surface-paintwork.title'),
                    'type'    => 'select',
                    'options' => ToolHelper::createOptions($facadePlasteredSurfaces),
                ],
                'building_features.facade_damaged_paintwork_id' => [
                    'label'        => Translation::translate('wall-insulation.intro.damage-paintwork.title'),
                    'type'         => 'select',
                    'options'      => ToolHelper::createOptions($facadeDamages),
                    'relationship' => 'damagedPaintwork'
                ],
                'building_features.wall_joints'                 => [
                    'label'   => Translation::translate('wall-insulation.optional.flushing.title'),
                    'type'    => 'select',
                    'options' => ToolHelper::createOptions($surfaces),
                ],
                'building_features.contaminated_wall_joints'    => [
                    'label'   => Translation::translate('wall-insulation.optional.is-facade-dirty.title'),
                    'type'    => 'select',
                    'options' => ToolHelper::createOptions($surfaces),
                ],
                'building_features.wall_surface'                => [
                    'label' => Translation::translate('wall-insulation.optional.facade-surface.title'),
                    'type'  => 'text',
                    'unit'  => Translation::translate('general.unit.square-meters.title'),
                ],
                'building_features.insulation_wall_surface'     => [
                    'label' => Translation::translate('wall-insulation.optional.insulated-surface.title'),
                    'type'  => 'text',
                    'unit'  => Translation::translate('general.unit.square-meters.title'),
                ],

                'calculations' => [
                    'indicative-costs-and-benefits-for-measure' => [
                        'savings_gas'         => Translation::translate('wall-insulation.costs.gas.title'),
                        'savings_co2'         => Translation::translate('wall-insulation.costs.co2.title'),
                        'savings_money'       => Translation::translate('general.costs.savings-in-euro.title'),
                        'cost_indication'     => Translation::translate('general.costs.indicative-costs.title'),
                        'interest_comparable' => Translation::translate('general.costs.comparable-rent.title'),
                    ],
                    'measures' => [
                        'repair_joint'    => [
                            'costs' => Translation::translate('wall-insulation.taking-into-account.repair-joint.title'),
                            'year'  => Translation::translate('wall-insulation.taking-into-account.repair-joint.year.title'),
                        ],
                        'clean_brickwork' => [
                            'costs' => Translation::translate('wall-insulation.taking-into-account.clean-brickwork.title'),
                            'year'  => Translation::translate('wall-insulation.taking-into-account.clean-brickwork.year.title'),
                        ],

                        'impregnate_wall' => [
                            'costs' => Translation::translate('wall-insulation.taking-into-account.impregnate-wall.title'),
                            'year'  => Translation::translate('wall-insulation.taking-into-account.impregnate-wall.year.title'),
                        ],

                        'paint_wall' => [
                            'costs' => Translation::translate('wall-insulation.taking-into-account.wall-painting.title'),
                            'year'  => Translation::translate('wall-insulation.taking-into-account.wall-painting.year.title'),
                        ]
                    ],

                ]
            ],

            'insulated-glazing' => [
                'element.'.$crackSealing->id                      => [
                    'label'   => Translation::translate('insulated-glazing.moving-parts-quality.title'),
                    'type'    => 'select',
                    'options' => ToolHelper::createOptions($crackSealing->values()->orderBy('order')->get(), 'value'),
                ],
                'building_features.window_surface'                => [
                    'label' => Translation::translate('insulated-glazing.windows-surface.title'),
                    'type'  => 'text',
                    'unit'  => Translation::translate('general.unit.square-meters.title'),
                ],
                'element.'.$frames->id                            => [
                    'label'   => Translation::translate('insulated-glazing.paint-work.which-frames.title'),
                    'type'    => 'select',
                    'options' => ToolHelper::createOptions($frames->values()->orderBy('order')->get(), 'value'),
                ],
                'element.'.$woodElements->id                      => [
                    'label'   => Translation::translate('insulated-glazing.paint-work.other-wood-elements.title'),
                    'type'    => 'multiselect',
                    'options' => ToolHelper::createOptions($woodElements->values()->orderBy('order')->get(), 'value'),
                ],
                'building_paintwork_statuses.last_painted_year'   => [
                    'label' => Translation::translate('insulated-glazing.paint-work.last-paintjob.title'),
                    'type'  => 'text',
                    'unit'  => Translation::translate('general.unit.year.title'),
                ],
                'building_paintwork_statuses.paintwork_status_id' => [
                    'label'        => Translation::translate('insulated-glazing.paint-work.paint-damage-visible.title'),
                    'type'         => 'select',
                    'options'      => ToolHelper::createOptions($paintworkStatuses),
                    'relationship' => 'paintworkStatus'
                ],
                'building_paintwork_statuses.wood_rot_status_id'  => [
                    'label'   => Translation::translate('insulated-glazing.paint-work.wood-rot-visible.title'),
                    'type'    => 'select',
                    'options' => ToolHelper::createOptions($woodRotStatuses),
                ],

            ],

            'floor-insulation'       => [
                'user_interest.element.'.$floorInsulation->id   => [
                    //'label' => Translation::translate('general.change-interested.title', ['item' => $livingRoomsWindows->name]),
                    'label'   => $floorInsulation->name.': '.Translation::translate('general.interested-in-improvement.title'),
                    'type'    => 'select',
                    'options' => $interestOptions,
                ],
                'element.'.$crawlspace->id.'.extra.has_crawlspace' => [
                    'label'   => Translation::translate('floor-insulation.has-crawlspace.title'),
                    'type'    => 'select',
                    'options' => __('woningdossier.cooperation.option'),
                ],
                'element.'.$crawlspace->id.'.extra.access'         => [
                    'label'   => Translation::translate('floor-insulation.crawlspace-access.title'),
                    'type'    => 'select',
                    'options' => __('woningdossier.cooperation.option'),
                ],
                'element.'.$crawlspace->id.'.element_value_id'     => [
                    'label'   => Translation::translate('floor-insulation.crawlspace-height.title'),
                    'type'    => 'select',
                    'options' => ToolHelper::createOptions($crawlspace->values()->orderBy('order')->get(), 'value'),
                ],
                'building_features.floor_surface'                  => [
                    'label' => Translation::translate('floor-insulation.surface.title'),
                    'type'  => 'text',
                    'unit'  => Translation::translate('general.unit.square-meters.title'),
                ],
                'building_features.insulation_surface'             => [
                    'label' => Translation::translate('floor-insulation.insulation-surface.title'),
                    'type'  => 'text',
                    'unit'  => Translation::translate('general.unit.square-meters.title'),
                ],

                'calculations' => [
                    'savings_gas'         => Translation::translate('floor-insulation.costs.gas.title'),
                    'savings_co2'         => Translation::translate('floor-insulation.costs.co2.title'),
                    'savings_money'       => Translation::translate('general.costs.savings-in-euro.title'),
                    'cost_indication'     => Translation::translate('general.costs.indicative-costs.title'),
                    'interest_comparable' => Translation::translate('general.costs.comparable-rent.title'),
                ]
            ],

            'roof-insulation'        => [
                'user_interest.element.'.$roofInsulation->id    => [
                    //'label' => Translation::translate('general.change-interested.title', ['item' => $livingRoomsWindows->name]),
                    'label'   => $roofInsulation->name.': '.Translation::translate('general.interested-in-improvement.title'),
                    'type'    => 'select',
                    'options' => $interestOptions,
                ],
                'building_features.roof_type_id' => [
                    'label'        => Translation::translate('roof-insulation.current-situation.main-roof.title'),
                    'type'         => 'select',
                    'options'      => ToolHelper::createOptions($roofTypes),
                    'relationship' => 'roofType'
                ],
                // rest will be added later on
            ],

            'high-efficiency-boiler' => [
                'user_interest.service.'.$hrBoiler->id          => [
                    //'label' => Translation::translate('general.change-interested.title', ['item' => $livingRoomsWindows->name]),
                    'label'   => $hrBoiler->name.': '.Translation::translate('general.interested-in-improvement.title'),
                    'type'    => 'select',
                    'options' => $interestOptions,
                ],
                'user_energy_habits.resident_count' => [
                    'label' => Translation::translate('general-data.data-about-usage.total-citizens.title'),
                    'type' => 'text'
                ],
                'user_energy_habits.amount_gas'                 => [
                    'label' => Translation::translate('general-data.data-about-usage.gas-usage-past-year.title'),
                    'type'  => 'text',
                    'unit'  => Translation::translate('general.unit.cubic-meters.title'),
                ],
                'service.'.$boiler->id.'.service_value_id' => [
                    'label' => Translation::translate('high-efficiency-boiler.boiler-type.title'),
                    'type' => 'select',
                    'options' => $boilerTypes
                ],
                'service.'.$boiler->id.'.extra.year' => [
                    'label' => Translation::translate('boiler.boiler-placed-date.title'),
                    'type'  => 'text',
                    'unit'  => Translation::translate('general.unit.year.title'),
                ],
                'calculations'                       => [
                    'savings_gas'         => Translation::translate('high-efficiency-boiler.costs.gas.title'),
                    'savings_co2'         => Translation::translate('high-efficiency-boiler.costs.co2.title'),
                    'savings_money'       => Translation::translate('general.costs.savings-in-euro.title'),
                    'cost_indication'     => Translation::translate('general.costs.indicative-costs.title'),
                    'interest_comparable' => Translation::translate('general.costs.comparable-rent.title'),

                    'replace_year' => Translation::translate('high-efficiency-boiler.indication-for-costs.indicative-replacement.title')
                ]
            ],

            'solar-panels' => [
                'user_interest.service.'.$solarPanels->id       => [
                    //'label' => Translation::translate('general.change-interested.title', ['item' => $livingRoomsWindows->name]),
                    'label'   => $solarPanels->name.': '.Translation::translate('general.interested-in-improvement.title'),
                    'type'    => 'select',
                    'options' => $interestOptions,
                ],
                'user_energy_habits.amount_electricity'         => [
                    'label' => Translation::translate('solar-panels.electra-usage.title'),
                    'type'  => 'text',
                    'unit'  => Translation::translate('general.unit.cubic-meters.title'),
                ],
                'building_pv_panels.peak_power'              => [
                    'label'   => Translation::translate('solar-panels.peak-power.title'),
                    'type'    => 'select',
                    'options' => $solarPanelsOptionsPeakPower,
                ],
                'building_pv_panels.number'                  => [
                    'label' => Translation::translate('solar-panels.number.title'),
                    'type'  => 'text',
                    'unit'  => Translation::translate('general.unit.pieces.title'),
                ],
                'building_pv_panels.pv_panel_orientation_id' => [
                    'label'        => Translation::translate('solar-panels.pv-panel-orientation-id.title'),
                    'type'         => 'select',
                    'options'      => ToolHelper::createOptions(PvPanelOrientation::orderBy('order')->get()),
                    'relationship' => 'orientation'
                ],
                'building_pv_panels.angle'                   => [
                    'label'   => Translation::translate('solar-panels.angle.title'),
                    'type'    => 'select',
                    'options' => $solarPanelsOptionsAngle,
                ],

                'calculations' => [
                    'yield_electricity'     => Translation::translate('solar-panels.indication-for-costs.yield-electricity.title'),
                    'raise_own_consumption' => Translation::translate('solar-panels.indication-for-costs.raise-own-consumption.title'),

                    'savings_co2'         => Translation::translate('solar-panels.costs.co2.title'),
                    'savings_money'       => Translation::translate('general.costs.savings-in-euro.title'),
                    'cost_indication'     => Translation::translate('general.costs.indicative-costs.title'),
                    'interest_comparable' => Translation::translate('general.costs.comparable-rent.title'),
                ],
            ],

            'heater'       => [
                'user_interest.service.'.$heater->id            => [
                    //'label' => Translation::translate('general.change-interested.title', ['item' => $livingRoomsWindows->name]),
                    'label'   => $heater->name.': '.Translation::translate('general.interested-in-improvement.title'),
                    'type'    => 'select',
                    'options' => $interestOptions,
                ],
                'user_energy_habits.water_comfort_id' => [
                    'label' => Translation::translate('heater.comfort-level-warm-tap-water.title'),
                    'type' => 'select',
                    'options' => $comfortLevelsTapWater
                ],
                'building_heaters.pv_panel_orientation_id' => [
                    'label'        => Translation::translate('heater.pv-panel-orientation-id.title'),
                    'type'         => 'select',
                    'options'      => ToolHelper::createOptions(PvPanelOrientation::orderBy('order')->get()),
                    'relationship' => 'orientation'
                ],
                'building_heaters.angle'                   => [
                    'label'   => Translation::translate('heater.angle.title'),
                    'type'    => 'select',
                    'options' => $heaterOptionsAngle,
                ],

                'calculations' => [
                    'consumption' => [
                        'water' => Translation::translate('heater.consumption-water.title'),
                        'gas' => Translation::translate('heater.consumption-gas.title'),
                    ],

                    'specs' => [
                        'size_boiler'    => Translation::translate('heater.size-boiler.title'),
                        'size_collector' => Translation::translate('heater.size-collector.title'),
                    ],
                    'production_heat'        => Translation::translate('heater.indication-for-costs.production-heat'),
                    'percentage_consumption' => Translation::translate('heater.indication-for-costs.percentage-consumption.title'),
                    'savings_gas'         => Translation::translate('heater.costs.gas.title'),
                    'savings_co2'         => Translation::translate('heater.costs.co2.title'),
                    'savings_money'       => Translation::translate('general.costs.savings-in-euro.title'),
                    'cost_indication'     => Translation::translate('general.costs.indicative-costs.title'),
                    'interest_comparable' => Translation::translate('general.costs.comparable-rent.title'),
                ]
            ],
        ];

        // Insulated glazing
        $igShorts = [
            'glass-in-lead', 'hrpp-glass-only',
            'hrpp-glass-frames', 'hr3p-frames',
        ];

        foreach ($igShorts as $igShort) {
            $measureApplication = MeasureApplication::where('short', $igShort)->first();
            if ($measureApplication instanceof MeasureApplication) {
                $structure['insulated-glazing']['user_interests.'.$measureApplication->id]                                      = [
                    //'label' => 'Interest in '.$measureApplication->measure_name,
                    'label'   => Translation::translate('general.change-interested.title',
                        ['item' => $measureApplication->measure_name]),
                    'type'    => 'select',
                    'options' => $interestOptions,
                ];
                $structure['insulated-glazing']['building_insulated_glazings.'.$measureApplication->id.'.insulated_glazing_id'] = [
                    'label'        => $measureApplication->measure_name.': '.Translation::translate('insulated-glazing.'.$measureApplication->short.'.current-glass.title'),
                    'type'         => 'select',
                    'options'      => ToolHelper::createOptions($insulatedGlazings),
                    'relationship' => 'insulatedGlazing'
                ];
                $structure['insulated-glazing']['building_insulated_glazings.'.$measureApplication->id.'.building_heating_id']  = [
                    'label'        => $measureApplication->measure_name.': '.Translation::translate('insulated-glazing.'.$measureApplication->short.'.rooms-heated.title'),
                    'type'         => 'select',
                    'options'      => ToolHelper::createOptions($heatings),
                    'relationship' => 'buildingHeating'
                ];
                $structure['insulated-glazing']['building_insulated_glazings.'.$measureApplication->id.'.m2']                   = [
                    'label' => $measureApplication->measure_name.': '.Translation::translate('insulated-glazing.'.$measureApplication->short.'.m2.title'),
                    'type'  => 'text',
                    'unit'  => Translation::translate('general.unit.square-meters.title'),
                ];
                $structure['insulated-glazing']['building_insulated_glazings.'.$measureApplication->id.'.windows']              = [
                    'label' => $measureApplication->measure_name.': '.Translation::translate('insulated-glazing.'.$measureApplication->short.'.window-replace.title'),
                    'type'  => 'text',
                ];
            }

            // set the calculations on the end because of the order
            if ($igShort == end($igShorts)) {
                $structure['insulated-glazing']['calculations'] = [
                    'savings_gas'         => Translation::translate('insulated-glazing.costs.gas.title'),
                    'savings_co2'         => Translation::translate('insulated-glazing.costs.co2.title'),
                    'savings_money'       => Translation::translate('general.costs.savings-in-euro.title'),
                    'cost_indication'     => Translation::translate('general.costs.indicative-costs.title'),
                    'interest_comparable' => Translation::translate('general.costs.comparable-rent.title'),

                    'paintwork' => [
                        'costs' => Translation::translate('insulated-glazing.taking-into-account.paintwork.title'),
                        'year'  => Translation::translate('insulated-glazing.taking-into-account.paintwork_year.title'),
                    ]
                ];
            }
        }


        // Roof insulation
        // have to refactor this
        // pitched = 1
        // flat = 2
        $pitched        = new \stdClass();
        $pitched->id    = 1;
        $pitched->short = 'pitched';
        $flat           = new \stdClass();
        $flat->id       = 2;
        $flat->short    = 'flat';
        $roofTypes1     = collect([$pitched, $flat]);

        // $roofTypes1 should become $roofTypes->where('short', '!=', 'none');

        foreach ($roofTypes1 as $roofType) {
            $structure['roof-insulation']['building_roof_types.'.$roofType->id.'.element_value_id']         = [
                'label'        => Translation::translate('roof-insulation.current-situation.is-'.$roofType->short.'-roof-insulated.title'),
                'type'         => 'select',
                'options'      => ToolHelper::createOptions($roofInsulation->values, 'value'),
                'relationship' => 'elementValue'
            ];
            $structure['roof-insulation']['building_roof_types.'.$roofType->id.'.roof_surface']             = [
                'label' => Translation::translate('roof-insulation.current-situation.'.$roofType->short.'-roof-surface.title'),
                'type'  => 'text',
                'unit'  => Translation::translate('general.unit.square-meters.title'),
            ];
            $structure['roof-insulation']['building_roof_types.'.$roofType->id.'.insulation_roof_surface']  = [
                'label' => Translation::translate('roof-insulation.current-situation.insulation-'.$roofType->short.'-roof-surface.title'),
                'type'  => 'text',
                'unit'  => Translation::translate('general.unit.square-meters.title'),
            ];
            $structure['roof-insulation']['building_roof_types.'.$roofType->id.'.extra.zinc_replaced_date'] = [
                'label' => Translation::translate('roof-insulation.current-situation.zinc-replaced.title'),
                'type'  => 'text',
                'unit'  => Translation::translate('general.unit.year.title'),
            ];
            if ('flat' == $roofType->short) {
                $structure['roof-insulation']['building_roof_types.'.$roofType->id.'.extra.bitumen_replaced_date'] = [
                    'label' => Translation::translate('roof-insulation.current-situation.bitumen-insulated.title'),
                    'type'  => 'text',
                    'unit'  => Translation::translate('general.unit.year.title'),
                ];
            }
            if ('pitched' == $roofType->short) {
                $structure['roof-insulation']['building_roof_types.'.$roofType->id.'.extra.tiles_condition'] = [
                    'label'   => Translation::translate('roof-insulation.current-situation.in-which-condition-tiles.title'),
                    'type'    => 'select',
                    'options' => ToolHelper::createOptions($roofTileStatuses),
                ];
            }
            $structure['roof-insulation']['building_roof_types.'.$roofType->id.'.extra.measure_application_id'] = [
                'label'        => Translation::translate('roof-insulation.'.$roofType->short.'-roof.insulate-roof.title'),
                'type'         => 'select',
                'options'      => ToolHelper::createOptions(collect($roofInsulationMeasureApplications[$roofType->short]),
                    'measure_name'),
                'relationship' => 'measureApplication'
            ];
            $structure['roof-insulation']['building_roof_types.'.$roofType->id.'.building_heating_id']          = [
                'label'        => Translation::translate('roof-insulation.'.$roofType->short.'-roof.situation.title'),
                'type'         => 'select',
                'options'      => ToolHelper::createOptions($heatings),
                'relationship' => 'heating'
            ];

            if ($roofType->short == $roofTypes1->last()->short) {

                $structure['roof-insulation']['calculations'] = [
                    'flat'    => [
                        'savings_gas'         => Translation::translate('roof-insulation.flat.costs.gas.title'),
                        'savings_co2'         => Translation::translate('roof-insulation.flat.costs.co2.title'),
                        'savings_money'       => Translation::translate('general.costs.savings-in-euro.title'),
                        'cost_indication'     => Translation::translate('general.costs.indicative-costs.title'),
                        'interest_comparable' => Translation::translate('general.costs.comparable-rent.title'),

                        'replace' => [
                            'costs' => Translation::translate('roof-insulation.flat.indicative-costs-replacement.title'),
                            'year' => Translation::translate('roof-insulation.flat.indicative-replacement.year.title')
                        ]
                    ],
                    'pitched' => [
                        'savings_gas'         => Translation::translate('roof-insulation.pitched.costs.gas.title'),
                        'savings_co2'         => Translation::translate('roof-insulation.pitched.costs.co2.title'),
                        'savings_money'       => Translation::translate('general.costs.savings-in-euro.title'),
                        'cost_indication'     => Translation::translate('general.costs.indicative-costs.title'),
                        'interest_comparable' => Translation::translate('general.costs.comparable-rent.title'),


                        'replace' => [
                            'costs' => Translation::translate('roof-insulation.pitched.indicative-costs-replacement.title'),
                            'year' => Translation::translate('roof-insulation.pitched.indicative-replacement.year.title')
                        ]


                    ]
                ];
            }
        }

        return $structure;
    }
}