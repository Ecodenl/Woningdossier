<?php

namespace App\Helpers;

use App\Helpers\KeyFigures\Heater\KeyFigures as HeaterKeyFigures;
use App\Helpers\KeyFigures\PvPanels\KeyFigures as SolarPanelsKeyFigures;
use App\Helpers\KeyFigures\RoofInsulation\Temperature;
use App\Models\BuildingHeating;
use App\Models\BuildingPaintworkStatus;
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

    /**
     * Method intended to return the structure of the tool / measure pages.
     *
     * @return array
     */
    public static function getContentStructure()
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

        // Common
        $interests       = Interest::orderBy('order')->get();
        $interestOptions = static::createOptions($interests);

        $structure = [
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
                    'options' => static::createOptions($roofTypes),
                ],
                'building_features.energy_label_id'             => [
                    'label'   => Translation::translate('general-data.building-type.current-energy-label.title'),
                    'type'    => 'select',
                    'options' => static::createOptions($energyLabels),
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
                    'options' => self::createOptions($livingRoomsWindows->values()->orderBy('order')->get(), 'value'),
                ],
                'element.'.$sleepingRoomsWindows->id            => [
                    //'label' => Translation::translate('general.change-interested.title', ['item' => $livingRoomsWindows->name]),
                    'label'   => $sleepingRoomsWindows->name,
                    'type'    => 'select',
                    'options' => self::createOptions($sleepingRoomsWindows->values()->orderBy('order')->get(), 'value'),
                ],
                'user_interest.element.'.$wallInsulation->id    => [
                    //'label' => Translation::translate('general.change-interested.title', ['item' => $livingRoomsWindows->name]),
                    'label'   => $wallInsulation->name.': '.Translation::translate('general.interested-in-improvement.title'),
                    'type'    => 'select',
                    'options' => $interestOptions,
                ],
                'user_interest.element.'.$floorInsulation->id   => [
                    //'label' => Translation::translate('general.change-interested.title', ['item' => $livingRoomsWindows->name]),
                    'label'   => $floorInsulation->name.': '.Translation::translate('general.interested-in-improvement.title'),
                    'type'    => 'select',
                    'options' => $interestOptions,
                ],
                'user_interest.element.'.$roofInsulation->id    => [
                    //'label' => Translation::translate('general.change-interested.title', ['item' => $livingRoomsWindows->name]),
                    'label'   => $roofInsulation->name.': '.Translation::translate('general.interested-in-improvement.title'),
                    'type'    => 'select',
                    'options' => $interestOptions,
                ],

                // services
                'service.'.$heatpumpHybrid->id                  => [
                    'label'   => $heatpumpHybrid->name,
                    'type'    => 'select',
                    'options' => static::createOptions($heatpumpHybrid->values()->orderBy('order')->get(), 'value'),
                ],
                'user_interest.service.'.$heatpumpHybrid->id    => [
                    //'label' => Translation::translate('general.change-interested.title', ['item' => $livingRoomsWindows->name]),
                    'label'   => $heatpumpHybrid->name.': '.Translation::translate('general.interested-in-improvement.title'),
                    'type'    => 'select',
                    'options' => $interestOptions,
                ],
                'service.'.$heatpumpFull->id                    => [
                    'label'   => $heatpumpFull->name,
                    'type'    => 'select',
                    'options' => static::createOptions($heatpumpFull->values()->orderBy('order')->get(), 'value'),
                ],
                'user_interest.service.'.$heatpumpFull->id      => [
                    //'label' => Translation::translate('general.change-interested.title', ['item' => $livingRoomsWindows->name]),
                    'label'   => $heatpumpFull->name.': '.Translation::translate('general.interested-in-improvement.title'),
                    'type'    => 'select',
                    'options' => $interestOptions,
                ],
                'user_interest.service.'.$heater->id            => [
                    //'label' => Translation::translate('general.change-interested.title', ['item' => $livingRoomsWindows->name]),
                    'label'   => $heater->name.': '.Translation::translate('general.interested-in-improvement.title'),
                    'type'    => 'select',
                    'options' => $interestOptions,
                ],
                'service.'.$hrBoiler->id                        => [
                    'label'   => $hrBoiler->name,
                    'type'    => 'select',
                    'options' => static::createOptions($hrBoiler->values()->orderBy('order')->get(), 'value'),
                ],
                'user_interest.service.'.$hrBoiler->id          => [
                    //'label' => Translation::translate('general.change-interested.title', ['item' => $livingRoomsWindows->name]),
                    'label'   => $hrBoiler->name.': '.Translation::translate('general.interested-in-improvement.title'),
                    'type'    => 'select',
                    'options' => $interestOptions,
                ],
                'service.'.$boiler->id.'.service_value_id'      => [
                    'label'   => Translation::translate('boiler.boiler-type.title'),
                    'type'    => 'select',
                    'options' => static::createOptions($boiler->values()->orderBy('order')->get(), 'value'),
                ],
                'service.'.$solarPanels->id.'.extra.value'      => [
                    'label' => $solarPanels->name,
                    'type'  => 'text',
                    'unit'  => Translation::translate('general.unit.pieces.title'),
                ],
                'user_interest.service.'.$solarPanels->id       => [
                    //'label' => Translation::translate('general.change-interested.title', ['item' => $livingRoomsWindows->name]),
                    'label'   => $solarPanels->name.': '.Translation::translate('general.interested-in-improvement.title'),
                    'type'    => 'select',
                    'options' => $interestOptions,
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
                    'options' => static::createOptions($ventilation->values()->orderBy('order')->get(), 'value'),
                ],
                'user_interest.service.'.$ventilation->id       => [
                    //'label' => Translation::translate('general.change-interested.title', ['item' => $livingRoomsWindows->name]),
                    'label'   => $ventilation->name.': '.Translation::translate('general.interested-in-improvement.title'),
                    'type'    => 'select',
                    'options' => $interestOptions,
                ],
                'service.'.$ventilation->id.'.extra.year'       => [
                    'label' => Translation::translate('general-data.energy-saving-measures.house-ventilation.if-mechanic.title'),
                    'type'  => 'text',
                    'unit'  => Translation::translate('general.unit.year.title'),
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
                'element.'.$wallInsulation->id                  => [
                    'label'   => Translation::translate('wall-insulation.intro.filled-insulation.title'),
                    'type'    => 'select',
                    'options' => static::createOptions($wallInsulation->values()->orderBy('order')->get(), 'value'),
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
                    'options' => static::createOptions($facadePlasteredSurfaces),
                ],
                'building_features.facade_damaged_paintwork_id' => [
                    'label'        => Translation::translate('wall-insulation.intro.damage-paintwork.title'),
                    'type'         => 'select',
                    'options'      => static::createOptions($facadeDamages),
                    'relationship' => 'damagedPaintwork'
                ],
                'building_features.wall_joints'                 => [
                    'label'   => Translation::translate('wall-insulation.optional.flushing.title'),
                    'type'    => 'select',
                    'options' => static::createOptions($surfaces),
                ],
                'building_features.contaminated_wall_joints'    => [
                    'label'   => Translation::translate('wall-insulation.optional.is-facade-dirty.title'),
                    'type'    => 'select',
                    'options' => static::createOptions($surfaces),
                ],

                'calculations' => [
                    'savings_gas'         => Translation::translate('wall-insulation.costs.gas.title'),
                    'savings_co2'         => Translation::translate('wall-insulation.costs.co2.title'),
                    'savings_money'       => Translation::translate('general.costs.savings-in-euro.title'),
                    'cost_indication'     => Translation::translate('general.costs.indicative-costs.title'),
                    'interest_comparable' => Translation::translate('general.costs.comparable-rent.title'),

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

                ]
            ],

            'insulated-glazing' => [
                'element.'.$crackSealing->id                      => [
                    'label'   => Translation::translate('insulated-glazing.moving-parts-quality.title'),
                    'type'    => 'select',
                    'options' => static::createOptions($crackSealing->values()->orderBy('order')->get(), 'value'),
                ],
                'building_features.window_surface'                => [
                    'label' => Translation::translate('insulated-glazing.windows-surface.title'),
                    'type'  => 'text',
                    'unit'  => Translation::translate('general.unit.square-meters.title'),
                ],
                'element.'.$frames->id                            => [
                    'label'   => Translation::translate('insulated-glazing.paint-work.which-frames.title'),
                    'type'    => 'select',
                    'options' => static::createOptions($frames->values()->orderBy('order')->get(), 'value'),
                ],
                'element.'.$woodElements->id                      => [
                    'label'   => Translation::translate('insulated-glazing.paint-work.other-wood-elements.title'),
                    'type'    => 'multiselect',
                    'options' => static::createOptions($woodElements->values()->orderBy('order')->get(), 'value'),
                ],
                'building_paintwork_statuses.last_painted_year'   => [
                    'label' => Translation::translate('insulated-glazing.paint-work.last-paintjob.title'),
                    'type'  => 'text',
                    'unit'  => Translation::translate('general.unit.year.title'),
                ],
                'building_paintwork_statuses.paintwork_status_id' => [
                    'label'        => Translation::translate('insulated-glazing.paint-work.paint-damage-visible.title'),
                    'type'         => 'select',
                    'options'      => static::createOptions($paintworkStatuses),
                    'relationship' => 'paintworkStatus'
                ],
                'building_paintwork_statuses.wood_rot_status_id'  => [
                    'label'   => Translation::translate('insulated-glazing.paint-work.wood-rot-visible.title'),
                    'type'    => 'select',
                    'options' => static::createOptions($woodRotStatuses),
                ],
            ],

            'floor-insulation'       => [
                'element.'.$floorInsulation->id                    => [
                    'label'   => Translation::translate('floor-insulation.floor-insulation.title'),
                    'type'    => 'select',
                    'options' => static::createOptions($floorInsulation->values()->orderBy('order')->get(), 'value'),
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
                    'options' => static::createOptions($crawlspace->values()->orderBy('order')->get(), 'value'),
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
                'element.'.$roofInsulation->id   => [
                    'label'   => $roofInsulation->name,
                    'type'    => 'select',
                    'options' => static::createOptions($roofInsulation->values()->orderBy('order')->get(), 'value'),
                ],
                'building_features.roof_type_id' => [
                    'label'        => Translation::translate('roof-insulation.current-situation.main-roof.title'),
                    'type'         => 'select',
                    'options'      => static::createOptions($roofTypes),
                    'relationship' => 'roofType'
                ],
                // rest will be added later on
            ],
            'high-efficiency-boiler' => [
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
                    'options'      => static::createOptions(PvPanelOrientation::orderBy('order')->get()),
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
                'service.'.$heater->id                     => [
                    'label'   => $heater->name,
                    'type'    => 'select',
                    'options' => static::createOptions($heater->values()->orderBy('order')->get(), 'value'),
                ],
                'building_heaters.pv_panel_orientation_id' => [
                    'label'        => Translation::translate('heater.pv-panel-orientation-id.title'),
                    'type'         => 'select',
                    'options'      => static::createOptions(PvPanelOrientation::orderBy('order')->get()),
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
        $insulatedGlazingStuffSoFar = $structure['insulated-glazing'];
        $structure['insulated-glazing'] = [];

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
                    'options'      => static::createOptions($insulatedGlazings),
                    'relationship' => 'insulatedGlazing'
                ];
                $structure['insulated-glazing']['building_insulated_glazings.'.$measureApplication->id.'.building_heating_id']  = [
                    'label'        => $measureApplication->measure_name.': '.Translation::translate('insulated-glazing.'.$measureApplication->short.'.rooms-heated.title'),
                    'type'         => 'select',
                    'options'      => static::createOptions($heatings),
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
        }
        foreach($insulatedGlazingStuffSoFar as $igK => $igV){
            $structure['insulated-glazing'][$igK] = $igV;
        }

        // set the calculations on the end because of the order
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
                'options'      => static::createOptions($roofInsulation->values, 'value'),
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
            $structure['roof-insulation']['building_roof_types.'.$roofType->id.'.zinc_surface']  = [
                'label' => Translation::translate('roof-insulation.current-situation.insulation-'.$roofType->short.'-zinc-surface.title'),
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
                    'options' => static::createOptions($roofTileStatuses),
                ];
            }
            $structure['roof-insulation']['building_roof_types.'.$roofType->id.'.extra.measure_application_id'] = [
                'label'        => Translation::translate('roof-insulation.'.$roofType->short.'-roof.insulate-roof.title'),
                'type'         => 'select',
                'options'      => static::createOptions(collect($roofInsulationMeasureApplications[$roofType->short]),
                    'measure_name'),
                'relationship' => 'measureApplication'
            ];
            $structure['roof-insulation']['building_roof_types.'.$roofType->id.'.building_heating_id']          = [
                'label'        => Translation::translate('roof-insulation.'.$roofType->short.'-roof.situation.title'),
                'type'         => 'select',
                'options'      => static::createOptions($heatings),
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



    /**
     * Called the tool structure, which it is, but like the excel hoom logic
     *
     * @return array
     */
    public static function getToolStructure()
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
        $interestOptions = static::createOptions($interests);

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
                    'options' => static::createOptions($roofTypes),
                ],
                'building_features.energy_label_id'             => [
                    'label'   => Translation::translate('general-data.building-type.current-energy-label.title'),
                    'type'    => 'select',
                    'options' => static::createOptions($energyLabels),
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
                    'options' => self::createOptions($livingRoomsWindows->values()->orderBy('order')->get(), 'value'),
                ],
                'element.'.$sleepingRoomsWindows->id            => [
                    //'label' => Translation::translate('general.change-interested.title', ['item' => $livingRoomsWindows->name]),
                    'label'   => $sleepingRoomsWindows->name,
                    'type'    => 'select',
                    'options' => self::createOptions($sleepingRoomsWindows->values()->orderBy('order')->get(), 'value'),
                ],



                'element.'.$wallInsulation->id                  => [
//                    'label'   => Translation::translate('wall-insulation.intro.filled-insulation.title'),
                    'label' => $wallInsulation->name,
                    'type'    => 'select',
                    'options' => static::createOptions($wallInsulation->values()->orderBy('order')->get(), 'value'),
                ],

                'element.'.$floorInsulation->id                    => [
//                    'label'   => Translation::translate('floor-insulation.floor-insulation.title'),
                    'label'   => $floorInsulation->name,
                    'type'    => 'select',
                    'options' => static::createOptions($floorInsulation->values()->orderBy('order')->get(), 'value'),
                ],
                'element.'.$roofInsulation->id   => [
                    'label'   => $roofInsulation->name,
                    'type'    => 'select',
                    'options' => static::createOptions($roofInsulation->values()->orderBy('order')->get(), 'value'),
                ],

                // services
                'service.'.$heatpumpHybrid->id                  => [
                    'label'   => $heatpumpHybrid->name,
                    'type'    => 'select',
                    'options' => static::createOptions($heatpumpHybrid->values()->orderBy('order')->get(), 'value'),
                ],
                'service.'.$heatpumpFull->id                    => [
                    'label'   => $heatpumpFull->name,
                    'type'    => 'select',
                    'options' => static::createOptions($heatpumpFull->values()->orderBy('order')->get(), 'value'),
                ],
                'service.'.$heater->id                     => [
                    'label'   => $heater->name,
                    'type'    => 'select',
                    'options' => static::createOptions($heater->values()->orderBy('order')->get(), 'value'),
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
                    'options' => static::createOptions($hrBoiler->values()->orderBy('order')->get(), 'value'),
                ],

                'service.'.$boiler->id.'.service_value_id'      => [
                    'label'   => Translation::translate('boiler.boiler-type.title'),
                    'type'    => 'select',
                    'options' => static::createOptions($boiler->values()->orderBy('order')->get(), 'value'),
                ],

                // ventilation
                'service.'.$ventilation->id.'.service_value_id' => [
                    'label'   => $ventilation->name,
                    'type'    => 'select',
                    'options' => static::createOptions($ventilation->values()->orderBy('order')->get(), 'value'),
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
                    'options' => static::createOptions($facadePlasteredSurfaces),
                ],
                'building_features.facade_damaged_paintwork_id' => [
                    'label'        => Translation::translate('wall-insulation.intro.damage-paintwork.title'),
                    'type'         => 'select',
                    'options'      => static::createOptions($facadeDamages),
                    'relationship' => 'damagedPaintwork'
                ],
                'building_features.wall_joints'                 => [
                    'label'   => Translation::translate('wall-insulation.optional.flushing.title'),
                    'type'    => 'select',
                    'options' => static::createOptions($surfaces),
                ],
                'building_features.contaminated_wall_joints'    => [
                    'label'   => Translation::translate('wall-insulation.optional.is-facade-dirty.title'),
                    'type'    => 'select',
                    'options' => static::createOptions($surfaces),
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
                    'savings_gas'         => Translation::translate('wall-insulation.costs.gas.title'),
                    'savings_co2'         => Translation::translate('wall-insulation.costs.co2.title'),
                    'savings_money'       => Translation::translate('general.costs.savings-in-euro.title'),
                    'cost_indication'     => Translation::translate('general.costs.indicative-costs.title'),
                    'interest_comparable' => Translation::translate('general.costs.comparable-rent.title'),

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

                ]
            ],

            'insulated-glazing' => [
                'element.'.$crackSealing->id                      => [
                    'label'   => Translation::translate('insulated-glazing.moving-parts-quality.title'),
                    'type'    => 'select',
                    'options' => static::createOptions($crackSealing->values()->orderBy('order')->get(), 'value'),
                ],
                'building_features.window_surface'                => [
                    'label' => Translation::translate('insulated-glazing.windows-surface.title'),
                    'type'  => 'text',
                    'unit'  => Translation::translate('general.unit.square-meters.title'),
                ],
                'element.'.$frames->id                            => [
                    'label'   => Translation::translate('insulated-glazing.paint-work.which-frames.title'),
                    'type'    => 'select',
                    'options' => static::createOptions($frames->values()->orderBy('order')->get(), 'value'),
                ],
                'element.'.$woodElements->id                      => [
                    'label'   => Translation::translate('insulated-glazing.paint-work.other-wood-elements.title'),
                    'type'    => 'multiselect',
                    'options' => static::createOptions($woodElements->values()->orderBy('order')->get(), 'value'),
                ],
                'building_paintwork_statuses.last_painted_year'   => [
                    'label' => Translation::translate('insulated-glazing.paint-work.last-paintjob.title'),
                    'type'  => 'text',
                    'unit'  => Translation::translate('general.unit.year.title'),
                ],
                'building_paintwork_statuses.paintwork_status_id' => [
                    'label'        => Translation::translate('insulated-glazing.paint-work.paint-damage-visible.title'),
                    'type'         => 'select',
                    'options'      => static::createOptions($paintworkStatuses),
                    'relationship' => 'paintworkStatus'
                ],
                'building_paintwork_statuses.wood_rot_status_id'  => [
                    'label'   => Translation::translate('insulated-glazing.paint-work.wood-rot-visible.title'),
                    'type'    => 'select',
                    'options' => static::createOptions($woodRotStatuses),
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
                    'options' => static::createOptions($crawlspace->values()->orderBy('order')->get(), 'value'),
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
                    'options'      => static::createOptions($roofTypes),
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
//                    'options' => $boilerTypes
                    'options' => static::createOptions($boilerTypes, 'value'),
                ],
                'service.'.$boiler->id.'.extra.date' => [
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
                    'options'      => static::createOptions(PvPanelOrientation::orderBy('order')->get()),
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
                    'options'      => static::createOptions(PvPanelOrientation::orderBy('order')->get()),
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
        $insulatedGlazingStuffSoFar = $structure['insulated-glazing'];
        $structure['insulated-glazing'] = [];

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
                    'options'      => static::createOptions($insulatedGlazings),
                    'relationship' => 'insulatedGlazing'
                ];
                $structure['insulated-glazing']['building_insulated_glazings.'.$measureApplication->id.'.building_heating_id']  = [
                    'label'        => $measureApplication->measure_name.': '.Translation::translate('insulated-glazing.'.$measureApplication->short.'.rooms-heated.title'),
                    'type'         => 'select',
                    'options'      => static::createOptions($heatings),
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
        }
        foreach($insulatedGlazingStuffSoFar as $igK => $igV){
            $structure['insulated-glazing'][$igK] = $igV;
        }

        // set the calculations on the end because of the order
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
                'options'      => static::createOptions($roofInsulation->values, 'value'),
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
                    'options' => static::createOptions($roofTileStatuses),
                ];
            }
            $structure['roof-insulation']['building_roof_types.'.$roofType->id.'.extra.measure_application_id'] = [
                'label'        => Translation::translate('roof-insulation.'.$roofType->short.'-roof.insulate-roof.title'),
                'type'         => 'select',
                'options'      => static::createOptions(collect($roofInsulationMeasureApplications[$roofType->short]),
                    'measure_name'),
                'relationship' => 'measureApplication'
            ];
            $structure['roof-insulation']['building_roof_types.'.$roofType->id.'.building_heating_id']          = [
                'label'        => Translation::translate('roof-insulation.'.$roofType->short.'-roof.situation.title'),
                'type'         => 'select',
                'options'      => static::createOptions($heatings),
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

//745
//660