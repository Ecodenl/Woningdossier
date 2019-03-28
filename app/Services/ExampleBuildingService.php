<?php

namespace App\Services;

use App\Helpers\KeyFigures\Heater\KeyFigures as HeaterKeyFigures;
use App\Helpers\KeyFigures\PvPanels\KeyFigures as SolarPanelsKeyFigures;
use App\Helpers\KeyFigures\RoofInsulation\Temperature;
use App\Helpers\Translation;
use App\Models\Building;
use App\Models\BuildingElement;
use App\Models\BuildingFeature;
use App\Models\BuildingHeater;
use App\Models\BuildingHeating;
use App\Models\BuildingInsulatedGlazing;
use App\Models\BuildingPaintworkStatus;
use App\Models\BuildingPvPanel;
use App\Models\BuildingRoofType;
use App\Models\BuildingService;
use App\Models\Element;
use App\Models\ElementValue;
use App\Models\EnergyLabel;
use App\Models\ExampleBuilding;
use App\Models\ExampleBuildingContent;
use App\Models\FacadeDamagedPaintwork;
use App\Models\FacadePlasteredSurface;
use App\Models\FacadeSurface;
use App\Models\InputSource;
use App\Models\InsulatingGlazing;
use App\Models\Interest;
use App\Models\MeasureApplication;
use App\Models\PaintworkStatus;
use App\Models\PvPanelOrientation;
use App\Models\RoofTileStatus;
use App\Models\RoofType;
use App\Models\Service;
use App\Models\ServiceValue;
use App\Models\WoodRotStatus;
use Illuminate\Support\Collection;

