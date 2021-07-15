<?php

namespace App\Helpers;

use App\Helpers\Cooperation\Tool\VentilationHelper;
use App\Helpers\KeyFigures\Heater\KeyFigures as HeaterKeyFigures;
use App\Helpers\KeyFigures\PvPanels\KeyFigures as SolarPanelsKeyFigures;
use App\Helpers\KeyFigures\RoofInsulation\Temperature;
use App\Models\BuildingHeating;
use App\Models\BuildingHeatingApplication;
use App\Models\BuildingType;
use App\Models\ComfortLevelTapWater;
use App\Models\Element;
use App\Models\EnergyLabel;
use App\Models\FacadeDamagedPaintwork;
use App\Models\FacadePlasteredSurface;
use App\Models\FacadeSurface;
use App\Models\InsulatingGlazing;
use App\Models\Interest;
use App\Models\MeasureApplication;
use App\Models\PaintworkStatus;
use App\Models\PvPanelOrientation;
use App\Models\RoofTileStatus;
use App\Models\RoofType;
use App\Models\Service;
use App\Models\Step;
use App\Models\Ventilation;
use App\Models\WoodRotStatus;
use Illuminate\Support\Collection;

class ToolHelperOld
{
    public static function createOptions(Collection $collection, $value = 'name', $id = 'id', $nullPlaceholder = true)
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

