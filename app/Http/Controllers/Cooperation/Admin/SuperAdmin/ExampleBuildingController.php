<?php

namespace App\Http\Controllers\Cooperation\Admin\SuperAdmin;

use App\Helpers\HoomdossierSession;
use App\Helpers\KeyFigures\Heater\KeyFigures as HeaterKeyFigures;
use App\Helpers\KeyFigures\PvPanels\KeyFigures as SolarPanelsKeyFigures;
use App\Helpers\KeyFigures\RoofInsulation\Temperature;
use App\Helpers\Translation;
use App\Http\Controllers\Controller;
use App\Http\Requests\Cooperation\Admin\SuperAdmin\ExampleBuildingRequest;
use App\Models\BuildingHeating;
use App\Models\BuildingType;
use App\Models\Cooperation;
use App\Models\Element;
use App\Models\ExampleBuilding;
use App\Models\ExampleBuildingContent;
use App\Models\FacadeDamagedPaintwork;
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
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class ExampleBuildingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response|\Illuminate\View\View
     */
    public function index()
    {
        $exampleBuildingsQuery = ExampleBuilding::orderBy('cooperation_id', 'asc')
                                                ->orderBy('order', 'asc');

        if (false === stristr(HoomdossierSession::currentRole(), 'super')) {
            $exampleBuildingsQuery->forMyCooperation();
        }

        $exampleBuildings = $exampleBuildingsQuery->get();

        return view('cooperation.admin.super-admin.example-buildings.index', compact('exampleBuildings'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response|\Illuminate\View\View
     */
    public function create()
    {
        $buildingTypes = BuildingType::all();
        $cooperations = Cooperation::all();

        $contentStructure = $this->getContentStructure();

        return view('cooperation.admin.super-admin.example-buildings.create',
            compact(
                'buildingTypes', 'cooperations', 'contentStructure'
            )
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'building_type_id' => 'required|exists:building_types,id',
            'cooperation_id' => 'nullable|exists:cooperations,id',
            'is_default' => 'required|boolean',
            'order' => 'nullable|numeric|min:0',
            'content.*.build_year' => 'nullable|numeric|min:1500|max:2025',
        ]);

        $buildingType = BuildingType::findOrFail($request->get('building_type_id'));
        $cooperation = Cooperation::find($request->get('cooperation_id'));

        $exampleBuilding = new ExampleBuilding();

        $translations = $request->input('name', []);
        $translations = array_only($translations, config('woningdossier.supported_locales'));
        $exampleBuilding->createTranslations('name', $translations);

        $exampleBuilding->buildingType()->associate($buildingType);
        if (! is_null($cooperation)) {
            $exampleBuilding->cooperation()->associate($cooperation);
        }
        $exampleBuilding->is_default = $request->get('is_default', false);
        $exampleBuilding->order = $request->get('order', null);
        $exampleBuilding->save();

        $contents = $request->input('content', []);

        foreach ($contents as $cid => $data) {
            $data['content'] = array_key_exists('content', $data) ? $this->array_undot($data['content']) : [];
            if (! is_numeric($cid) && 'new' == $cid) {
                if (1 == $request->get('new', 0)) {
                    // addition
                    $content = new ExampleBuildingContent($data);
                }
            } else {
                $content = $exampleBuilding->contents()->where('id', $cid)->first();
                $content->fill($data);
            }
            if (isset($content) && $content instanceof ExampleBuildingContent) {
                $content->exampleBuilding()->associate($exampleBuilding);
                $content->save();
            }
        }

        return redirect()->route('cooperation.admin.super-admin.example-buildings.edit', ['id' => $exampleBuilding])->with('success', 'This example building was added');
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  $cooperation
     * @param int $id
     *
     * @return \Illuminate\Http\Response|\Illuminate\View\View
     */
    public function edit(Cooperation $cooperation, $id)
    {
        /** @var ExampleBuilding $exampleBuilding */
        $exampleBuilding = ExampleBuilding::findOrFail($id);
        $buildingTypes = BuildingType::all();
        $cooperations = Cooperation::all();

        $contentStructure = $this->getContentStructure();

        return view('cooperation.admin.super-admin.example-buildings.edit',
            compact(
                'exampleBuilding', 'buildingTypes',
                'cooperations', 'contentStructure'
            )
        );
    }

    /**
     * Returns the content structure as nested array for the ExampleBuilding
     * configuration (form and data structure for storage).
     *
     * @return array
     */
    protected function getContentStructure()
    {
        // Wall insulation
        $wallInsulation = Element::where('short', 'wall-insulation')->first();
        $facadeDamages = FacadeDamagedPaintwork::orderBy('order')->get();
        $surfaces = FacadeSurface::orderBy('order')->get();

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
        $boiler = Service::where('short', 'boiler')->first();
        //$solarPanels = Service::where('short', 'total-sun-panels')->first();
        $solarPanelsOptionsPeakPower = ['' => '-'] + SolarPanelsKeyFigures::getPeakPowers();
        $solarPanelsOptionsAngle = ['' => '-'] + SolarPanelsKeyFigures::getAngles();

        //$heater = Service::where('short', 'sun-boiler')->first();
        $heaterOptionsAngle = ['' => '-'] + HeaterKeyFigures::getAngles();

        // Common
        //$interests = Interest::orderBy('order')->get();
        //$interestOptions = $this->createOptions($interests);

        $structure = [
            'general-data' => [
                'building_features.surface' => [
                    'label' => Translation::translate('general-data.building-type.what-user-surface.title'),
                    'type' => 'text',
                    'unit' => Translation::translate('general.unit.square-meters.title'),
                ],
                // user interests
            ],
            'wall-insulation' => [
                /*'user_interest.element.'.$wallInsulation->id => [
                    'label' => 'Interest in '.$wallInsulation->name,
                    'type' => 'select',
                    'options' => $interestOptions,
                ],*/
                'element.'.$wallInsulation->id => [
                    'label' => Translation::translate('wall-insulation.intro.filled-insulation.title'),
                    'type' => 'select',
                    'options' => $this->createOptions($wallInsulation->values()->orderBy('order')->get(), 'value'),
                ],
                'building_features.wall_surface' => [
                    'label' => Translation::translate('wall-insulation.optional.facade-surface.title'),
                    'type' => 'text',
                    'unit' => Translation::translate('general.unit.square-meters.title'),
                ],
                'building_features.insulation_wall_surface' => [
                    'label' => Translation::translate('wall-insulation.optional.insulation-wall-surface.title'),
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
                'building_features.facade_damaged_paintwork_id' => [
                    'label' => Translation::translate('wall-insulation.intro.damage-paintwork.title'),
                    'type' => 'select',
                    'options' => $this->createOptions($facadeDamages),
                ],
                'building_features.wall_joints' => [
                    'label' => Translation::translate('wall-insulation.optional.flushing.title'),
                    'type' => 'select',
                    'options' => $this->createOptions($surfaces),
                ],
                'building_features.contaminated_wall_joints' => [
                    'label' => Translation::translate('wall-insulation.optional.if-facade-dirty.title'),
                    'type' => 'select',
                    'options' => $this->createOptions($surfaces),
                ],
            ],
            
            'insulated-glazing' => [
                // will be filled in later
                'element.'.$crackSealing->id => [
                    'label' => Translation::translate('insulated-glazing.moving-parts-quality.title'),
                    'type' => 'select',
                    'options' => $this->createOptions($crackSealing->values()->orderBy('order')->get(), 'value.'),
                ],
                'building_features.window_surface' => [
                    'label' => Translation::translate('insulated-glazing.windows-surface.title'),
                    'type' => 'text',
                    'unit' => Translation::translate('general.unit.square-meters.title'),
                ],
                'element.'.$frames->id => [
                    'label' => Translation::translate('insulated-glazing.paint-work.which-frames.title'),
                    'type' => 'select',
                    'options' => $this->createOptions($frames->values()->orderBy('order')->get(), 'value'),
                ],
                'building_paintwork_statuses.last_painted_year' => [
                    'label' => Translation::translate('insulated-glazing.paint-work.last-paintjob.title'),
                    'type' => 'text',
                    'unit' => Translation::translate('general.unit.year.title'),
                ],
                'building_paintwork_statuses.paintwork_status_id' => [
                    'label' => Translation::translate('insulated-glazing.paint-work.paint-damage-visible.title'),
                    'type' => 'select',
                    'options' => $this->createOptions($paintworkStatuses),
                ],
                'building_paintwork_statuses.wood_rot_status_id' => [
                    'label' => Translation::translate('insulated-glazing.paint-work.wood-rot-visible.title'),
                    'type' => 'select',
                    'options' => $this->createOptions($woodRotStatuses),
                ],
            ],
            'floor-insulation' => [
                /*'user_interest.element.'.$floorInsulation->id => [
                    'label' => 'Interest in '.$floorInsulation->name,
                    'type' => 'select',
                    'options' => $interestOptions,
                ],*/
                'element.'.$floorInsulation->id => [
                    'label' => Translation::translate('floor-insulation.floor-insulation.title'),
                    'type' => 'select',
                    'options' => $this->createOptions($floorInsulation->values()->orderBy('order')->get(), 'value'),
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
                    'options' => $this->createOptions($crawlspace->values()->orderBy('order')->get(), 'value'),
                ],
            ],
            'roof-insulation' => [
                /*'user_interest.element.'.$roofInsulation->id => [
                    'label' => 'Interest in '.$roofInsulation->name,
                    'type' => 'select',
                    'options' => $interestOptions,
                ],*/
                'building_features.roof_type_id' => [
                    'label' => Translation::translate('roof-insulation.current-situation.main-roof.title'),
                    'type' => 'select',
                    'options' => $this->createOptions($roofTypes),
                ],
                // rest will be added later on
            ],
            'high-efficiency-boiler' => [
                // no use for user interest here..

                'service.'.$boiler->id.'.service_value_id' => [
                    'label' => Translation::translate('boiler.boiler-type.title'),
                    'type' => 'select',
                    'options' => $this->createOptions($boiler->values()->orderBy('order')->get(), 'value'),
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
                    'options' => $this->createOptions(PvPanelOrientation::orderBy('order')->get()),
                ],
                'building_pv_panels.angle' => [
                    'label' => Translation::translate('solar-panels.angle.title'),
                    'type' => 'select',
                    'options' => $solarPanelsOptionsAngle,
                ],
            ],
            'heater' => [
                'building_heaters.pv_panel_orientation_id' => [
                    'label' => Translation::translate('heater.pv-panel-orientation-id.title'),
                    'type' => 'select',
                    'options' => $this->createOptions(PvPanelOrientation::orderBy('order')->get()),
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
                /*$structure['insulated-glazing']['user_interests.'.$measureApplication->id] = [
                    'label' => 'Interest in '.$measureApplication->measure_name,
                    'type' => 'select',
                    'options' => $interestOptions,
                ];*/
                $structure['insulated-glazing']['building_insulated_glazings.'.$measureApplication->id.'.insulated_glazing_id'] = [
                    'label' => $measureApplication->measure_name.': '.Translation::translate('insulated-glazing.'.$measureApplication->short.'.current-glass.title'),
                    'type' => 'select',
                    'options' => $this->createOptions($insulatedGlazings),
                ];
                $structure['insulated-glazing']['building_insulated_glazings.'.$measureApplication->id.'.building_heating_id'] = [
                    'label' => $measureApplication->measure_name.': '.Translation::translate('insulated-glazing.'.$measureApplication->short.'.rooms-heated.title'),
                    'type' => 'select',
                    'options' => $this->createOptions($heatings),
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
                'options' => $this->createOptions($roofInsulation->values, 'value'),
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
                    'options' => $this->createOptions($roofTileStatuses),
                ];
            }
            $structure['roof-insulation']['building_roof_types.'.$roofType->id.'.extra.measure_application_id'] = [
                'label' => Translation::translate('roof-insulation.'.$roofType->short.'-roof.insulate-roof.title'),
                'type' => 'select',
                'options' => $this->createOptions(collect($roofInsulationMeasureApplications[$roofType->short]), 'measure_name.title'),
            ];
            $structure['roof-insulation']['building_roof_types.'.$roofType->id.'.building_heating_id'] = [
                'label' => Translation::translate('roof-insulation.'.$roofType->short.'-roof.situation.title'),
                'type' => 'select',
                'options' => $this->createOptions($heatings),
            ];
        }

        return $structure;
    }

    protected function createOptions(Collection $collection, $value = 'name', $id = 'id', $nullPlaceholder = true)
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
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int                      $id
     * @param  $cooperation
     *
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function update(ExampleBuildingRequest $request, Cooperation $cooperation, $id)
    {
        /** @var ExampleBuilding $exampleBuilding */
        $exampleBuilding = ExampleBuilding::findOrFail($id);

        $this->validate($request, [
            'building_type_id' => 'required|exists:building_types,id',
            'cooperation_id' => 'nullable|exists:cooperations,id',
            'is_default' => 'required|boolean',
            'order' => 'nullable|numeric|min:0',
            'content.*.build_year' => 'nullable|numeric|min:1500|max:2025',
        ]);

        $buildingType = BuildingType::findOrFail($request->get('building_type_id'));
        $cooperation = Cooperation::find($request->get('cooperation_id'));

        $translations = $request->input('name', []);
        foreach (config('woningdossier.supported_locales') as $locale) {
            if (isset($translations[$locale]) && ! empty($translations[$locale])) {
                $exampleBuilding->updateTranslation('name', $translations[$locale], $locale);
            }
        }

        $exampleBuilding->buildingType()->associate($buildingType);
        if (! is_null($cooperation)) {
            $exampleBuilding->cooperation()->associate($cooperation);
        }
        $exampleBuilding->is_default = $request->get('is_default', false);
        $exampleBuilding->order = $request->get('order', null);

        $contents = $request->input('content', []);

        foreach ($contents as $cid => $data) {
            $data['content'] = array_key_exists('content', $data) ? $this->array_undot($data['content']) : [];

            $content = null;
            if (! is_numeric($cid) && 'new' == $cid) {
                if (1 == $request->get('new', 0)) {
                    // addition
                    $content = new ExampleBuildingContent($data);
                }
            } else {
                $content = $exampleBuilding->contents()->where('id', $cid)->first();
                $content->fill($data);
            }
            if ($content instanceof ExampleBuildingContent) {
                $content->exampleBuilding()->associate($exampleBuilding);
                $content->save();
            }
        }
        $exampleBuilding->save();

        return redirect()->route('cooperation.admin.super-admin.example-buildings.edit', ['id' => $id])->with('success', 'Example building updated');
    }

    protected function array_undot($content)
    {
        $array = [];
        foreach ($content as $key => $values) {
            foreach ($values as $dottedKey => $value) {
                array_set($array, $key.'.'.$dottedKey, $value);
            }
        }

        return $array;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @param  $cooperation
     *
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function destroy(Cooperation $cooperation, $id)
    {
        /** @var ExampleBuilding $exampleBuilding */
        $exampleBuilding = ExampleBuilding::findOrFail($id);
        try {
            $exampleBuilding->delete();
        } catch (\Exception $e) {
            // do nothing
        }

        return redirect()->route('cooperation.admin.super-admin.example-buildings.index')->with('success', 'Example building deleted');
    }

    /**
     * Copies over a specific example building configuration (content / structure).
     *
     * @param Cooperation $cooperation
     * @param int         $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function copy(Cooperation $cooperation, $id)
    {
        /** @var ExampleBuilding $exampleBuilding */
        $exampleBuilding = ExampleBuilding::findOrFail($id);
        $exampleBuildingContents = $exampleBuilding->contents;
        $translations = $exampleBuilding->getTranslations('name');
        $names = [];
        foreach ($translations as $translation) {
            $names[$translation->language] = $translation->translation.' (copy)';
        }

        $newEB = new ExampleBuilding($exampleBuilding->toArray());
        $name = $newEB->createTranslations('name', $names);
        $newEB->name = $name;
        $newEB->save();

        /** @var ExampleBuildingContent $exampleBuildingContent */
        foreach ($exampleBuildingContents as $exampleBuildingContent) {
            $newEBC = new ExampleBuildingContent($exampleBuildingContent->toArray());
            $newEBC->exampleBuilding()
                   ->associate($newEB)
                   ->save();
        }

        return redirect()->route('cooperation.admin.super-admin.example-buildings.index')->with('success', 'Example building copied');
    }
}
