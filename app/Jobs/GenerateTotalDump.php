<?php

namespace App\Jobs;

use App\Calculations\InsulatedGlazing;
use App\Calculations\WallInsulation;
use App\Exports\Cooperation\TotalExport;
use App\Helpers\Hoomdossier;
use App\Helpers\ToolHelper;
use App\Models\Building;
use App\Models\BuildingElement;
use App\Models\BuildingFeature;
use App\Models\BuildingHeater;
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
use App\Models\MeasureApplication;
use App\Models\RoofType;
use App\Models\Service;
use App\Models\User;
use App\Models\UserEnergyHabit;
use App\Models\UserInterest;
use App\Scopes\GetValueScope;
use App\Services\CsvService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Collection;
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
        // unfortunately we cant array dot the structure since we only need the labels
        foreach ($structure as $stepSlug => $stepStructure) {
            foreach ($stepStructure as $tableWithColumnOrAndId => $contents) {
                if ($tableWithColumnOrAndId != 'calculations') {
                    $headers[$stepSlug.'.'.$tableWithColumnOrAndId] = $contents['label'];
                } else {
                    // here we can dot it tho
                    $deeperContents = $contents;
                    $headers        = array_merge($headers, array_dot($deeperContents, $stepSlug.'.calculation.'));
                }

            }
        }

        $rows[] = $headers;

        /**
         * Get the data for every user.
         * @var User $user
         */
        foreach ($users as $user) {
            $row = [];
            // collect basic info from a user.
            $building   = $user->buildings()->first();
            $buildingId = $building->id;


            $calculateData = $this->getCalculateData($building, $user);

            // loop through the headers
            foreach ($headers as $tableWithColumnOrAndIdKey => $translatedInputName) {
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
                    $column      = $columnOrId;
                    $costsOrYear = $tableWithColumnOrAndId[3] ?? null;

                    switch ($step) {
                        case 'wall-insulation':
                            $row[$buildingId][$tableWithColumnOrAndIdKey] = ! is_null($costsOrYear) ? $calculateData['wall-insulation'][$column][$costsOrYear] : $calculateData['wall-insulation'][$column] ?? '';
                            break;
                        case 'insulated-glazing':
                            $row[$buildingId][$tableWithColumnOrAndIdKey] = ! is_null($costsOrYear) ? $calculateData['insulated-glazing'][$column][$costsOrYear] : $calculateData['insulated-glazing'][$column] ?? '';
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
            dd(array_merge($headers, $row[$buildingId]));

            $rows[] = array_merge($headers, $row[$buildingId]);
        }

        // export the csv file
        Excel::store(new TotalExport($rows), 'tests.csv', 'reports', \Maatwebsite\Excel\Excel::CSV);

    }

    /**
     * The method will return a array with calculations categorized under a step slug
     *
     * This method collects all data necessary to create the calculations. (spoiler alert: its a lot)
     *
     * @param  Building  $building
     * @param  User  $user
     *
     * @return array
     */
    protected function getCalculateData(Building $building, User $user)
    {
        // collect some info about their building
        $buildingFeature   = $building->buildingFeatures()->withoutGlobalScope(GetValueScope::class)->residentInput()->first();
        $buildingElements = $building->buildingElements()->withoutGlobalScope(GetValueScope::class)->residentInput()->get();
        $buildingPaintworkStatus = $building->currentPaintworkStatus()->withoutGlobalScope(GetValueScope::class)->residentInput()->first();

        $cavityWall        = $buildingFeature->cavity_wall;
        $wallInsulationElementId = $facadeInsulation = $building->getBuildingElement('wall-insulation');
        $woodElements = Element::where('short', 'wood-elements')->first();
        $frames = Element::where('short', 'frames')->first();
        $crackSealing = Element::where('short', 'crack-sealing')->first();

        // the user interest on the insulated glazing
        // key = measure_application_id
        // val = interest_id
        $userInterestsForInsulatedGlazing = $user
            ->interests()
            ->withoutGlobalScope(GetValueScope::class)
            ->residentInput()
            ->where('interested_in_type', 'measure_application')
            ->select('interested_in_id', 'interest_id')
            ->get()
            ->pluck('interest_id', 'interested_in_id')
            ->toArray();

        /** @var Collection $buildingInsulatedGlazings */
        $buildingInsulatedGlazings = $building
            ->currentInsulatedGlazing()
            ->withoutGlobalScope(GetValueScope::class)
            ->residentInput()
            ->select('measure_application_id', 'insulating_glazing_id', 'building_heating_id', 'm2', 'windows')
            ->get();


        // build the right structure for the calculation
        $buildingInsulatedGlazingArray = [];
        foreach ($buildingInsulatedGlazings as $buildingInsulatedGlazing) {
            $buildingInsulatedGlazingArray[$buildingInsulatedGlazing->measure_application_id] = [
                'insulating_glazing_id' => $buildingInsulatedGlazing->insulating_glazing_id,
                'building_heating_id'   => $buildingInsulatedGlazing->building_heating_id,
                'm2'                    => $buildingInsulatedGlazing->m2,
                'windows'               => $buildingInsulatedGlazing->windows,
            ];
        }


        // handle the wood / frame / crack sealing elements for the insulated glazing
        $buildingWoodElement = $buildingElements->where('element_id', $woodElements->id)->pluck('element_value_id')->toArray();
        $buildingElementsArray[$woodElements->short][$woodElements->id] = array_combine($buildingWoodElement, $buildingWoodElement) ?? null;

        $buildingFrameElement = $buildingElements->where('element_id', $frames->id)->first();
        $buildingElementsArray[$frames->id][$frames->short] = $buildingFrameElement->element_value_id ?? null;

        $buildingCrackSealingElement = $buildingElements->where('element_id', $crackSealing->id)->first();
        $buildingElementsArray[$crackSealing->id][$crackSealing->short] = $buildingCrackSealingElement->element_value_id ?? null;

        $buildingPaintworkStatusesArray = [
            'last_painted_year' => $buildingPaintworkStatus->last_painted_year,
            'paintwork_status_id' => $buildingPaintworkStatus->paintwork_status_id,
            'wood_rot_status_id' => $buildingPaintworkStatus->wood_rot_status_id,
        ];


        // wall insulation savings
        $wallInsulationSavings = WallInsulation::calculate($building, $user, [
            'cavity_wall'                 => $cavityWall,
            'element'                     => $wallInsulationElementId,
            'insulation_wall_surface'     => $buildingFeature->insulation_wall_surface,
            'wall_joints'                 => $buildingFeature->wall_joins,
            'contaminated_wall_joints'    => $buildingFeature->contaminated_wall_joints,
            'facade_plastered_painted'    => $buildingFeature->facade_plastered_painted,
            'facade_plastered_surface_id' => $buildingFeature->facade_plastered_surface_id,
            'facade_damaged_paintwork_id' => $buildingFeature->facade_damaged_paintwork_id,
        ]);

        $insulatedGlazingSavings = InsulatedGlazing::calculate($building, $user, [
            'user_interests' => $userInterestsForInsulatedGlazing,
            'building_insulated_glazings' => $buildingInsulatedGlazingArray,
            'building_elements' => $buildingElementsArray,
            'window_surface' => $buildingFeature->window_surface,
            'building_paintwork_statuses' => $buildingPaintworkStatusesArray
        ]);

        return [
            'wall-insulation' => $wallInsulationSavings,
            'insulated-glazing' => $insulatedGlazingSavings
        ];
    }
}