    /**
     * @param $contentKey
     *
     * @return array
     */
    public static function getContentStructure($contentKey = null)
    {
        // General data - Elements (that are not queried later on step basis)
        $livingRoomsWindows = Element::findByShort('living-rooms-windows');
        $sleepingRoomsWindows = Element::findByShort('sleeping-rooms-windows');
        // General data - Services (that are not queried later on step basis)
        $heatPump = Service::findByShort('heat-pump');
        $ventilation = Service::findByShort('house-ventilation');
        $buildingHeatingApplications = BuildingHeatingApplication::orderBy('order')->get();

        // Wall insulation
        $wallInsulation = Element::findByShort('wall-insulation');
        $facadeDamages = FacadeDamagedPaintwork::orderBy('order')->get();
        $surfaces = FacadeSurface::orderBy('order')->get();
        $facadePlasteredSurfaces = FacadePlasteredSurface::orderBy('order')->get();
        $energyLabels = EnergyLabel::all();

        // Insulated glazing
        $insulatedGlazings = InsulatingGlazing::all();
        $heatings = BuildingHeating::where('calculate_value', '<', 5)->get(); // we don't want n.v.t.
        $crackSealing = Element::findByShort('crack-sealing');
        $frames = Element::findByShort('frames');
        $woodElements = Element::findByShort('wood-elements');
        $paintworkStatuses = PaintworkStatus::orderBy('order')->get();
        $woodRotStatuses = WoodRotStatus::orderBy('order')->get();

        // Floor insulation
        /** @var Element $floorInsulation */
        $floorInsulation = Element::findByShort('floor-insulation');
        $crawlspace = Element::findByShort('crawlspace');

        // Roof insulation
        $roofInsulation = Element::findByShort('roof-insulation');
        $roofTypes = RoofType::all();
        $roofTileStatuses = RoofTileStatus::orderBy('order')->get();
        // Same as RoofInsulationController->getMeasureApplicationsAdviceMap()
        $roofInsulationMeasureApplications = [
            'flat' => [
                Temperature::ROOF_INSULATION_FLAT_ON_CURRENT => MeasureApplication::where('short',
                    'roof-insulation-flat-current')->first(),
                Temperature::ROOF_INSULATION_FLAT_REPLACE => MeasureApplication::where('short',
                    'roof-insulation-flat-replace-current')->first(),
            ],
            'pitched' => [
                Temperature::ROOF_INSULATION_PITCHED_INSIDE => MeasureApplication::where('short',
                    'roof-insulation-pitched-inside')->first(),
                Temperature::ROOF_INSULATION_PITCHED_REPLACE_TILES => MeasureApplication::where('short',
                    'roof-insulation-pitched-replace-tiles')->first(),
            ],
        ];

        // High efficiency boiler
        // NOTE: building element hr-boiler tells us if it's there
        $hrBoiler = Service::findByShort('hr-boiler');
        $boiler = Service::findByShort('boiler');

        // Solar panels
        $solarPanels = Service::findByShort('total-sun-panels');
        $solarPanelsOptionsPeakPower = ['' => '-'] + SolarPanelsKeyFigures::getPeakPowers();
        $solarPanelsOptionsAngle = ['' => '-'] + SolarPanelsKeyFigures::getAngles();

        $heater = Service::findByShort('sun-boiler');
        $heaterOptionsAngle = ['' => '-'] + HeaterKeyFigures::getAngles();

        $comfortLevelsTapWater = ComfortLevelTapWater::all();

        $buildingTypes = BuildingType::all();
        $buildingHeatings = BuildingHeating::all();
        $boilerTypes = $boiler->values()->orderBy('order')->get();

        // Common
        $interests = Interest::orderBy('order')->get();
        $interestOptions = static::createOptions($interests);

        $stepUserInterestKey = 'user_interests.'.Step::class.'.';
        $measureApplicationInterestKey = 'user_interests.'.MeasureApplication::class.'.';

        $structure = [
            'general-data' => [
                'current-state' => [
                    // elements and services
                    'element.'.$crackSealing->id => [
                        'label' => $crackSealing->name,
                        'type' => 'select',
                        'options' => static::createOptions($crackSealing->values()->orderBy('order')->get(), 'value'),
                    ],

                    'service.'.$hrBoiler->id => [
                        'label' => $hrBoiler->name,
                        'type' => 'select',
                        'options' => static::createOptions($hrBoiler->values()->orderBy('order')->get(), 'value'),
                    ],
//                    'service.'.$boiler->id.'.service_value_id' => [
//                        'label' => __('boiler.boiler-type.title'),
//                        'type' => 'select',
//                        'options' => static::createOptions($boiler->values()->orderBy('order')->get(), 'value'),
//                    ],
                    'building_features.building_heating_application_id' => [
                        'label' => __('cooperation/tool/general-data/current-state.index.building-heating-applications.title'),
                        'type' => 'select',
                        'options' => static::createOptions($buildingHeatingApplications),
                    ],

                    'service.'.$heatPump->id => [
                        'label' => $heatPump->name,
                        'type' => 'select',
                        'options' => static::createOptions($heatPump->values()->orderBy('order')->get(), 'value'),
                    ],

                    'service.'.$solarPanels->id.'.extra.value' => [
                        'label' => $solarPanels->name,
                        'type' => 'text',
                        'unit' => __('general.unit.pieces.title'),
                    ],

                    'building_pv_panels.total_installed_power' => [
                        'label' => __('cooperation/tool/general-data/current-state.index.installed-power.title'),
                        'type' => 'text',
                        'unit' => __('general.unit.wp.title'),
                    ],
                    'service.'.$solarPanels->id.'.extra.year' => [
                        'label' => __('cooperation/tool/general-data/current-state.index.service.total-sun-panels.year.title'),
                        'type' => 'text',
                        'unit' => __('general.unit.year.title'),
                    ],

                    // services
                    'service.'.$heater->id => [
                        'label' => $heater->name,
                        'type' => 'select',
                        'options' => static::createOptions($heater->values()->orderBy('order')->get(), 'value'),
                    ],
                    // ventilation
                    'service.'.$ventilation->id.'.service_value_id' => [
                        'label' => $ventilation->name,
                        'type' => 'select',
                        'options' => static::createOptions($ventilation->values()->orderBy('order')->get(), 'value'),
                    ],
                    'service.'.$ventilation->id.'.extra.demand_driven' => [
                        'label' => __('cooperation/tool/general-data/current-state.index.service.house-ventilation.demand-driven.title'),
                        'type' => 'select',
                        'options' => [
                            false => '-',
                            true => __('cooperation/tool/general-data/current-state.index.service.house-ventilation.demand-driven.title'),
                        ],
                    ],
                    'service.'.$ventilation->id.'.extra.heat_recovery' => [
                        'label' => __('cooperation/tool/general-data/current-state.index.service.house-ventilation.heat-recovery.title'),
                        'type' => 'select',
                        'options' => [
                            false => '-',
                            true => __('cooperation/tool/general-data/current-state.index.service.house-ventilation.heat-recovery.title'),
                        ],
                    ],
                ],
                'usage' => [
                    'user_energy_habits.resident_count' => [
                        'label' => __('cooperation/tool/general-data/usage.index.water-gas.resident-count.title'),
                        'type' => 'text',
                    ],
                    'user_energy_habits.water_comfort_id' => [
                        'label' => __('cooperation/tool/general-data/usage.index.water-gas.water-comfort.title'),
                        'type' => 'select',
                        'options' => static::createOptions($comfortLevelsTapWater),
                    ],
                    'user_energy_habits.cook_gas' => [
                        'label' => __('cooperation/tool/general-data/usage.index.water-gas.cook-gas.title'),
                        'type' => 'select',
                        'options' => [
                            1 => __('woningdossier.cooperation.radiobutton.yes'),
                            2 => __('woningdossier.cooperation.radiobutton.no'),
                        ],
                    ],
                ],
                // interests come later on
            ],
            'ventilation' => [
                '-' => [
                    'building_ventilations.how' => [
                        'label' => __('cooperation/tool/ventilation.index.how.title'),
                        'type' => 'multiselect',
                        'options' => VentilationHelper::getHowValues(),
                    ],

                    'building_ventilations.living_situation' => [
                        'label' => __('cooperation/tool/ventilation.index.living-situation.title'),
                        'type' => 'multiselect',
                        'options' => VentilationHelper::getLivingSituationValues(),
                    ],

                    'building_ventilations.usage' => [
                        'label' => __('cooperation/tool/ventilation.index.usage.title'),
                        'type' => 'multiselect',
                        'options' => VentilationHelper::getUsageValues(),
                    ],
                    'calculations' => [
                        'savings_gas' => __('ventilation.costs.gas.title'),
                        'savings_co2' => __('ventilation.costs.co2.title'),
                        'savings_money' => __('cooperation/tool/ventilation.index.savings-in-euro.title'),
                        'cost_indication' => __('cooperation/tool/ventilation.index.indicative-costs.title'),
                        'interest_comparable' => __('cooperation/tool/ventilation.index.comparable-rent.title'),
                    ],
                ],
            ],

            'wall-insulation' => [
                '-' => [
                    $stepUserInterestKey.$wallInsulation->id.'interest_id' => [
                        //'label' => __('general.change-interested.title', ['item' => $livingRoomsWindows->name]),
                        'label' => $wallInsulation->name.': '.__('wall-insulation.index.interested-in-improvement.title'),
                        'type' => 'select',
                        'options' => $interestOptions,
                    ],
                    'building_features.cavity_wall' => [
                        'label' => __('wall-insulation.intro.has-cavity-wall.title'),
                        'type' => 'select',
                        'options' => [
                            0 => __('woningdossier.cooperation.radiobutton.unknown'),
                            1 => __('woningdossier.cooperation.radiobutton.yes'),
                            2 => __('woningdossier.cooperation.radiobutton.no'),
                        ],
                    ],
                    'building_features.facade_plastered_painted' => [
                        'label' => __('wall-insulation.intro.is-facade-plastered-painted.title'),
                        'type' => 'select',
                        'options' => [
                            1 => __('woningdossier.cooperation.radiobutton.yes'),
                            2 => __('woningdossier.cooperation.radiobutton.no'),
                            3 => __('woningdossier.cooperation.radiobutton.mostly'),
                        ],
                    ],
                    'building_features.facade_plastered_surface_id' => [
                        'label' => __('wall-insulation.intro.surface-paintwork.title'),
                        'type' => 'select',
                        'options' => static::createOptions($facadePlasteredSurfaces),
                    ],
                    'building_features.facade_damaged_paintwork_id' => [
                        'label' => __('wall-insulation.intro.damage-paintwork.title'),
                        'type' => 'select',
                        'options' => static::createOptions($facadeDamages),
                        'relationship' => 'damagedPaintwork',
                    ],
                    'building_features.wall_joints' => [
                        'label' => __('wall-insulation.optional.flushing.title'),
                        'type' => 'select',
                        'options' => static::createOptions($surfaces),
                    ],
                    'building_features.contaminated_wall_joints' => [
                        'label' => __('wall-insulation.optional.is-facade-dirty.title'),
                        'type' => 'select',
                        'options' => static::createOptions($surfaces),
                    ],
                    'building_features.wall_surface' => [
                        'label' => __('wall-insulation.optional.facade-surface.title'),
                        'type' => 'text',
                        'unit' => __('general.unit.square-meters.title'),
                    ],
                    'building_features.insulation_wall_surface' => [
                        'label' => __('wall-insulation.optional.insulated-surface.title'),
                        'type' => 'text',
                        'unit' => __('general.unit.square-meters.title'),
                    ],

                    'calculations' => [
                        'savings_gas' => __('wall-insulation.index.costs.gas.title'),
                        'savings_co2' => __('wall-insulation.index.costs.co2.title'),
                        'savings_money' => __('wall-insulation.index.savings-in-euro.title'),
                        'cost_indication' => __('wall-insulation.index.indicative-costs.title'),
                        'interest_comparable' => __('wall-insulation.index.comparable-rent.title'),

                        'repair_joint' => [
                            'costs' => __('wall-insulation.taking-into-account.repair-joint.title'),
                            'year' => __('wall-insulation.taking-into-account.repair-joint.year.title'),
                        ],
                        'clean_brickwork' => [
                            'costs' => __('wall-insulation.taking-into-account.clean-brickwork.title'),
                            'year' => __('wall-insulation.taking-into-account.clean-brickwork.year.title'),
                        ],

                        'impregnate_wall' => [
                            'costs' => __('wall-insulation.taking-into-account.impregnate-wall.title'),
                            'year' => __('wall-insulation.taking-into-account.impregnate-wall.year.title'),
                        ],

                        'paint_wall' => [
                            'costs' => __('wall-insulation.taking-into-account.wall-painting.title'),
                            'year' => __('wall-insulation.taking-into-account.wall-painting.year.title'),
                        ],
                    ],
                ],
            ],

            'insulated-glazing' => [
                '-' => [
                    'building_features.window_surface' => [
                        'label' => __('insulated-glazing.windows-surface.title'),
                        'type' => 'text',
                        'unit' => __('general.unit.square-meters.title'),
                    ],
                    'element.'.$frames->id => [
                        'label' => __('insulated-glazing.paint-work.which-frames.title'),
                        'type' => 'select',
                        'options' => static::createOptions($frames->values()->orderBy('order')->get(), 'value'),
                    ],
                    'element.'.$woodElements->id => [
                        'label' => __('insulated-glazing.paint-work.other-wood-elements.title'),
                        'type' => 'multiselect',
                        'options' => static::createOptions($woodElements->values()->orderBy('order')->get(), 'value'),
                    ],
                    'building_paintwork_statuses.last_painted_year' => [
                        'label' => __('insulated-glazing.paint-work.last-paintjob.title'),
                        'type' => 'text',
                        'unit' => __('general.unit.year.title'),
                    ],
                    'building_paintwork_statuses.paintwork_status_id' => [
                        'label' => __('insulated-glazing.paint-work.paint-damage-visible.title'),
                        'type' => 'select',
                        'options' => static::createOptions($paintworkStatuses),
                        'relationship' => 'paintworkStatus',
                    ],
                    'building_paintwork_statuses.wood_rot_status_id' => [
                        'label' => __('insulated-glazing.paint-work.wood-rot-visible.title'),
                        'type' => 'select',
                        'options' => static::createOptions($woodRotStatuses),
                    ],
                ],
            ],

            'floor-insulation' => [
                '-' => [
                    $stepUserInterestKey.Step::findByShort('floor-insulation')->id.'.interest_id' => [
                        //'label' => __('general.change-interested.title', ['item' => $livingRoomsWindows->name]),
                        'label' => $floorInsulation->name.': '.__('floor-insulation.index.interested-in-improvement.title'),
                        'type' => 'select',
                        'options' => $interestOptions,
                    ],
                    'element.'.$crawlspace->id.'.extra.has_crawlspace' => [
                        'label' => __('floor-insulation.has-crawlspace.title'),
                        'type' => 'select',
                        'options' => __('woningdossier.cooperation.option'),
                    ],
                    'element.'.$crawlspace->id.'.extra.access' => [
                        'label' => __('floor-insulation.crawlspace-access.title'),
                        'type' => 'select',
                        'options' => __('woningdossier.cooperation.option'),
                    ],
                    'element.'.$crawlspace->id.'.element_value_id' => [
                        'label' => __('floor-insulation.crawlspace-height.title'),
                        'type' => 'select',
                        'options' => static::createOptions($crawlspace->values()->orderBy('order')->get(), 'value'),
                    ],
                    'building_features.floor_surface' => [
                        'label' => __('floor-insulation.surface.title'),
                        'type' => 'text',
                        'unit' => __('general.unit.square-meters.title'),
                    ],
                    'building_features.insulation_surface' => [
                        'label' => __('floor-insulation.insulation-surface.title'),
                        'type' => 'text',
                        'unit' => __('general.unit.square-meters.title'),
                    ],

                    'calculations' => [
                        'savings_gas' => __('floor-insulation.index.costs.gas.title'),
                        'savings_co2' => __('floor-insulation.index.costs.co2.title'),
                        'savings_money' => __('floor-insulation.index.savings-in-euro.title'),
                        'cost_indication' => __('floor-insulation.index.indicative-costs.title'),
                        'interest_comparable' => __('floor-insulation.index.comparable-rent.title'),
                    ],
                ],
            ],

            'roof-insulation' => [
                '-' => [
                    $stepUserInterestKey.Step::findByShort('roof-insulation')->id.'.interest_id' => [
                        //'label' => __('general.change-interested.title', ['item' => $livingRoomsWindows->name]),
                        'label' => $roofInsulation->name.': '.__('roof-insulation.index.interested-in-improvement.title'),
                        'type' => 'select',
                        'options' => $interestOptions,
                    ],
                    'building_features.roof_type_id' => [
                        'label' => __('roof-insulation.current-situation.main-roof.title'),
                        'type' => 'select',
                        'options' => static::createOptions($roofTypes),
                        'relationship' => 'roofType',
                    ],
                ],
                // rest will be added later on
            ],

            'high-efficiency-boiler' => [
                '-' => [
                    $stepUserInterestKey.Step::findByShort('high-efficiency-boiler')->id.'.interest_id' => [
                        //'label' => __('general.change-interested.title', ['item' => $livingRoomsWindows->name]),
                        'label' => $hrBoiler->name.': '.__('high-efficiency-boiler.index.interested-in-improvement.title'),
                        'type' => 'select',
                        'options' => $interestOptions,
                    ],
                    'user_energy_habits.resident_count' => [
                        'label' => __('cooperation/tool/high-efficiency-boiler.index.resident-count.title'),
                        'type' => 'text',
                    ],
                    'user_energy_habits.amount_gas' => [
                        'label' => __('cooperation/tool/high-efficiency-boiler.index.gas-usage.title'),
                        'type' => 'text',
                        'unit' => __('general.unit.cubic-meters.title'),
                    ],
                    'service.'.$boiler->id.'.service_value_id' => [
                        'label' => __('high-efficiency-boiler.boiler-type.title'),
                        'type' => 'select',
//                    'options' => $boilerTypes
                        'options' => static::createOptions($boilerTypes, 'value'),
                    ],
                    'service.'.$boiler->id.'.extra.date' => [
                        'label' => __('boiler.boiler-placed-date.title'),
                        'type' => 'text',
                        'unit' => __('general.unit.year.title'),
                    ],
                    'calculations' => [
                        'savings_gas' => __('high-efficiency-boiler.index.costs.gas.title'),
                        'savings_co2' => __('high-efficiency-boiler.index.costs.co2.title'),
                        'savings_money' => __('high-efficiency-boiler.index.savings-in-euro.title'),
                        'cost_indication' => __('high-efficiency-boiler.index.indicative-costs.title'),
                        'interest_comparable' => __('high-efficiency-boiler.index.comparable-rent.title'),

                        'replace_year' => __('high-efficiency-boiler.indication-for-costs.indicative-replacement.title'),
                    ],
                ],
            ],
            'solar-panels' => [
                '-' => [
                    $stepUserInterestKey.Step::findByShort('solar-panels')->id.'.interest_id' => [
                        //'label' => __('general.change-interested.title', ['item' => $livingRoomsWindows->name]),
                        'label' => __('solar-panels.index.interested-in-improvement.title'),
                        'type' => 'select',
                        'options' => $interestOptions,
                    ],
                    'user_energy_habits.amount_electricity' => [
                        'label' => __('solar-panels.electra-usage.title'),
                        'type' => 'text',
                        'unit' => __('general.unit.cubic-meters.title'),
                    ],
                    'building_pv_panels.peak_power' => [
                        'label' => __('solar-panels.peak-power.title'),
                        'type' => 'select',
                        'options' => $solarPanelsOptionsPeakPower,
                    ],
                    'building_pv_panels.number' => [
                        'label' => __('solar-panels.number.title'),
                        'type' => 'text',
                        'unit' => __('general.unit.pieces.title'),
                    ],
                    'building_pv_panels.pv_panel_orientation_id' => [
                        'label' => __('solar-panels.pv-panel-orientation-id.title'),
                        'type' => 'select',
                        'options' => static::createOptions(PvPanelOrientation::orderBy('order')->get()),
                        'relationship' => 'orientation',
                    ],
                    'building_pv_panels.angle' => [
                        'label' => __('solar-panels.angle.title'),
                        'type' => 'select',
                        'options' => $solarPanelsOptionsAngle,
                    ],

                    'calculations' => [
                        'yield_electricity' => __('solar-panels.indication-for-costs.yield-electricity.title'),
                        'raise_own_consumption' => __('solar-panels.indication-for-costs.raise-own-consumption.title'),

                        'savings_co2' => __('solar-panels.index.costs.co2.title'),
                        'savings_money' => __('solar-panels.index.savings-in-euro.title'),
                        'cost_indication' => __('solar-panels.index.indicative-costs.title'),
                        'interest_comparable' => __('solar-panels.index.comparable-rent.title'),
                    ],
                ],
            ],

            'heater' => [
                '-' => [
                    $stepUserInterestKey.Step::findByShort('heater')->id.'.interest_id' => [
                        //'label' => __('general.change-interested.title', ['item' => $livingRoomsWindows->name]),
                        'label' => $heater->name.': '.__('heater.index.interested-in-improvement.title'),
                        'type' => 'select',
                        'options' => $interestOptions,
                    ],
                    'user_energy_habits.water_comfort_id' => [
                        'label' => __('heater.comfort-level-warm-tap-water.title'),
                        'type' => 'select',
                        'options' => self::createOptions($comfortLevelsTapWater),
                    ],
                    'building_heaters.pv_panel_orientation_id' => [
                        'label' => __('heater.pv-panel-orientation-id.title'),
                        'type' => 'select',
                        'options' => static::createOptions(PvPanelOrientation::orderBy('order')->get()),
                        'relationship' => 'orientation',
                    ],
                    'building_heaters.angle' => [
                        'label' => __('heater.angle.title'),
                        'type' => 'select',
                        'options' => $heaterOptionsAngle,
                    ],

                    'calculations' => [
                        'consumption' => [
                            'water' => __('heater.consumption-water.title'),
                            'gas' => __('heater.consumption-gas.title'),
                        ],

                        'specs' => [
                            'size_boiler' => __('heater.size-boiler.title'),
                            'size_collector' => __('heater.size-collector.title'),
                        ],
                        'production_heat' => __('heater.indication-for-costs.production-heat.title'),
                        'percentage_consumption' => __('heater.indication-for-costs.percentage-consumption.title'),
                        'savings_gas' => __('heater.index.costs.gas.title'),
                        'savings_co2' => __('heater.index.costs.co2.title'),
                        'savings_money' => __('heater.index.savings-in-euro.title'),
                        'cost_indication' => __('heater.index.indicative-costs.title'),
                        'interest_comparable' => __('heater.index.comparable-rent.title'),
                    ],
                ],
            ],
        ];

        $steps = Step::withoutSubSteps()->get();

        $steps = $steps->keyBy('short')->forget('general-data');
        // todo: remove the information pull and if.
        $ventilationInformation = $steps->pull('ventilation-information');
        // because the ventilation branch
        if (is_null($ventilationInformation)) {
            $ventilationInformation = $steps->pull('ventilation');
        }
        $steps->push($ventilationInformation);

        foreach ($steps as $step) {
//            <select id="user_interest" class="form-control" name="user_interests[{{$step->id}}][interest_id]">
            $structure['general-data']['interest'][$stepUserInterestKey.$step->id.'.interest_id'] = [
                'label' => $step->name,
                'type' => 'select',
                'options' => $interestOptions,
            ];
        }
        $structure['general-data']['interest']['user_energy_habits.renovation_plans'] = [
            'label' => __('cooperation/tool/general-data/interest.index.motivation.renovation-plans.title'),
            'type' => 'select',
            'options' => [
                1 => __('cooperation/tool/general-data/interest.index.motivation.renovation-plans.options.yes-within-2-year'),
                2 => __('cooperation/tool/general-data/interest.index.motivation.renovation-plans.options.yes-within-5-year'),
                0 => __('cooperation/tool/general-data/interest.index.motivation.renovation-plans.options.none'),
            ],
        ];
        $structure['general-data']['interest']['user_energy_habits.building_complaints'] = [
            'label' => __('cooperation/tool/general-data/interest.index.motivation.building-complaints.title'),
            'type' => 'text',
        ];

        // Insulated glazing
        $igShorts = [
            'hrpp-glass-only', 'hrpp-glass-frames', 'hr3p-frames', 'glass-in-lead',
        ];

        // As Darkwing Duck would say: Let's get dangerous
        //
        // We're going to get all insulated-glazing stuff out of the array,
        // put all measure applications in first, then put the stuff back and
        // then add the calculations.
        // The order was accepted for the example buildings, but not for the
        // CSV files. And the order should be the same, but not "the same".
        // Makes sense.. no?
        // (hint: no it doesn't..)
        $insulatedGlazingStuffSoFar = $structure['insulated-glazing']['-'];
        $structure['insulated-glazing'] = [];

        foreach ($igShorts as $igShort) {
            $measureApplication = MeasureApplication::where('short', $igShort)->first();
            if ($measureApplication instanceof MeasureApplication) {
                $structure['insulated-glazing']['-'][$measureApplicationInterestKey.$measureApplication->id.'.interest_id'] = [
                    //'label' => 'Interest in '.$measureApplication->measure_name,
                    'label' => __('general.change-interested.title',
                        ['item' => $measureApplication->measure_name]),
                    'type' => 'select',
                    'options' => $interestOptions,
                ];
                $structure['insulated-glazing']['-']['building_insulated_glazings.'.$measureApplication->id.'.insulating_glazing_id'] = [
                    'label' => $measureApplication->measure_name.': '.__('insulated-glazing.'.$measureApplication->short.'.current-glass.title'),
                    'type' => 'select',
                    'options' => static::createOptions($insulatedGlazings),
                    'relationship' => 'insulatedGlazing',
                ];
                $structure['insulated-glazing']['-']['building_insulated_glazings.'.$measureApplication->id.'.building_heating_id'] = [
                    'label' => $measureApplication->measure_name.': '.__('insulated-glazing.'.$measureApplication->short.'.rooms-heated.title'),
                    'type' => 'select',
                    'options' => static::createOptions($heatings),
                    'relationship' => 'buildingHeating',
                ];
                $structure['insulated-glazing']['-']['building_insulated_glazings.'.$measureApplication->id.'.m2'] = [
                    'label' => $measureApplication->measure_name.': '.__('insulated-glazing.'.$measureApplication->short.'.m2.title'),
                    'type' => 'text',
                    'unit' => __('general.unit.square-meters.title'),
                ];
                $structure['insulated-glazing']['-']['building_insulated_glazings.'.$measureApplication->id.'.windows'] = [
                    'label' => $measureApplication->measure_name.': '.__('insulated-glazing.'.$measureApplication->short.'.window-replace.title'),
                    'type' => 'text',
                ];
            }
        }
        foreach ($insulatedGlazingStuffSoFar as $igK => $igV) {
            $structure['insulated-glazing']['-'][$igK] = $igV;
        }

        // set the calculations on the end because of the order
        $structure['insulated-glazing']['-']['calculations'] = [
            'savings_gas' => __('insulated-glazing.index.costs.gas.title'),
            'savings_co2' => __('insulated-glazing.index.costs.co2.title'),
            'savings_money' => __('insulated-glazing.index.savings-in-euro.title'),
            'cost_indication' => __('insulated-glazing.index.indicative-costs.title'),
            'interest_comparable' => __('insulated-glazing.index.comparable-rent.title'),

            'paintwork' => [
                'costs' => __('insulated-glazing.taking-into-account.paintwork.title'),
                'year' => __('insulated-glazing.taking-into-account.paintwork_year.title'),
            ],
        ];

        // Roof insulation
        // have to refactor this
        // pitched = 1
        // flat = 2
        $pitched = new \stdClass();
        $pitched->id = 1;
        $pitched->short = 'pitched';
        $flat = new \stdClass();
        $flat->id = 2;
        $flat->short = 'flat';
        $roofTypes1 = collect([$pitched, $flat]);

        // $roofTypes1 should become $roofTypes->where('short', '!=', 'none');

        foreach ($roofTypes1 as $roofType) {
            $structure['roof-insulation']['-']['building_roof_types.'.$roofType->id.'.element_value_id'] = [
                'label' => __('roof-insulation.current-situation.is-'.$roofType->short.'-roof-insulated.title'),
                'type' => 'select',
                'options' => static::createOptions($roofInsulation->values, 'value'),
                'relationship' => 'elementValue',
            ];
            $structure['roof-insulation']['-']['building_roof_types.'.$roofType->id.'.roof_surface'] = [
                'label' => __('roof-insulation.current-situation.'.$roofType->short.'-roof-surface.title'),
                'type' => 'text',
                'unit' => __('general.unit.square-meters.title'),
            ];
            $structure['roof-insulation']['-']['building_roof_types.'.$roofType->id.'.insulation_roof_surface'] = [
                'label' => __('roof-insulation.current-situation.insulation-'.$roofType->short.'-roof-surface.title'),
                'type' => 'text',
                'unit' => __('general.unit.square-meters.title'),
            ];
            $structure['roof-insulation']['-']['building_roof_types.'.$roofType->id.'.extra.zinc_replaced_date'] = [
                'label' => __('roof-insulation.current-situation.zinc-replaced.title'),
                'type' => 'text',
                'unit' => __('general.unit.year.title'),
            ];
            if ('flat' == $roofType->short) {
                $structure['roof-insulation']['-']['building_roof_types.'.$roofType->id.'.extra.bitumen_replaced_date'] = [
                    'label' => __('roof-insulation.current-situation.bitumen-insulated.title'),
                    'type' => 'text',
                    'unit' => __('general.unit.year.title'),
                ];
            }
            if ('pitched' == $roofType->short) {
                $structure['roof-insulation']['-']['building_roof_types.'.$roofType->id.'.extra.tiles_condition'] = [
                    'label' => __('roof-insulation.current-situation.in-which-condition-tiles.title'),
                    'type' => 'select',
                    'options' => static::createOptions($roofTileStatuses),
                ];
            }
            $structure['roof-insulation']['-']['building_roof_types.'.$roofType->id.'.extra.measure_application_id'] = [
                'label' => __('roof-insulation.'.$roofType->short.'-roof.insulate-roof.title'),
                'type' => 'select',
                'options' => static::createOptions(collect($roofInsulationMeasureApplications[$roofType->short]),
                    'measure_name'),
                'relationship' => 'measureApplication',
            ];
            $structure['roof-insulation']['-']['building_roof_types.'.$roofType->id.'.building_heating_id'] = [
                'label' => __('roof-insulation.'.$roofType->short.'-roof.situation.title'),
                'type' => 'select',
                'options' => static::createOptions($heatings),
                'relationship' => 'heating',
            ];

            if ($roofType->short == $roofTypes1->last()->short) {
                $structure['roof-insulation']['-']['calculations'] = [
                    'flat' => [
                        'savings_gas' => __('roof-insulation.flat.costs.gas.title'),
                        'savings_co2' => __('roof-insulation.flat.costs.co2.title'),
                        'savings_money' => __('roof-insulation.index.savings-in-euro.title'),
                        'cost_indication' => __('roof-insulation.index.indicative-costs.title'),
                        'interest_comparable' => __('roof-insulation.index.comparable-rent.title'),

                        'replace' => [
                            'costs' => __('roof-insulation.flat.indicative-costs-replacement.title'),
                            'year' => __('roof-insulation.flat.indicative-replacement.year.title'),
                        ],
                    ],
                    'pitched' => [
                        'savings_gas' => __('roof-insulation.pitched.costs.gas.title'),
                        'savings_co2' => __('roof-insulation.pitched.costs.co2.title'),
                        'savings_money' => __('roof-insulation.index.savings-in-euro.title'),
                        'cost_indication' => __('roof-insulation.index.indicative-costs.title'),
                        'interest_comparable' => __('roof-insulation.index.comparable-rent.title'),

                        'replace' => [
                            'costs' => __('roof-insulation.pitched.indicative-costs-replacement.title'),
                            'year' => __('roof-insulation.pitched.indicative-replacement.year.title'),
                        ],
                    ],
                ];
            }
        }

        // and here we will add the interest options for the ventilation information
        $measureApplicationsForVentilation = MeasureApplication::whereIn('short', [
            'ventilation-balanced-wtw',
            'ventilation-decentral-wtw',
            'ventilation-demand-driven',
            'crack-sealing',
        ])->get();

        foreach ($measureApplicationsForVentilation as $measureApplication) {
            $structure['ventilation']['-'][$measureApplicationInterestKey.$measureApplication->id.'.interest_id'] = [
                //'label' => 'Interest in '.$measureApplication->measure_name,
//                'label' => __('general.change-interested.title', ['item' => $measureApplication->measure_name]),
                'label' => $measureApplication->measure_name,
                'type' => 'select',
                'options' => $interestOptions,
            ];
        }

        // when a content key is set, we will try to retrieve the specific content from the structure.
        if (! is_null($contentKey)) {
            $contentKeyData = explode('.', $contentKey, 3);
            $step = $contentKeyData[0];
            $subStep = $contentKeyData[1];
            $contentKey = $contentKeyData[2];

            return $structure[$step][$subStep][$contentKey];
        }

        return $structure;
    }
}