class ExampleBuildingService
{
    public static function apply(ExampleBuilding $exampleBuilding, $buildYear, Building $userBuilding)
    {
        $inputSource = InputSource::findByShort('example-building');

        // Clear the current example building data
        self::log('Lookup '.$exampleBuilding->name.' for '.$buildYear);
        $contents = $exampleBuilding->getContentForYear($buildYear);
        if (! $contents instanceof ExampleBuildingContent) {
            // There's nothing to apply
            self::log('No data to apply');

            return;
        }

        // traverse the contents:
        $exampleData = $contents->content;

        self::log('Applying Example Building '.$exampleBuilding->name.' ('.$exampleBuilding->id.', '.$contents->build_year.')');

        self::clearExampleBuilding($userBuilding);

        $features = [];

        foreach ($exampleData as $stepSlug => $stepData) {
            self::log('=====');
            self::log('Processing '.$stepSlug);
            self::log('=====');

            foreach ($stepData as $columnOrTable => $values) {
                self::log('-> '.$stepSlug.' + '.$columnOrTable.' <-');

                if (is_null($values)) {
                    self::log('Skipping '.$columnOrTable.' (empty)');
                    continue;
                }
                if ('user_interest' == $columnOrTable) {
                    self::log('Skipping outdated user interests');
                    continue;
                }
                if ('element' == $columnOrTable) {
                    // process elements
                    if (is_array($values)) {
                        foreach ($values as $elementId => $elementValueData) {
                            $extra = null;
                            if (is_array($elementValueData)) {
                                if (! array_key_exists('element_value_id', $elementValueData)) {
                                    self::log('Skipping element value as there is no element_value_id');
                                    continue;
                                }
                                $elementValueId = (int) $elementValueData['element_value_id'];
                                if (array_key_exists('extra', $elementValueData)) {
                                    $extra = $elementValueData['extra'];
                                }
                            } else {
                                $elementValueId = (int) $elementValueData;
                            }

                            $element = Element::find($elementId);
                            if ($element instanceof Element) {
                                $buildingElement = new BuildingElement(['extra' => $extra]);
                                $buildingElement->inputSource()->associate($inputSource);
                                $buildingElement->element()->associate($element);
                                $buildingElement->building()->associate($userBuilding);

                                if (! is_null($elementValueId)) {
                                    $elementValue = $element->values()->where('id', $elementValueId)->first();

                                    if ($elementValue instanceof ElementValue) {
                                        $buildingElement->elementValue()->associate($elementValue);
                                    }
                                }

                                $buildingElement->save();
                                self::log('Saving building element '.json_encode($buildingElement->toArray()));
                            }
                        }
                    }
                }
                if ('service' == $columnOrTable) {
                    // process elements
                    if (is_array($values)) {
                        foreach ($values as $serviceId => $serviceValueData) {
                            $extra = null;
                            if (is_array($serviceValueData)) {
                                if (! array_key_exists('service_value_id', $serviceValueData)) {
                                    self::log('Skipping service value as there is no service_value_id');
                                    continue;
                                }
                                $serviceValueId = (int) $serviceValueData['service_value_id'];
                                if (array_key_exists('extra', $serviceValueData)) {
                                    $extra = $serviceValueData['extra'];
                                }
                            } else {
                                $serviceValueId = (int) $serviceValueData;
                            }
                            if (! is_null($serviceValueId)) {
                                $service = Service::find($serviceId);
                                if ($service instanceof Service) {
                                    $serviceValue = $service->values()->where('id', $serviceValueId)->first();
                                    if ($serviceValue instanceof ServiceValue) {
                                        $buildingService = new BuildingService(['extra' => $extra]);
                                        $buildingService->inputSource()->associate($inputSource);
                                        $buildingService->service()->associate($service);
                                        $buildingService->serviceValue()->associate($serviceValue);
                                        $buildingService->building()->associate($userBuilding);
                                        $buildingService->save();
                                        self::log('Saving building service '.json_encode($buildingService->toArray()));
                                    }
                                }
                            }
                        }
                    }
                }
                if ('building_features' == $columnOrTable) {
                    $features = array_replace_recursive($features, $values);
                }
                if ('building_paintwork_statuses' == $columnOrTable) {
                    $statusId = array_get($values, 'paintwork_status_id');
                    $woodRotStatusId = array_get($values, 'wood_rot_status_id');

                    if (empty($statusId) || empty($woodRotStatusId)) {
                        self::log('Skipping paintwork status as the paint or wood rot (or both) status is empty');
                        continue;
                    }

                    $buildingPaintworkStatus = new BuildingPaintworkStatus($values);

                    $buildingPaintworkStatus->inputSource()->associate($inputSource);
                    $buildingPaintworkStatus->building()->associate($userBuilding);
                    $buildingPaintworkStatus->save();

                    //continue;
                }
                if ('building_insulated_glazings' == $columnOrTable) {
                    foreach ($values as $measureApplicationId => $glazingData) {
                        $glazingData['measure_application_id'] = $measureApplicationId;

                        $buildingInsulatedGlazing = new BuildingInsulatedGlazing($glazingData);

                        $buildingInsulatedGlazing->inputSource()->associate($inputSource);
                        $buildingInsulatedGlazing->building()->associate($userBuilding);
                        $buildingInsulatedGlazing->save();

                        self::log('Saving building insulated glazing '.json_encode($buildingInsulatedGlazing->toArray()));
                    }
                }
                if ('building_roof_types' == $columnOrTable) {
                    foreach ($values as $roofTypeId => $buildingRoofTypeData) {
                        $buildingRoofTypeData['roof_type_id'] = $roofTypeId;

                        $buildingRoofType = new BuildingRoofType($buildingRoofTypeData);
                        $buildingRoofType->inputSource()->associate($inputSource);
                        $buildingRoofType->building()->associate($userBuilding);
                        $buildingRoofType->save();

                        self::log('Saving building rooftype '.json_encode($buildingRoofType->toArray()));
                    }
                }
                if ('building_pv_panels' == $columnOrTable) {
                    $buildingPvPanels = new BuildingPvPanel($values);
                    $buildingPvPanels->inputSource()->associate($inputSource);
                    $buildingPvPanels->building()->associate($userBuilding);
                    $buildingPvPanels->save();

                    self::log('Saving building pv_panels '.json_encode($buildingPvPanels->toArray()));
                }
                if ('building_heaters' == $columnOrTable) {
                    $buildingHeater = new BuildingHeater($values);
                    $buildingHeater->inputSource()->associate($inputSource);
                    $buildingHeater->building()->associate($userBuilding);
                    $buildingHeater->save();

                    self::log('Saving building heater '.json_encode($buildingHeater->toArray()));
                }
            }
        }

        self::log('processing features '.json_encode($features));
        $buildingFeatures = new BuildingFeature($features);
        $buildingFeatures->buildingType()->associate($exampleBuilding->buildingType);
        $buildingFeatures->inputSource()->associate($inputSource);
        $buildingFeatures->building()->associate($userBuilding);
        $buildingFeatures->save();
        self::log('Saving building features '.json_encode($buildingFeatures->toArray()));

        //dd($exampleData);
    }

