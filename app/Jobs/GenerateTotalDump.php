<?php

namespace App\Jobs;

use App\Calculations\WallInsulation;
use App\Exports\Cooperation\TotalExport;
use App\Helpers\ToolHelper;
use App\Models\BuildingElement;
use App\Models\BuildingFeature;
use App\Models\BuildingHeater;
use App\Models\BuildingInsulatedGlazing;
use App\Models\BuildingPaintworkStatus;
use App\Models\BuildingPvPanel;
use App\Models\BuildingRoofType;
use App\Models\BuildingService;
use App\Models\Cooperation;
use App\Models\ElementValue;
use App\Models\EnergyLabel;
use App\Models\FacadeDamagedPaintwork;
use App\Models\FacadePlasteredSurface;
use App\Models\MeasureApplication;
use App\Models\RoofType;
use App\Models\Service;
use App\Models\UserEnergyHabit;
use App\Models\UserInterest;
use App\Scopes\GetValueScope;
use App\Services\CsvService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Maatwebsite\Excel\Facades\Excel;

class GenerateTotalDump
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $cooperation;

    /**
     * @param  Cooperation  $cooperation
     */
    public function __construct(Cooperation $cooperation)
    {
        $this->cooperation = $cooperation;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Get the users from the cooperations
        $users = $this->cooperation->users->take(5);

        $headers = [];
        $rows    = [];

        // get the content structure of the whole tool.
        $structure = ToolHelper::getContentStructure();

        // build the header structure, we will set those in the csv and use it later on to get the answers form the users.
        foreach ($structure as $stepSlug => $stepStructure) {
            foreach ($stepStructure as $tableWithColumnOrAndId => $contents) {
                if ($tableWithColumnOrAndId != 'calculations') {
                    $headers[$stepSlug.'.'.$tableWithColumnOrAndId] = $contents['label'];
                } else {
                    // calculations are 1 level deeper.
                    $calculations = $contents;
                    foreach ($calculations as $calculationType => $translation) {
                        if ( ! is_array($translation)) {
                            $headers[$stepSlug.'.calculation.'.$calculationType] = $translation;
                        } else {
                            foreach ($translation as $calculationTypeDeeper => $translationDeeper) {
                                $headers[$stepSlug.'.calculation.'.$calculationType.'.'.$calculationTypeDeeper] = $translationDeeper;
                            }
                        }
                    }
                }

            }
        }

        $rows[] = $headers;

        // get the data for every user.
        foreach ($users as $user) {
            $row = [];
            // collect basic info from a user.
            $building   = $user->buildings()->first();
            $buildingId = $building->id;

            // collect some info about their building
            $buildingFeature = $building->buildingFeatures;
            $cavityWall = $buildingFeature->cavity_wall;
            $buildingElementId = $facadeInsulation = $building->getBuildingElement('wall-insulation');

            // wall insulation savings
            $wallInsulationSavings = WallInsulation::calculate($building, $user, [
                'cavity_wall' => $cavityWall,
                'element' => $buildingElementId,
                'insulation_wall_surface' => $buildingFeature->insulation_wall_surface,
                'wall_joints' => $buildingFeature->wall_joins,
                'contaminated_wall_joints' => $buildingFeature->contaminated_wall_joints,
                'facade_plastered_painted' => $buildingFeature->facade_plastered_painted,
                'facade_plastered_surface_id' => $buildingFeature->facade_plastered_surface_id,
                'facade_damaged_paintwork_id' => $buildingFeature->facade_damaged_paintwork_id,
            ]);

//            dd($wallInsulationSavings);



            // loop through the headers
            foreach ($headers as $tableWithColumnOrAndIdKey => $translatedInputName) {
                // explode it so we can do stuff with it.
                $tableWithColumnOrAndId = explode('.', $tableWithColumnOrAndIdKey);

                // collect some basic info
                // which will apply to (most) cases.
                $step = $tableWithColumnOrAndId[0];
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
                    $column = $columnOrId;

                    switch ($step) {
                        case 'wall-insulation':
                            $row[$buildingId][$tableWithColumnOrAndIdKey] = $wallInsulationSavings[$column];
                            break;

                    }
                }

                // handle the building_features table and its columns.
                if ($table == 'building_features') {

                    $buildingFeature = BuildingFeature::withoutGlobalScope(GetValueScope::class)->where($whereUserOrBuildingId)->first();

                    if ($buildingFeature instanceof BuildingFeature) {

                        switch ($columnOrId) {
                            case 'roof_type_id':
                                $row[$buildingId][$tableWithColumnOrAndIdKey] = $buildingFeature->roofType instanceof RoofType ? $buildingFeature->roofType->name : '';
                                break;
                            case 'energy_label_id':
                                $row[$buildingId][$tableWithColumnOrAndIdKey] = $buildingFeature->energyLabel instanceof EnergyLabel ? $buildingFeature->energyLabel->name : '';
                                break;
                            case 'facade_damaged_paintwork_id':
                                $row[$buildingId][$tableWithColumnOrAndIdKey] = $buildingFeature->damagedPaintwork instanceof FacadeDamagedPaintwork ? $buildingFeature->damagedPaintwork->value : '';
                                break;
                            case 'facade_plastered_painted':
                                $possibleAnswers                              = [
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
                                $possibleAnswers                              = [
                                    1 => \App\Helpers\Translation::translate('general.options.yes.title'),
                                    2 => \App\Helpers\Translation::translate('general.options.no.title'),
                                    0 => \App\Helpers\Translation::translate('general.options.unknown.title'),
                                ];
                                $row[$buildingId][$tableWithColumnOrAndIdKey] = $possibleAnswers[$buildingFeature->monument] ?? '';
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
                    $column     = $tableWithColumnOrAndId[3];

                    $buildingRoofType = BuildingRoofType::withoutGlobalScope(GetValueScope::class)
                                                        ->where('roof_type_id', $roofTypeId)
                                                        ->where($whereUserOrBuildingId)
                                                        ->first();

                    if ($buildingRoofType instanceof BuildingRoofType) {

                        switch ($column) {
                            case 'element_value_id':
                                $row[$buildingId][$tableWithColumnOrAndIdKey] = $buildingRoofType->elementValue instanceof ElementValue ? $buildingRoofType->elementValue->value : '';
                                break;
                            case 'building_heating_id':
                                $row[$buildingId][$tableWithColumnOrAndIdKey] = $buildingRoofType->heating instanceof buildingHeater ? $buildingRoofType->heating->name : '';
                                break;
                            case 'extra.measure_application_id':
                                $extraIsArray                                 = is_array($buildingRoofType->extra);
                                $measureApplicationId                         = $extraIsArray ? $buildingRoofType->extra['measure_application_id'] ?? null : null;
                                $row[$buildingId][$tableWithColumnOrAndIdKey] = is_null($measureApplicationId) ? '' : MeasureApplication::find($measureApplicationId)->measure_name;
                                break;
                            default:
                                // check if we need to get data from the extra column
                                if (stristr($tableWithColumnOrAndIdKey, 'extra')) {
                                    $extraKey                                     = explode('extra.',
                                        $tableWithColumnOrAndIdKey)[1];
                                    $row[$buildingId][$tableWithColumnOrAndIdKey] = $buildingRoofType->extra[$extraKey] ?? '';
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
                if ($table == 'user_interest') {
                    $interestInType = $columnOrId;
                    $interestInId   = $tableWithColumnOrAndId[3];

                    $userInterest = UserInterest::withoutGlobalScope(GetValueScope::class)
                                                ->where($whereUserOrBuildingId)
                                                ->where('interested_in_id', $interestInId)
                                                ->where('interested_in_type', $interestInType)
                                                ->residentInput()->first();

                    $row[$buildingId][$tableWithColumnOrAndIdKey] = $userInterest->interest->name ?? '';
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

                                    $row[$buildingId][$tableWithColumnOrAndIdKey] = is_array($buildingElement->extra) ? $buildingElement->extra[$extraKey] ?? '' : '';
                                } else {
                                    $row[$buildingId][$tableWithColumnOrAndIdKey] = $buildingElement->elementValue->value ?? '';
                                }
                            } else {
                                // always set defaults
                                $row[$buildingId][$tableWithColumnOrAndIdKey] = '';
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

                                    if ($extraKey == 'value' && $extraIsArray) {
                                        $service                                      = Service::find($buildingService->extra['value']);
                                        $row[$buildingId][$tableWithColumnOrAndIdKey] = $service instanceof Service ? $service->name : '';
                                    } else {
                                        // if is array, try to get the answer from the extra column, does the key not exist set a default value.
                                        $row[$buildingId][$tableWithColumnOrAndIdKey] = $extraIsArray ? $buildingService->extra[$extraKey] ?? '' : '';
                                    }
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
                                $row[$buildingId][$tableWithColumnOrAndIdKey] = $buildingInsulatedGlazing->insulatedGlazing->name ?? '';
                                break;
                            case 'building_heating_id':
                                $row[$buildingId][$tableWithColumnOrAndIdKey] = $buildingInsulatedGlazing->buildingHeater->name ?? '';
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
                    $buildingPvPanel = BuildingPvPanel::withoutGlobalScope(GetValueScope::class)
                                                      ->where($whereUserOrBuildingId)
                                                      ->residentInput()->first();

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
                    $buildingHeater = BuildingHeater::withoutGlobalScope(GetValueScope::class)
                                                    ->where($whereUserOrBuildingId)
                                                    ->residentInput()->first();

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
                    $userEnergyHabit = UserEnergyHabit::withoutGlobalScope(GetValueScope::class)
                                                      ->where($whereUserOrBuildingId)
                                                      ->residentInput()->first();

                    if ($userEnergyHabit instanceof UserEnergyHabit) {
                        switch ($column) {
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
                    $buildingPaintworkStatus = BuildingPaintworkStatus::withoutGlobalScope(GetValueScope::class)
                                                                      ->where($whereUserOrBuildingId)
                                                                      ->residentInput()->first();

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
                dd($row);

            $rows[] = array_merge($headers, $row[$buildingId]);
        }

        // export the csv file
        Excel::store(new TotalExport($rows),'tests.csv', 'reports', \Maatwebsite\Excel\Excel::CSV);

    }
}
