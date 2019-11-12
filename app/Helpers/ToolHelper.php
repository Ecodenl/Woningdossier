<?php

namespace App\Helpers;

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
use App\Models\WoodRotStatus;
use Illuminate\Support\Collection;

class ToolHelper
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

    public static function getStructure()
    {
        // General data - Elements (that are not queried later on step basis)
        $livingRoomsWindows = Element::where('short', 'living-rooms-windows')->first();
        $sleepingRoomsWindows = Element::where('short', 'sleeping-rooms-windows')->first();
        // General data - Services (that are not queried later on step basis)
        $heatPump = Service::where('short', 'heat-pump')->first();
        $ventilation = Service::where('short', 'house-ventilation')->first();
        $buildingHeatingApplications = BuildingHeatingApplication::orderBy('order')->get();

        // Wall insulation
        $wallInsulation = Element::where('short', 'wall-insulation')->first();
        $facadeDamages = FacadeDamagedPaintwork::orderBy('order')->get();
        $surfaces = FacadeSurface::orderBy('order')->get();
        $facadePlasteredSurfaces = FacadePlasteredSurface::orderBy('order')->get();
        $energyLabels = EnergyLabel::all();

        // Insulated glazing
        $insulatedGlazings = InsulatingGlazing::all();
        $heatings = BuildingHeating::where('calculate_value', '<', 5)->get(); // we don't want n.v.t.
        $crackSealing = Element::where('short', 'crack-sealing')->first();
        $frames = Element::where('short', 'frames')->first();
        $woodElements = Element::where('short', 'wood-elements')->first();
        $paintworkStatuses = PaintworkStatus::orderBy('order')->get();
        $woodRotStatuses = WoodRotStatus::orderBy('order')->get();

        // Floor insulation
        /** @var Element $floorInsulation */
        $floorInsulation = Element::where('short', 'floor-insulation')->first();
        $crawlspace = Element::where('short', 'crawlspace')->first();

        // Roof insulation
        $roofInsulation = Element::where('short', 'roof-insulation')->first();
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
        $hrBoiler = Service::where('short', 'hr-boiler')->first();
        $boiler = Service::where('short', 'boiler')->first();

        // Solar panels
        $solarPanels = Service::where('short', 'total-sun-panels')->first();
        $solarPanelsOptionsPeakPower = ['' => '-'] + SolarPanelsKeyFigures::getPeakPowers();
        $solarPanelsOptionsAngle = ['' => '-'] + SolarPanelsKeyFigures::getAngles();

        $heater = Service::where('short', 'sun-boiler')->first();
        $heaterOptionsAngle = ['' => '-'] + HeaterKeyFigures::getAngles();

        $comfortLevelsTapWater = ComfortLevelTapWater::all();

        $buildingTypes = BuildingType::all();
        $buildingHeatings = BuildingHeating::all();
        $boilerTypes = $boiler->values()->orderBy('order')->get();

        // Common
        $interests = Interest::orderBy('order')->get();
        $interestOptions = static::createOptions($interests);

        $structure = [
            'general-data' => [
                'building-characteristics' => [
                    'building_features.building_type_id' => [
                        'label' => __('cooperation/tool/general-data/building-characteristics.index.building-type.title'),
                        'type' => 'select',
                        'options' => static::createOptions($buildingTypes),
                    ],
                    'building_features.build_year' => [
                        'label' => __('cooperation/tool/general-data/building-characteristics.index.build-year.title'),
                        'type' => 'text',
                    ],
                    'building_features.surface' => [
                        'label' => __('cooperation/tool/general-data/building-characteristics.index.surface.title'),
                        'type' => 'text',
                        'unit' => __('general.unit.square-meters.title'),
                    ],
                    'building_features.building_layers' => [
                        'label' => __('cooperation/tool/general-data/building-characteristics.index.building-layers.title'),
                        'type' => 'text',
                    ],
                    'building_features.roof_type_id' => [
                        'label' => __('cooperation/tool/general-data/building-characteristics.index.roof-type.title'),
                        'type' => 'select',
                        'options' => static::createOptions($roofTypes),
                    ],
                    'building_features.energy_label_id' => [
                        'label' => __('cooperation/tool/general-data/building-characteristics.index.energy-label.title'),
                        'type' => 'select',
                        'options' => static::createOptions($energyLabels),
                    ],
                    'building_features.monument' => [
                        'label' => __('cooperation/tool/general-data/building-characteristics.index.monument.title'),
                        'type' => 'select',
                        'options' => [
                            1 => __('woningdossier.cooperation.radiobutton.yes'),
                            2 => __('woningdossier.cooperation.radiobutton.no'),
                            0 => __('woningdossier.cooperation.radiobutton.unknown'),
                        ],
                    ],
                ],
                'current-state' => [
                    // elements and services
                    'element.' . $livingRoomsWindows->id => [
                        //'label' => __('general.change-interested.title', ['item' => $livingRoomsWindows->name]),
                        'label' => $livingRoomsWindows->name,
                        'type' => 'select',
                        'options' => self::createOptions($livingRoomsWindows->values()->orderBy('order')->get(), 'value'),
                    ],
                    'element.' . $sleepingRoomsWindows->id => [
                        //'label' => __('general.change-interested.title', ['item' => $livingRoomsWindows->name]),
                        'label' => $sleepingRoomsWindows->name,
                        'type' => 'select',
                        'options' => self::createOptions($sleepingRoomsWindows->values()->orderBy('order')->get(), 'value'),
                    ],
                    'element.' . $wallInsulation->id => [
//                    'label'   => __('wall-insulation.intro.filled-insulation.title'),
                        'label' => $wallInsulation->name,
                        'type' => 'select',
                        'options' => static::createOptions($wallInsulation->values()->orderBy('order')->get(), 'value'),
                    ],
                    'element.' . $floorInsulation->id => [
//                    'label'   => __('floor-insulation.floor-insulation.title'),
                        'label' => $floorInsulation->name,
                        'type' => 'select',
                        'options' => static::createOptions($floorInsulation->values()->orderBy('order')->get(), 'value'),
                    ],
                    'element.' . $roofInsulation->id => [
                        'label' => $roofInsulation->name,
                        'type' => 'select',
                        'options' => static::createOptions($roofInsulation->values()->orderBy('order')->get(), 'value'),
                    ],


                    'service.' . $boiler->id . '.service_value_id' => [
                        'label' => __('boiler.boiler-type.title'),
                        'type' => 'select',
                        'options' => static::createOptions($boiler->values()->orderBy('order')->get(), 'value'),
                    ],

                    'service.' . $hrBoiler->id => [
                        'label' => $hrBoiler->name,
                        'type' => 'select',
                        'options' => static::createOptions($hrBoiler->values()->orderBy('order')->get(), 'value'),
                    ],

                    'building_features.building_heating_application_id' => [
                        'label' => __('cooperation/tool/general-data/current-state.index.building-heating-applications.title'),
                        'type' => 'select',
                        'options' => static::createOptions($buildingHeatingApplications),
                    ],

                    'service.' . $heatPump->id => [
                        'label' => $heatPump->name,
                        'type' => 'select',
                        'options' => static::createOptions($heatPump->values()->orderBy('order')->get(), 'value'),
                    ],

                    'service.' . $solarPanels->id . '.extra.value' => [
                        'label' => $solarPanels->name,
                        'type' => 'text',
                        'unit' => __('general.unit.pieces.title'),
                    ],

                    'building_pv_panels.total_installed_power' => [
                        'label' => __('cooperation/tool/general-data/current-state.index.installed-power.title'),
                        'type' => 'text',
                        'unit' => __('general.unit.wp.title'),
                    ],
                    'service.' . $solarPanels->id . '.extra.year' => [
                        'label' => __('cooperation/tool/general-data/current-state.index.service.total-sun-panels.year.title'),
                        'type' => 'text',
                        'unit' => __('general.unit.year.title'),
                    ],

                    // services
                    'service.' . $heater->id => [
                        'label' => $heater->name,
                        'type' => 'select',
                        'options' => static::createOptions($heater->values()->orderBy('order')->get(), 'value'),
                    ],
                    // ventilation
                    'service.' . $ventilation->id . '.service_value_id' => [
                        'label' => $ventilation->name,
                        'type' => 'select',
                        'options' => static::createOptions($ventilation->values()->orderBy('order')->get(), 'value'),
                    ],
                    'service.' . $ventilation->id . '.extra.demand_driven' => [
                        'label' => __('cooperation/tool/general-data/current-state.index.service.house-ventilation.demand-driven.title'),
                        'type' => 'multiselect',
                        'options' => [
                            true => __('cooperation/tool/general-data/current-state.index.service.house-ventilation.demand-driven.title'),
                        ]
                    ],
                    'service.' . $ventilation->id . '.extra.heat_recovery' => [
                        'label' => __('cooperation/tool/general-data/current-state.index.service.house-ventilation.heat-recovery.title'),
                        'type' => 'multiselect',
                        'options' => [
                            true => __('cooperation/tool/general-data/current-state.index.service.house-ventilation.heat-recovery.title'),
                        ]
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
                    'user_energy_habits.thermostat_high' => [
                        'label' => __('cooperation/tool/general-data/usage.index.heating-habits.thermostat-high.title'),
                        'type' => 'text',
                        'unit' => __('general.unit.degrees.title'),
                    ],

                    'user_energy_habits.thermostat_low' => [
                        'label' => __('cooperation/tool/general-data/usage.index.heating-habits.thermostat-low.title'),
                        'type' => 'text',
                        'unit' => __('general.unit.degrees.title'),
                    ],
                    'user_energy_habits.hours_high' => [
                        'label' => __('cooperation/tool/general-data/usage.index.heating-habits.hours-high.title'),
                        'type' => 'text',
                        'unit' => __('general.unit.hours.title'),
                    ],
                    'user_energy_habits.heating_first_floor' => [
                        'label' => __('cooperation/tool/general-data/usage.index.heating-habits.heating-first-floor.title'),
                        'type' => 'select',
                        'options' => static::createOptions($buildingHeatings),
                    ],

                    'user_energy_habits.heating_second_floor' => [
                        'label' => __('cooperation/tool/general-data/usage.index.heating-habits.heating-first-floor.title'),
                        'type' => 'select',
                        'options' => self::createOptions($buildingHeatings),
                    ],

                    'user_energy_habits.amount_electricity' => [
                        'label' => __('cooperation/tool/general-data/usage.index.energy-usage.amount-electricity.title'),
                        'type' => 'text',
                        'unit' => __('general.unit.cubic-meters.title'),
                    ],
                    'user_energy_habits.amount_gas' => [
                        'label' => __('cooperation/tool/general-data/usage.index.energy-usage.gas-usage.title'),
                        'type' => 'text',
                        'unit' => __('general.unit.cubic-meters.title'),
                    ],
                ],
                // interests come later on
            ],

            'wall-insulation' => [
                '-' => [
                    'user_interests.element.' . $wallInsulation->id => [
                        //'label' => __('general.change-interested.title', ['item' => $livingRoomsWindows->name]),
                        'label' => $wallInsulation->name . ': ' . __('general.interested-in-improvement.title'),
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
                        'savings_gas' => __('wall-insulation.costs.gas.title'),
                        'savings_co2' => __('wall-insulation.costs.co2.title'),
                        'savings_money' => __('general.costs.savings-in-euro.title'),
                        'cost_indication' => __('general.costs.indicative-costs.title'),
                        'interest_comparable' => __('general.costs.comparable-rent.title'),

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
                ]
            ],

            'insulated-glazing' => [
                '-' => [
                    'element.' . $crackSealing->id => [
                        'label' => __('insulated-glazing.moving-parts-quality.title'),
                        'type' => 'select',
                        'options' => static::createOptions($crackSealing->values()->orderBy('order')->get(), 'value'),
                    ],
                    'building_features.window_surface' => [
                        'label' => __('insulated-glazing.windows-surface.title'),
                        'type' => 'text',
                        'unit' => __('general.unit.square-meters.title'),
                    ],
                    'element.' . $frames->id => [
                        'label' => __('insulated-glazing.paint-work.which-frames.title'),
                        'type' => 'select',
                        'options' => static::createOptions($frames->values()->orderBy('order')->get(), 'value'),
                    ],
                    'element.' . $woodElements->id => [
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
                ]
            ],

            'floor-insulation' => [
                '-' => [
                    'user_interests.element.' . $floorInsulation->id => [
                        //'label' => __('general.change-interested.title', ['item' => $livingRoomsWindows->name]),
                        'label' => $floorInsulation->name . ': ' . __('general.interested-in-improvement.title'),
                        'type' => 'select',
                        'options' => $interestOptions,
                    ],
                    'element.' . $crawlspace->id . '.extra.has_crawlspace' => [
                        'label' => __('floor-insulation.has-crawlspace.title'),
                        'type' => 'select',
                        'options' => __('woningdossier.cooperation.option'),
                    ],
                    'element.' . $crawlspace->id . '.extra.access' => [
                        'label' => __('floor-insulation.crawlspace-access.title'),
                        'type' => 'select',
                        'options' => __('woningdossier.cooperation.option'),
                    ],
                    'element.' . $crawlspace->id . '.element_value_id' => [
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
                        'savings_gas' => __('floor-insulation.costs.gas.title'),
                        'savings_co2' => __('floor-insulation.costs.co2.title'),
                        'savings_money' => __('general.costs.savings-in-euro.title'),
                        'cost_indication' => __('general.costs.indicative-costs.title'),
                        'interest_comparable' => __('general.costs.comparable-rent.title'),
                    ],
                ]
            ],

            'roof-insulation' => [
                '-' => [
                    'user_interests.element.' . $roofInsulation->id => [
                        //'label' => __('general.change-interested.title', ['item' => $livingRoomsWindows->name]),
                        'label' => $roofInsulation->name . ': ' . __('general.interested-in-improvement.title'),
                        'type' => 'select',
                        'options' => $interestOptions,
                    ],
                    'building_features.roof_type_id' => [
                        'label' => __('roof-insulation.current-situation.main-roof.title'),
                        'type' => 'select',
                        'options' => static::createOptions($roofTypes),
                        'relationship' => 'roofType',
                    ],
                ]
                // rest will be added later on
            ],

            'high-efficiency-boiler' => [
                '-' => [
                    'user_interests.service.' . $hrBoiler->id => [
                        //'label' => __('general.change-interested.title', ['item' => $livingRoomsWindows->name]),
                        'label' => $hrBoiler->name . ': ' . __('general.interested-in-improvement.title'),
                        'type' => 'select',
                        'options' => $interestOptions,
                    ],
                    'user_energy_habits.resident_count' => [
                        'label' => __('general-data.data-about-usage.total-citizens.title'),
                        'type' => 'text',
                    ],
                    'user_energy_habits.amount_gas' => [
                        'label' => __('general-data.data-about-usage.gas-usage-past-year.title'),
                        'type' => 'text',
                        'unit' => __('general.unit.cubic-meters.title'),
                    ],
                    'service.' . $boiler->id . '.service_value_id' => [
                        'label' => __('high-efficiency-boiler.boiler-type.title'),
                        'type' => 'select',
//                    'options' => $boilerTypes
                        'options' => static::createOptions($boilerTypes, 'value'),
                    ],
                    'service.' . $boiler->id . '.extra.date' => [
                        'label' => __('boiler.boiler-placed-date.title'),
                        'type' => 'text',
                        'unit' => __('general.unit.year.title'),
                    ],
                    'calculations' => [
                        'savings_gas' => __('high-efficiency-boiler.costs.gas.title'),
                        'savings_co2' => __('high-efficiency-boiler.costs.co2.title'),
                        'savings_money' => __('general.costs.savings-in-euro.title'),
                        'cost_indication' => __('general.costs.indicative-costs.title'),
                        'interest_comparable' => __('general.costs.comparable-rent.title'),

                        'replace_year' => __('high-efficiency-boiler.indication-for-costs.indicative-replacement.title'),
                    ],
                ]
            ],

            'solar-panels' => [
                '-' => [
                    'user_interests.service.' . $solarPanels->id => [
                        //'label' => __('general.change-interested.title', ['item' => $livingRoomsWindows->name]),
                        'label' => $solarPanels->name . ': ' . __('general.interested-in-improvement.title'),
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

                        'savings_co2' => __('solar-panels.costs.co2.title'),
                        'savings_money' => __('general.costs.savings-in-euro.title'),
                        'cost_indication' => __('general.costs.indicative-costs.title'),
                        'interest_comparable' => __('general.costs.comparable-rent.title'),
                    ],
                ]
            ],

            'heater' => [
                '-' => [
                    'user_interests.service.' . $heater->id => [
                        //'label' => __('general.change-interested.title', ['item' => $livingRoomsWindows->name]),
                        'label' => $heater->name . ': ' . __('general.interested-in-improvement.title'),
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
                        'production_heat' => __('heater.indication-for-costs.production-heat'),
                        'percentage_consumption' => __('heater.indication-for-costs.percentage-consumption.title'),
                        'savings_gas' => __('heater.costs.gas.title'),
                        'savings_co2' => __('heater.costs.co2.title'),
                        'savings_money' => __('general.costs.savings-in-euro.title'),
                        'cost_indication' => __('general.costs.indicative-costs.title'),
                        'interest_comparable' => __('general.costs.comparable-rent.title'),
                    ],
                ],
            ]
        ];

        $steps = Step::withoutSubSteps()->get();

        $steps = $steps->keyBy('short')->forget('general-data');
        $ventilationInformation = $steps->pull('ventilation-information');
        $steps->push($ventilationInformation);


        foreach ($steps as $step) {
//            <select id="user_interest" class="form-control" name="user_interests[{{$step->id}}][interest_id]">
            $structure['general-data']['interest']['user_interests.' . $step->id . '.interest_id'] = [
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
                0 => __('cooperation/tool/general-data/interest.index.motivation.renovation-plans.options.none')
            ]
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
                $structure['insulated-glazing']['-']['user_interests.' . $measureApplication->id] = [
                    //'label' => 'Interest in '.$measureApplication->measure_name,
                    'label' => __('general.change-interested.title',
                        ['item' => $measureApplication->measure_name]),
                    'type' => 'select',
                    'options' => $interestOptions,
                ];
                $structure['insulated-glazing']['-']['building_insulated_glazings.' . $measureApplication->id . '.insulated_glazing_id'] = [
                    'label' => $measureApplication->measure_name . ': ' . __('insulated-glazing.' . $measureApplication->short . '.current-glass.title'),
                    'type' => 'select',
                    'options' => static::createOptions($insulatedGlazings),
                    'relationship' => 'insulatedGlazing',
                ];
                $structure['insulated-glazing']['-']['building_insulated_glazings.' . $measureApplication->id . '.building_heating_id'] = [
                    'label' => $measureApplication->measure_name . ': ' . __('insulated-glazing.' . $measureApplication->short . '.rooms-heated.title'),
                    'type' => 'select',
                    'options' => static::createOptions($heatings),
                    'relationship' => 'buildingHeating',
                ];
                $structure['insulated-glazing']['-']['building_insulated_glazings.' . $measureApplication->id . '.m2'] = [
                    'label' => $measureApplication->measure_name . ': ' . __('insulated-glazing.' . $measureApplication->short . '.m2.title'),
                    'type' => 'text',
                    'unit' => __('general.unit.square-meters.title'),
                ];
                $structure['insulated-glazing']['-']['building_insulated_glazings.' . $measureApplication->id . '.windows'] = [
                    'label' => $measureApplication->measure_name . ': ' . __('insulated-glazing.' . $measureApplication->short . '.window-replace.title'),
                    'type' => 'text',
                ];
            }
        }
        foreach ($insulatedGlazingStuffSoFar as $igK => $igV) {
            $structure['insulated-glazing']['-'][$igK] = $igV;
        }

        // set the calculations on the end because of the order
        $structure['insulated-glazing']['-']['calculations'] = [
            'savings_gas' => __('insulated-glazing.costs.gas.title'),
            'savings_co2' => __('insulated-glazing.costs.co2.title'),
            'savings_money' => __('general.costs.savings-in-euro.title'),
            'cost_indication' => __('general.costs.indicative-costs.title'),
            'interest_comparable' => __('general.costs.comparable-rent.title'),

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
            $structure['roof-insulation']['-']['building_roof_types.' . $roofType->id . '.element_value_id'] = [
                'label' => __('roof-insulation.current-situation.is-' . $roofType->short . '-roof-insulated.title'),
                'type' => 'select',
                'options' => static::createOptions($roofInsulation->values, 'value'),
                'relationship' => 'elementValue',
            ];
            $structure['roof-insulation']['-']['building_roof_types.' . $roofType->id . '.roof_surface'] = [
                'label' => __('roof-insulation.current-situation.' . $roofType->short . '-roof-surface.title'),
                'type' => 'text',
                'unit' => __('general.unit.square-meters.title'),
            ];
            $structure['roof-insulation']['-']['building_roof_types.' . $roofType->id . '.insulation_roof_surface'] = [
                'label' => __('roof-insulation.current-situation.insulation-' . $roofType->short . '-roof-surface.title'),
                'type' => 'text',
                'unit' => __('general.unit.square-meters.title'),
            ];
            $structure['roof-insulation']['-']['building_roof_types.' . $roofType->id . '.extra.zinc_replaced_date'] = [
                'label' => __('roof-insulation.current-situation.zinc-replaced.title'),
                'type' => 'text',
                'unit' => __('general.unit.year.title'),
            ];
            if ('flat' == $roofType->short) {
                $structure['roof-insulation']['-']['building_roof_types.' . $roofType->id . '.extra.bitumen_replaced_date'] = [
                    'label' => __('roof-insulation.current-situation.bitumen-insulated.title'),
                    'type' => 'text',
                    'unit' => __('general.unit.year.title'),
                ];
            }
            if ('pitched' == $roofType->short) {
                $structure['roof-insulation']['-']['building_roof_types.' . $roofType->id . '.extra.tiles_condition'] = [
                    'label' => __('roof-insulation.current-situation.in-which-condition-tiles.title'),
                    'type' => 'select',
                    'options' => static::createOptions($roofTileStatuses),
                ];
            }
            $structure['roof-insulation']['-']['building_roof_types.' . $roofType->id . '.extra.measure_application_id'] = [
                'label' => __('roof-insulation.' . $roofType->short . '-roof.insulate-roof.title'),
                'type' => 'select',
                'options' => static::createOptions(collect($roofInsulationMeasureApplications[$roofType->short]),
                    'measure_name'),
                'relationship' => 'measureApplication',
            ];
            $structure['roof-insulation']['-']['building_roof_types.' . $roofType->id . '.building_heating_id'] = [
                'label' => __('roof-insulation.' . $roofType->short . '-roof.situation.title'),
                'type' => 'select',
                'options' => static::createOptions($heatings),
                'relationship' => 'heating',
            ];

            if ($roofType->short == $roofTypes1->last()->short) {
                $structure['roof-insulation']['-']['calculations'] = [
                    'flat' => [
                        'savings_gas' => __('roof-insulation.flat.costs.gas.title'),
                        'savings_co2' => __('roof-insulation.flat.costs.co2.title'),
                        'savings_money' => __('general.costs.savings-in-euro.title'),
                        'cost_indication' => __('general.costs.indicative-costs.title'),
                        'interest_comparable' => __('general.costs.comparable-rent.title'),

                        'replace' => [
                            'costs' => __('roof-insulation.flat.indicative-costs-replacement.title'),
                            'year' => __('roof-insulation.flat.indicative-replacement.year.title'),
                        ],
                    ],
                    'pitched' => [
                        'savings_gas' => __('roof-insulation.pitched.costs.gas.title'),
                        'savings_co2' => __('roof-insulation.pitched.costs.co2.title'),
                        'savings_money' => __('general.costs.savings-in-euro.title'),
                        'cost_indication' => __('general.costs.indicative-costs.title'),
                        'interest_comparable' => __('general.costs.comparable-rent.title'),

                        'replace' => [
                            'costs' => __('roof-insulation.pitched.indicative-costs-replacement.title'),
                            'year' => __('roof-insulation.pitched.indicative-replacement.year.title'),
                        ],
                    ],
                ];
            }
        }

        return $structure;
    }

}