    public static function clearExampleBuilding(Building $building)
    {
        $inputSource = InputSource::findByShort('example-building');

        return BuildingDataService::clearBuildingFromInputSource($building, $inputSource);
    }

    protected static function log($text)
    {
        \Log::debug(__CLASS__.' '.$text);
    }

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
			    'building_features.surface' => [
				    'label' => Translation::translate('general-data.building-type.what-user-surface.title'),
				    'type' => 'text',
				    'unit' => Translation::translate('general.unit.square-meters.title'),
			    ],
				// elements and services
			    'user_interest.element.'.$livingRoomsWindows->id => [
				    //'label' => Translation::translate('general.change-interested.title', ['item' => $livingRoomsWindows->name]),
				    'label' => $livingRoomsWindows->name . ': ' . Translation::translate('general.interested-in-improvement.title'),
					'type' => 'select',
					'options' => $interestOptions,
				],
			    'user_interest.element.'.$sleepingRoomsWindows->id => [
				    //'label' => Translation::translate('general.change-interested.title', ['item' => $livingRoomsWindows->name]),
				    'label' => $sleepingRoomsWindows->name . ': ' . Translation::translate('general.interested-in-improvement.title'),
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
			    // ventilation
			    'service.'.$ventilation->id => [
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
			    'user_interest.element.'.$wallInsulation->id => [
				    //'label' => Translation::translate('general.change-interested.title', ['item' => $livingRoomsWindows->name]),
				    'label' => $wallInsulation->name . ': ' . Translation::translate('general.interested-in-improvement.title'),
				    'type' => 'select',
				    'options' => $interestOptions,
			    ],
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
		    ],
		    'floor-insulation' => [
			    'user_interest.element.'.$floorInsulation->id => [
				    //'label' => Translation::translate('general.change-interested.title', ['item' => $livingRoomsWindows->name]),
				    'label' => $floorInsulation->name . ': ' . Translation::translate('general.interested-in-improvement.title'),
				    'type' => 'select',
				    'options' => $interestOptions,
			    ],
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
		    ],
		    'roof-insulation' => [
			    'element.'.$roofInsulation->id => [
				    'label' => $roofInsulation->name,
				    'type' => 'select',
				    'options' => static::createOptions($roofInsulation->values()->orderBy('order')->get(), 'value'),
			    ],
			    'user_interest.element.'.$roofInsulation->id => [
				    //'label' => Translation::translate('general.change-interested.title', ['item' => $livingRoomsWindows->name]),
				    'label' => $roofInsulation->name . ': ' . Translation::translate('general.interested-in-improvement.title'),
				    'type' => 'select',
				    'options' => $interestOptions,
			    ],
			    'building_features.roof_type_id' => [
				    'label' => Translation::translate('roof-insulation.current-situation.main-roof.title'),
				    'type' => 'select',
				    'options' => static::createOptions($roofTypes),
			    ],
			    // rest will be added later on
		    ],
		    'high-efficiency-boiler' => [
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
			    'service.'.$boiler->id.'.extra' => [
				    'label' => Translation::translate('boiler.boiler-placed-date.title'),
				    'type' => 'text',
				    'unit' => Translation::translate('general.unit.year.title'),
			    ],
		    ],
//		    'heat-pump' => [
//
//		    ],
		    'solar-panels' => [
			    'service.'.$solarPanels->id => [
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
		    ],
		    'heater' => [
			    'service.'.$heater->id => [
				    'label' => $heater->name,
				    'type' => 'select',
				    'options' => static::createOptions($heater->values()->orderBy('order')->get(), 'value'),
			    ],
			    'user_interest.service.'.$heater->id => [
				    //'label' => Translation::translate('general.change-interested.title', ['item' => $livingRoomsWindows->name]),
				    'label' => $heater->name . ': ' . Translation::translate('general.interested-in-improvement.title'),
				    'type' => 'select',
				    'options' => $interestOptions,
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
		    ],
	    ];

		/*
		// From GeneralDataController
		$interestElements = Element::whereIn('short', [
			'living-rooms-windows', 'sleeping-rooms-windows',
		])->orderBy('order')->get();

		foreach ($interestElements as $interestElement) {
			$k = 'user_interest.element.'.$interestElement->id;
			$structure['general-data'][$k] = [
				'label' => 'Interest in '.$interestElement->name,
				'type' => 'select',
				'options' => $interestOptions,
			];
		}
		*/


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
}
