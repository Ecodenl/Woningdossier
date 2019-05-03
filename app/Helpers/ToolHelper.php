<?php

namespace App\Helpers;

use App\Helpers\KeyFigures\Heater\KeyFigures as HeaterKeyFigures;
use App\Helpers\KeyFigures\PvPanels\KeyFigures as SolarPanelsKeyFigures;
use App\Helpers\KeyFigures\RoofInsulation\Temperature;
use App\Models\BuildingHeating;
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
use App\Models\WoodRotStatus;
use Illuminate\Support\Collection;

class ToolHelper {


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

    /**
     * Method intended to return the structure of the tool / measure pages.
     *
     * @return array
     */
    public static function getContentStructure()
    {
        // General data

        // General data - Elements (that are not queried later on step basis)
        $livingRoomsWindows = Element::where('short', 'living-rooms-windows')->first();
        $sleepingRoomsWindows = Element::where('short', 'sleeping-rooms-windows')->first();
        // General data - Services (that are not queried later on step basis)
        $heatpumpHybrid = Service::where('short', 'hybrid-heat-pump')->first();
        $heatpumpFull = Service::where('short', 'full-heat-pump')->first();
        $ventilation = Service::where('short', 'house-ventilation')->first();

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
                Temperature::ROOF_INSULATION_FLAT_ON_CURRENT => MeasureApplication::where('short', 'roof-insulation-flat-current')->first(),
                Temperature::ROOF_INSULATION_FLAT_REPLACE => MeasureApplication::where('short', 'roof-insulation-flat-replace-current')->first(),
            ],
            'pitched' => [
                Temperature::ROOF_INSULATION_PITCHED_INSIDE => MeasureApplication::where('short', 'roof-insulation-pitched-inside')->first(),
                Temperature::ROOF_INSULATION_PITCHED_REPLACE_TILES => MeasureApplication::where('short', 'roof-insulation-pitched-replace-tiles')->first(),
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

        // Common
        $interests = Interest::orderBy('order')->get();
        $interestOptions = static::createOptions($interests);

        $structure = [
            'general-data' => [
                'building_features.surface' => [
                    'label' => Translation::translate('general-data.building-type.what-user-surface.title'),
                    'type' => 'text',
                    'unit' => Translation::translate('general.unit.square-meters.title'),
                ],
                'building_features.building_layers' => [
                    'label' => Translation::translate('general-data.building-type.how-much-building-layers.title'),
                    'type' => 'text',
                ],
                'building_features.roof_type_id' => [
                    'label' => Translation::translate('general-data.building-type.type-roof.title'),
                    'type' => 'select',
                    'options' => static::createOptions($roofTypes),
                ],
                'building_features.energy_label_id' => [
                    'label' => Translation::translate('general-data.building-type.current-energy-label.title'),
                    'type' => 'select',
                    'options' => static::createOptions($energyLabels),
                ],
                'building_features.monument' => [
                    'label' => Translation::translate('general-data.building-type.is-monument.title'),
                    'type' => 'select',
                    'options' => [
                        1 => __('woningdossier.cooperation.radiobutton.yes'),
                        2 => __('woningdossier.cooperation.radiobutton.no'),
                        0 => __('woningdossier.cooperation.radiobutton.unknown'),
                    ],
                ],
                // elements and services
                'element.'.$livingRoomsWindows->id => [
                    //'label' => Translation::translate('general.change-interested.title', ['item' => $livingRoomsWindows->name]),
                    'label' => $livingRoomsWindows->name,
                    'type' => 'select',
                    'options' => self::createOptions($livingRoomsWindows->values()->orderBy('order')->get(), 'value'),
                ],
                'element.'.$sleepingRoomsWindows->id => [
                    //'label' => Translation::translate('general.change-interested.title', ['item' => $livingRoomsWindows->name]),
                    'label' => $sleepingRoomsWindows->name,
                    'type' => 'select',
                    'options' => self::createOptions($sleepingRoomsWindows->values()->orderBy('order')->get(), 'value'),
                ],
                'user_interest.element.'.$wallInsulation->id => [
                    //'label' => Translation::translate('general.change-interested.title', ['item' => $livingRoomsWindows->name]),
                    'label' => $wallInsulation->name . ': ' . Translation::translate('general.interested-in-improvement.title'),
                    'type' => 'select',
                    'options' => $interestOptions,
                ],
                'user_interest.element.'.$floorInsulation->id => [
                    //'label' => Translation::translate('general.change-interested.title', ['item' => $livingRoomsWindows->name]),
                    'label' => $floorInsulation->name . ': ' . Translation::translate('general.interested-in-improvement.title'),
                    'type' => 'select',
                    'options' => $interestOptions,
                ],
                'user_interest.element.'.$roofInsulation->id => [
                    //'label' => Translation::translate('general.change-interested.title', ['item' => $livingRoomsWindows->name]),
                    'label' => $roofInsulation->name . ': ' . Translation::translate('general.interested-in-improvement.title'),
                    'type' => 'select',
                    'options' => $interestOptions,
                ],

                // services
                'service.'.$heatpumpHybrid->id => [
                    'label' => $heatpumpHybrid->name,
                    'type' => 'select',
                    'options' => static::createOptions($heatpumpHybrid->values()->orderBy('order')->get(), 'value'),
                ],
                'user_interest.service.'.$heatpumpHybrid->id => [
                    //'label' => Translation::translate('general.change-interested.title', ['item' => $livingRoomsWindows->name]),
                    'label' => $heatpumpHybrid->name . ': ' . Translation::translate('general.interested-in-improvement.title'),
                    'type' => 'select',
                    'options' => $interestOptions,
                ],
                'service.'.$heatpumpFull->id => [
                    'label' => $heatpumpFull->name,
                    'type' => 'select',
                    'options' => static::createOptions($heatpumpFull->values()->orderBy('order')->get(), 'value'),
                ],
                'user_interest.service.'.$heatpumpFull->id => [
                    //'label' => Translation::translate('general.change-interested.title', ['item' => $livingRoomsWindows->name]),
                    'label' => $heatpumpFull->name . ': ' . Translation::translate('general.interested-in-improvement.title'),
                    'type' => 'select',
                    'options' => $interestOptions,
                ],
                'user_interest.service.'.$heater->id => [
                    //'label' => Translation::translate('general.change-interested.title', ['item' => $livingRoomsWindows->name]),
                    'label' => $heater->name . ': ' . Translation::translate('general.interested-in-improvement.title'),
                    'type' => 'select',
                    'options' => $interestOptions,
                ],
                'service.'.$hrBoiler->id => [
                    'label' => $hrBoiler->name,
                    'type' => 'select',
                    'options' => static::createOptions($hrBoiler->values()->orderBy('order')->get(), 'value'),
                ],
                'user_interest.service.'.$hrBoiler->id => [
                    //'label' => Translation::translate('general.change-interested.title', ['item' => $livingRoomsWindows->name]),
                    'label' => $hrBoiler->name . ': ' . Translation::translate('general.interested-in-improvement.title'),
                    'type' => 'select',
                    'options' => $interestOptions,
                ],
                'service.'.$boiler->id.'.service_value_id' => [
                    'label' => Translation::translate('boiler.boiler-type.title'),
                    'type' => 'select',
                    'options' => static::createOptions($boiler->values()->orderBy('order')->get(), 'value'),
                ],
                'service.'.$solarPanels->id . '.extra.value' => [
                    'label' => $solarPanels->name,
                    'type' => 'text',
                    'unit' => Translation::translate('general.unit.pieces.title'),
                ],
                'user_interest.service.'.$solarPanels->id => [
                    //'label' => Translation::translate('general.change-interested.title', ['item' => $livingRoomsWindows->name]),
                    'label' => $solarPanels->name . ': ' . Translation::translate('general.interested-in-improvement.title'),
                    'type' => 'select',
                    'options' => $interestOptions,
                ],
                'service.'.$solarPanels->id.'.extra.year' => [
                    'label' => Translation::translate('general-data.energy-saving-measures.solar-panels.if-yes.title'),
                    'type' => 'text',
                    'unit' => Translation::translate('general.unit.year.title'),
                ],

                // ventilation
                'service.'.$ventilation->id.'.service_value_id' => [
                    'label' => $ventilation->name,
                    'type' => 'select',
                    'options' => static::createOptions($ventilation->values()->orderBy('order')->get(), 'value'),
                ],
                'user_interest.service.'.$ventilation->id => [
                    //'label' => Translation::translate('general.change-interested.title', ['item' => $livingRoomsWindows->name]),
                    'label' => $ventilation->name . ': ' . Translation::translate('general.interested-in-improvement.title'),
                    'type' => 'select',
                    'options' => $interestOptions,
                ],
                'service.'.$ventilation->id.'.extra.year' => [
                    'label' => Translation::translate('general-data.energy-saving-measures.house-ventilation.if-mechanic.title'),
                    'type' => 'text',
                    'unit' => Translation::translate('general.unit.year.title'),
                ],


                // habits
                'user_energy_habits.cook_gas' => [
                    'label' => Translation::translate('general-data.data-about-usage.cooked-on-gas.title'),
                    'type' => 'select',
                    'options' => [
                        1 => __('woningdossier.cooperation.radiobutton.yes'),
                        2 => __('woningdossier.cooperation.radiobutton.no'),
                    ],
                ],
                'user_energy_habits.amount_electricity' => [
                    'label' => Translation::translate('general-data.data-about-usage.electricity-consumption-past-year.title'),
                    'type' => 'text',
                    'unit' => Translation::translate('general.unit.cubic-meters.title'),
                ],
                'user_energy_habits.amount_gas' => [
                    'label' => Translation::translate('general-data.data-about-usage.gas-usage-past-year.title'),
                    'type' => 'text',
                    'unit' => Translation::translate('general.unit.cubic-meters.title'),
                ],
                // user interests
            ],

            'wall-insulation' => [
                'element.'.$wallInsulation->id => [
                    'label' => Translation::translate('wall-insulation.intro.filled-insulation.title'),
                    'type' => 'select',
                    'options' => static::createOptions($wallInsulation->values()->orderBy('order')->get(), 'value'),
                ],
                'building_features.wall_surface' => [
                    'label' => Translation::translate('wall-insulation.optional.facade-surface.title'),
                    'type' => 'text',
                    'unit' => Translation::translate('general.unit.square-meters.title'),
                ],
                'building_features.insulation_wall_surface' => [
                    'label' => Translation::translate('wall-insulation.optional.insulated-surface.title'),
                    'type' => 'text',
                    'unit' => Translation::translate('general.unit.square-meters.title'),
                ],
                'building_features.cavity_wall' => [
                    'label' => Translation::translate('wall-insulation.intro.has-cavity-wall.title'),
                    'type' => 'select',
                    'options' => [
                        0 => __('woningdossier.cooperation.radiobutton.unknown'),
                        1 => __('woningdossier.cooperation.radiobutton.yes'),
                        2 => __('woningdossier.cooperation.radiobutton.no'),
                    ],
                ],
                'building_features.facade_plastered_painted' => [
                    'label' => Translation::translate('wall-insulation.intro.is-facade-plastered-painted.title'),
                    'type' => 'select',
                    'options' => [
                        1 => __('woningdossier.cooperation.radiobutton.yes'),
                        2 => __('woningdossier.cooperation.radiobutton.no'),
                        3 => __('woningdossier.cooperation.radiobutton.mostly'),
                    ],
                ],
                'building_features.facade_plastered_surface_id' => [
                    'label' => Translation::translate('wall-insulation.intro.surface-paintwork.title'),
                    'type' => 'select',
                    'options' => static::createOptions($facadePlasteredSurfaces),
                ],
                'building_features.facade_damaged_paintwork_id' => [
                    'label' => Translation::translate('wall-insulation.intro.damage-paintwork.title'),
                    'type' => 'select',
                    'options' => static::createOptions($facadeDamages),
                ],
                'building_features.wall_joints' => [
                    'label' => Translation::translate('wall-insulation.optional.flushing.title'),
                    'type' => 'select',
                    'options' => static::createOptions($surfaces),
                ],
                'building_features.contaminated_wall_joints' => [
                    'label' => Translation::translate('wall-insulation.optional.is-facade-dirty.title'),
                    'type' => 'select',
                    'options' => static::createOptions($surfaces),
                ],

                'calculations' => [
                    'gas_savings' => Translation::translate('wall-insulation.costs.gas.title'),
                    'co2_savings' => Translation::translate('wall-insulation.costs.co2.title'),
                    'savings_in_euro' => Translation::translate('general.costs.savings-in-euro.title'),
                    'indicative_costs' => Translation::translate('general.costs.indicative-costs.title'),
                    'comparable_rent' => Translation::translate('general.costs.comparable-rent.title'),

                    'repair_joint' => Translation::translate('wall-insulation.taking-into-account.repair-joint.title'),
                    'clean_brickwork' => Translation::translate('wall-insulation.taking-into-account.clean-brickwork.title'),
                    'impregnate_wall' => Translation::translate('wall-insulation.taking-into-account.impregnate-wall.title'),
                    'paint_wall' => Translation::translate('wall-insulation.taking-into-account.wall-painting.title'),
                ]
            ],

            'insulated-glazing' => [
                'element.'.$crackSealing->id => [
                    'label' => Translation::translate('insulated-glazing.moving-parts-quality.title'),
                    'type' => 'select',
                    'options' => static::createOptions($crackSealing->values()->orderBy('order')->get(), 'value'),
                ],
                'building_features.window_surface' => [
                    'label' => Translation::translate('insulated-glazing.windows-surface.title'),
                    'type' => 'text',
                    'unit' => Translation::translate('general.unit.square-meters.title'),
                ],
                'element.'.$frames->id => [
                    'label' => Translation::translate('insulated-glazing.paint-work.which-frames.title'),
                    'type' => 'select',
                    'options' => static::createOptions($frames->values()->orderBy('order')->get(), 'value'),
                ],
                'building_paintwork_statuses.last_painted_year' => [
                    'label' => Translation::translate('insulated-glazing.paint-work.last-paintjob.title'),
                    'type' => 'text',
                    'unit' => Translation::translate('general.unit.year.title'),
                ],
                'building_paintwork_statuses.paintwork_status_id' => [
                    'label' => Translation::translate('insulated-glazing.paint-work.paint-damage-visible.title'),
                    'type' => 'select',
                    'options' => static::createOptions($paintworkStatuses),
                ],
                'building_paintwork_statuses.wood_rot_status_id' => [
                    'label' => Translation::translate('insulated-glazing.paint-work.wood-rot-visible.title'),
                    'type' => 'select',
                    'options' => static::createOptions($woodRotStatuses),
                ],
                'element.'.$woodElements->id => [
                    'label' => Translation::translate('insulated-glazing.paint-work.other-wood-elements.title'),
                    'type' => 'multiselect',
                    'options' => static::createOptions($woodElements->values()->orderBy('order')->get(), 'value'),
                ],

                'calculations' => [
                    'gas_savings' => Translation::translate('insulated-glazing.costs.gas.title'),
                    'co2_savings' => Translation::translate('insulated-glazing.costs.co2.title'),
                    'savings_in_euro' => Translation::translate('general.costs.savings-in-euro.title'),
                    'indicative_costs' => Translation::translate('general.costs.indicative-costs.title'),
                    'comparable_rent' => Translation::translate('general.costs.comparable-rent.title'),

                    'paintwork' => Translation::translate('insulated-glazing.taking-into-account.paintwork.title'),
                    'paint_work' => Translation::translate('insulated-glazing.taking-into-account.paintwork_year.title')
                ],
            ],
            'floor-insulation' => [
                'element.'.$floorInsulation->id => [
                    'label' => Translation::translate('floor-insulation.floor-insulation.title'),
                    'type' => 'select',
                    'options' => static::createOptions($floorInsulation->values()->orderBy('order')->get(), 'value'),
                ],
                'building_features.floor_surface' => [
                    'label' => Translation::translate('floor-insulation.surface.title'),
                    'type' => 'text',
                    'unit' => Translation::translate('general.unit.square-meters.title'),
                ],
                'building_features.insulation_surface' => [
                    'label' => Translation::translate('floor-insulation.insulation-surface.title'),
                    'type' => 'text',
                    'unit' => Translation::translate('general.unit.square-meters.title'),
                ],
                'element.'.$crawlspace->id.'.extra.has_crawlspace' => [
                    'label' => Translation::translate('floor-insulation.has-crawlspace.title'),
                    'type' => 'select',
                    'options' => __('woningdossier.cooperation.option'),
                ],
                'element.'.$crawlspace->id.'.extra.access' => [
                    'label' => Translation::translate('floor-insulation.crawlspace-access.title'),
                    'type' => 'select',
                    'options' => __('woningdossier.cooperation.option'),
                ],
                'element.'.$crawlspace->id.'.element_value_id' => [
                    'label' => Translation::translate('floor-insulation.crawlspace-height.title'),
                    'type' => 'select',
                    'options' => static::createOptions($crawlspace->values()->orderBy('order')->get(), 'value'),
                ],

                'calculations' => [
                    'gas_savings' => Translation::translate('floor-insulation.costs.gas.title'),
                    'co2_savings' => Translation::translate('floor-insulation.costs.co2.title'),
                    'savings_in_euro' => Translation::translate('general.costs.savings-in-euro.title'),
                    'indicative_costs' => Translation::translate('general.costs.indicative-costs.title'),
                    'comparable_rent' => Translation::translate('general.costs.comparable-rent.title'),
                ]
            ],
            'roof-insulation' => [
                'element.'.$roofInsulation->id => [
                    'label' => $roofInsulation->name,
                    'type' => 'select',
                    'options' => static::createOptions($roofInsulation->values()->orderBy('order')->get(), 'value'),
                ],
                'building_features.roof_type_id' => [
                    'label' => Translation::translate('roof-insulation.current-situation.main-roof.title'),
                    'type' => 'select',
                    'options' => static::createOptions($roofTypes),
                ],
                // rest will be added later on

                'calculations' => [
                    'flat' => [
                        'gas_savings' => Translation::translate('roof-insulation.flat.costs.gas.title'),
                        'co2_savings' => Translation::translate('roof-insulation.flat.costs.co2.title'),
                        'savings_in_euro' => Translation::translate('general.costs.savings-in-euro.title'),
                        'indicative_costs' => Translation::translate('general.costs.indicative-costs.title'),
                        'comparable_rent' => Translation::translate('general.costs.comparable-rent.title'),

                        'indicative_costs_replacement' => Translation::translate('roof-insulation.flat.indicative-costs-replacement.title'),
                        'indicative_replacement_year' => Translation::translate('roof-insulation.flat.indicative-replacement.year.title')
                    ],
                    'pitched' => [
                        'gas_savings' => Translation::translate('roof-insulation.pitched.costs.gas.title'),
                        'co2_savings' => Translation::translate('roof-insulation.pitched.costs.co2.title'),
                        'savings_in_euro' => Translation::translate('general.costs.savings-in-euro.title'),
                        'indicative_costs' => Translation::translate('general.costs.indicative-costs.title'),
                        'comparable_rent' => Translation::translate('general.costs.comparable-rent.title'),

                        'indicative_costs_replacement' => Translation::translate('roof-insulation.pitched.indicative-costs-replacement.title'),
                        'indicative_replacement_year' => Translation::translate('roof-insulation.pitched.indicative-replacement.year.title')
                    ]
                ]
            ],
            'high-efficiency-boiler' => [
                'service.'.$boiler->id.'.extra.year' => [
                    'label' => Translation::translate('boiler.boiler-placed-date.title'),
                    'type' => 'text',
                    'unit' => Translation::translate('general.unit.year.title'),
                ],
                'calculations' => [
                    'gas_savings' => Translation::translate('high-efficiency-boiler.costs.gas.title'),
                    'co2_savings' => Translation::translate('high-efficiency-boiler.costs.co2.title'),
                    'savings_in_euro' => Translation::translate('general.costs.savings-in-euro.title'),
                    'indicative_costs' => Translation::translate('general.costs.indicative-costs.title'),
                    'comparable_rent' => Translation::translate('general.costs.comparable-rent.title'),

                    'indicative_replacement_year' => Translation::translate('high-efficiency-boiler.indication-for-costs.indicative-replacement.title')
                ]
            ],

            'solar-panels' => [
                'building_pv_panels.peak_power' => [
                    'label' => Translation::translate('solar-panels.peak-power.title'),
                    'type' => 'select',
                    'options' => $solarPanelsOptionsPeakPower,
                ],
                'building_pv_panels.number' => [
                    'label' => Translation::translate('solar-panels.number.title'),
                    'type' => 'text',
                    'unit' => Translation::translate('general.unit.pieces.title'),
                ],
                'building_pv_panels.pv_panel_orientation_id' => [
                    'label' => Translation::translate('solar-panels.pv-panel-orientation-id.title'),
                    'type' => 'select',
                    'options' => static::createOptions(PvPanelOrientation::orderBy('order')->get()),
                ],
                'building_pv_panels.angle' => [
                    'label' => Translation::translate('solar-panels.angle.title'),
                    'type' => 'select',
                    'options' => $solarPanelsOptionsAngle,
                ],

                'calculations' => [
                    'yield_electricity' => Translation::translate('solar-panels.indication-for-costs.yield-electricity.title'),
                    'raise_own_consumption' => Translation::translate('solar-panels.indication-for-costs.raise-own-consumption.title'),

                    'gas_savings' => Translation::translate('solar-panels.costs.gas.title'),
                    'co2_savings' => Translation::translate('solar-panels.costs.co2.title'),
                    'savings_in_euro' => Translation::translate('general.costs.savings-in-euro.title'),
                    'indicative_costs' => Translation::translate('general.costs.indicative-costs.title'),
                    'comparable_rent' => Translation::translate('general.costs.comparable-rent.title'),
                ],
            ],
            'heater' => [
                'service.'.$heater->id => [
                    'label' => $heater->name,
                    'type' => 'select',
                    'options' => static::createOptions($heater->values()->orderBy('order')->get(), 'value'),
                ],
                'building_heaters.pv_panel_orientation_id' => [
                    'label' => Translation::translate('heater.pv-panel-orientation-id.title'),
                    'type' => 'select',
                    'options' => static::createOptions(PvPanelOrientation::orderBy('order')->get()),
                ],
                'building_heaters.angle' => [
                    'label' => Translation::translate('heater.angle.title'),
                    'type' => 'select',
                    'options' => $heaterOptionsAngle,
                ],

                'calculations' => [
                    'consumption_water' => Translation::translate('heater.consumption-water.title'),
                    'consumption_gas' => Translation::translate('heater.consumption-gas.title'),

                    'size_boiler' => Translation::translate('heater.size-boiler.title'),
                    'size_collector' => Translation::translate('heater.size-collector.title'),

                    'production_heat' => Translation::translate('heater.indication-for-costs.production-heat'),
                    'percentage_consumption' => Translation::translate('heater.indication-for-costs.percentage-consumption.title'),

                    'gas_savings' => Translation::translate('heater.costs.gas.title'),
                    'co2_savings' => Translation::translate('heater.costs.co2.title'),
                    'savings_in_euro' => Translation::translate('general.costs.savings-in-euro.title'),
                    'indicative_costs' => Translation::translate('general.costs.indicative-costs.title'),
                    'comparable_rent' => Translation::translate('general.costs.comparable-rent.title'),
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
                $structure['insulated-glazing']['user_interests.'.$measureApplication->id] = [
                    //'label' => 'Interest in '.$measureApplication->measure_name,
                    'label' => Translation::translate('general.change-interested.title', ['item' => $measureApplication->measure_name]),
                    'type' => 'select',
                    'options' => $interestOptions,
                ];
                $structure['insulated-glazing']['building_insulated_glazings.'.$measureApplication->id.'.insulated_glazing_id'] = [
                    'label' => $measureApplication->measure_name.': '.Translation::translate('insulated-glazing.'.$measureApplication->short.'.current-glass.title'),
                    'type' => 'select',
                    'options' => static::createOptions($insulatedGlazings),
                ];
                $structure['insulated-glazing']['building_insulated_glazings.'.$measureApplication->id.'.building_heating_id'] = [
                    'label' => $measureApplication->measure_name.': '.Translation::translate('insulated-glazing.'.$measureApplication->short.'.rooms-heated.title'),
                    'type' => 'select',
                    'options' => static::createOptions($heatings),
                ];
                $structure['insulated-glazing']['building_insulated_glazings.'.$measureApplication->id.'.m2'] = [
                    'label' => $measureApplication->measure_name.': '.Translation::translate('insulated-glazing.'.$measureApplication->short.'.m2.title'),
                    'type' => 'text',
                    'unit' => Translation::translate('general.unit.square-meters.title'),
                ];
                $structure['insulated-glazing']['building_insulated_glazings.'.$measureApplication->id.'.windows'] = [
                    'label' => $measureApplication->measure_name.': '.Translation::translate('insulated-glazing.'.$measureApplication->short.'.window-replace.title'),
                    'type' => 'text',
                ];
            }
        }

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
            $structure['roof-insulation']['building_roof_types.'.$roofType->id.'.element_value_id'] = [
                'label' => Translation::translate('roof-insulation.current-situation.is-'.$roofType->short.'-roof-insulated.title'),
                'type' => 'select',
                'options' => static::createOptions($roofInsulation->values, 'value'),
            ];
            $structure['roof-insulation']['building_roof_types.'.$roofType->id.'.roof_surface'] = [
                'label' => Translation::translate('roof-insulation.current-situation.'.$roofType->short.'-roof-surface.title'),
                'type' => 'text',
                'unit' => Translation::translate('general.unit.square-meters.title'),
            ];
            $structure['roof-insulation']['building_roof_types.'.$roofType->id.'.insulation_roof_surface'] = [
                'label' => Translation::translate('roof-insulation.current-situation.insulation-'.$roofType->short.'-roof-surface.title'),
                'type' => 'text',
                'unit' => Translation::translate('general.unit.square-meters.title'),
            ];
            $structure['roof-insulation']['building_roof_types.'.$roofType->id.'.extra.zinc_replaced_date'] = [
                'label' => Translation::translate('roof-insulation.current-situation.zinc-replaced.title'),
                'type' => 'text',
                'unit' => Translation::translate('general.unit.year.title'),
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
                    'label' => Translation::translate('roof-insulation.current-situation.in-which-condition-tiles.title'),
                    'type' => 'select',
                    'options' => static::createOptions($roofTileStatuses),
                ];
            }
            $structure['roof-insulation']['building_roof_types.'.$roofType->id.'.extra.measure_application_id'] = [
                'label' => Translation::translate('roof-insulation.'.$roofType->short.'-roof.insulate-roof.title'),
                'type' => 'select',
                'options' => static::createOptions(collect($roofInsulationMeasureApplications[$roofType->short]), 'measure_name'),
            ];
            $structure['roof-insulation']['building_roof_types.'.$roofType->id.'.building_heating_id'] = [
                'label' => Translation::translate('roof-insulation.'.$roofType->short.'-roof.situation.title'),
                'type' => 'select',
                'options' => static::createOptions($heatings),
            ];
        }

        return $structure;
    }
}