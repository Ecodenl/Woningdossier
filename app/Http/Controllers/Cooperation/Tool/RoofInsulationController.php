<?php

namespace App\Http\Controllers\Cooperation\Tool;

use App\Events\StepDataHasBeenChangedEvent;
use App\Helpers\Calculation\BankInterestCalculator;
use App\Helpers\Calculator;
use App\Helpers\Hoomdossier;
use App\Helpers\HoomdossierSession;
use App\Helpers\KeyFigures\RoofInsulation\Temperature;
use App\Helpers\NumberFormatter;
use App\Helpers\RoofInsulationCalculator;
use App\Helpers\StepHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\RoofInsulationFormRequest;
use App\Models\Building;
use App\Models\BuildingFeature;
use App\Models\BuildingHeating;
use App\Models\BuildingRoofType;
use App\Models\Cooperation;
use App\Models\Element;
use App\Models\ElementValue;
use App\Models\Interest;
use App\Models\MeasureApplication;
use App\Models\RoofTileStatus;
use App\Models\RoofType;
use App\Models\Step;
use App\Models\UserActionPlanAdvice;
use App\Models\UserEnergyHabit;
use App\Models\UserInterest;
use App\Scopes\GetValueScope;
use App\Services\ModelService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class RoofInsulationController extends Controller
{
    protected $step;

    public function __construct(Request $request)
    {
        $slug = str_replace('/tool/', '', $request->getRequestUri());
        $this->step = Step::where('slug', $slug)->first();
    }

    /**
     * Display a listing of the resource.
     *
     * return \Illuminate\Http\Response
     */
    public function index()
    {
        $typeIds = [5];

        /** var Building $building */
        $building = Building::find(HoomdossierSession::getBuilding());

        /** var BuildingFeature $features */
        $features = $building->buildingFeatures;
        $buildingFeaturesForMe = $building->buildingFeatures()->forMe()->get();

        $roofTypes = RoofType::all();

        $currentRoofTypes = $building->roofTypes;
        $currentRoofTypesForMe = $building->roofTypes()->forMe()->get();

        $roofTileStatuses = RoofTileStatus::orderBy('order')->get();
        $roofInsulation = Element::where('short', 'roof-insulation')->first();
        $heatings = BuildingHeating::all();
        $measureApplications = $this->getMeasureApplicationsAdviceMap();

        $currentCategorizedRoofTypes = [
            'flat' => [],
            'pitched' => [],
        ];

        $currentCategorizedRoofTypesForMe = [
            'flat' => [],
            'pitched' => [],
        ];

        if ($currentRoofTypes instanceof Collection) {
            /** var BuildingRoofType $currentRoofType */
            foreach ($currentRoofTypes as $currentRoofType) {
                $cat = $this->getRoofTypeCategory($currentRoofType->roofType);
                if (! empty($cat)) {
                    $currentCategorizedRoofTypes[$cat] = $currentRoofType->toArray();
                }
            }

            foreach ($currentRoofTypesForMe as $currentRoofTypeForMe) {
                $cat = $this->getRoofTypeCategory($currentRoofTypeForMe->roofType);
                if (! empty($cat)) {
                    // we do not want this to be an array, otherwise we would have to add additional functionality to the input group component.
                    $currentCategorizedRoofTypesForMe[$cat][] = $currentRoofTypeForMe;
                }
            }
        }

        return view('cooperation.tool.roof-insulation.index', compact(
            'building', 'features', 'roofTypes', 'typeIds', 'buildingFeaturesForMe',
             'currentRoofTypes', 'roofTileStatuses', 'roofInsulation', 'currentRoofTypesForMe',
             'heatings', 'measureApplications', 'currentCategorizedRoofTypes', 'currentCategorizedRoofTypesForMe'));
    }

    protected function getRoofTypeCategory(RoofType $roofType)
    {
        if ($roofType->calculate_value <= 2) {
            return 'pitched';
        }
        if ($roofType->calculate_value <= 4) {
            return 'flat';
        }

        return '';
    }

    protected function getRoofTypeSubCategory(RoofType $roofType)
    {
        if (1 == $roofType->calculate_value) {
            return 'tiles';
        }
        if (2 == $roofType->calculate_value) {
            return 'bitumen';
        }
        if (4 == $roofType->calculate_value) {
            return 'zinc';
        }

        return '';
    }

    protected function getMeasureApplicationsAdviceMap()
    {
        return [
            'flat' => [
                Temperature::ROOF_INSULATION_FLAT_ON_CURRENT => MeasureApplication::where('short', 'roof-insulation-flat-current')->first(),
                Temperature::ROOF_INSULATION_FLAT_REPLACE => MeasureApplication::where('short', 'roof-insulation-flat-replace-current')->first(),
            ],
            'pitched' => [
                Temperature::ROOF_INSULATION_PITCHED_INSIDE => MeasureApplication::where('short', 'roof-insulation-pitched-inside')->first(),
                Temperature::ROOF_INSULATION_PITCHED_REPLACE_TILES => MeasureApplication::where('short', 'roof-insulation-pitched-replace-tiles')->first(),
            ],
        ];
    }

    protected function saveAdvices(Request $request)
    {
        /** var JsonResponse $results */
        $results = $this->calculate($request);
        $results = $results->getData(true);

        $result = [];

        $user = Building::find(HoomdossierSession::getBuilding())->user;

        // Remove old results
        UserActionPlanAdvice::forMe()->where('input_source_id', HoomdossierSession::getInputSource())->forStep($this->step)->delete();

        $roofTypes = $request->input('building_roof_types', []);
        foreach ($roofTypes as $i => $details) {
            if (is_numeric($i) && is_numeric($details)) {
                $roofType = RoofType::find($details);
                if ($roofType instanceof RoofType) {
                    $cat = $this->getRoofTypeCategory($roofType);
                    // add as key to result array
                    $result[$cat] = [
                        'type' => $this->getRoofTypeSubCategory($roofType),
                    ];
                }
            }
        }

        foreach (array_keys($result) as $roofCat) {
            $isBitumenOnPitchedRoof = 'pitched' == $roofCat && $results['pitched']['type'] == 'bitumen';
            // It's a bitumen roof is the category is not pitched or none (so currently only: flat)
            $isBitumenRoof = ! in_array($roofCat, ['none', 'pitched']) || $isBitumenOnPitchedRoof;

            $measureApplicationId = $request->input('building_roof_types.'.$roofCat.'.measure_application_id', 0);
            if ($measureApplicationId > 0) {
                // results in an advice
                $measureApplication = MeasureApplication::find($measureApplicationId);
                if ($measureApplication instanceof MeasureApplication) {
                    $actionPlanAdvice = null;
                    $advicedYear = '';

                    $interests = $request->input('interest', '');
                    foreach ($interests as $type => $interestTypes) {
                        foreach ($interestTypes as $typeId => $interestId) {
                            $interest = Interest::find($interestId);

                            if (1 == $interest->calculate_value) {
                                // on short term: this year
                                $advicedYear = Carbon::now()->year;
                            } elseif (2 == $interest->calculate_value) {
                                // on term: this year + 5
                                $advicedYear = Carbon::now()->year + 5;
                            } else {
                                $advicedYear = $results[$roofCat]['replace']['year'];
                            }
                        }
                    }
                    // The measure type determines which array keys to take
                    // as the replace array will always be present due to
                    // how calculate() works in this step
                    /*if ('replace' == $measureApplication->application) {
                        if (isset($results[$roofCat]['replace']['costs']) && $results[$roofCat]['replace']['costs'] > 0) {
                            // take the replace array
                            $actionPlanAdvice = new UserActionPlanAdvice($results[$roofCat]['replace']);
                            $actionPlanAdvice->savings_gas = $results[$roofCat]['savings_gas'];
                            $actionPlanAdvice->savings_money = $results[$roofCat]['savings_money'];
                        }
                    } else {*/
                    if (isset($results[$roofCat]['cost_indication']) && $results[$roofCat]['cost_indication'] > 0) {
                        // take the array $roofCat array
                        $actionPlanAdvice = new UserActionPlanAdvice($results[$roofCat]);
                        $actionPlanAdvice->year = $advicedYear;
                        $actionPlanAdvice->costs = $results[$roofCat]['cost_indication'];
                    }
                    //}

                    if ($actionPlanAdvice instanceof UserActionPlanAdvice) {
                        $actionPlanAdvice->user()->associate($user);
                        $actionPlanAdvice->measureApplication()->associate($measureApplication);
                        $actionPlanAdvice->step()->associate($this->step);
                        $actionPlanAdvice->save();
                    }
                }
            }
            $extra = $request->input('building_roof_types.'.$roofCat.'.extra', []);
            if (array_key_exists('zinc_replaced_date', $extra)) {
                $zincReplaceYear = (int) $extra['zinc_replaced_date'];
                $surface = $request->input('building_roof_types.'.$roofCat.'.insulation_roof_surface', 0);
                if ($zincReplaceYear > 0 && $surface > 0) {
                    $zincReplaceMeasure = MeasureApplication::where('short', 'replace-zinc')->first();

                    $year = RoofInsulationCalculator::determineApplicationYear($zincReplaceMeasure, $zincReplaceYear, 1);
                    $costs = Calculator::calculateMeasureApplicationCosts($zincReplaceMeasure, $surface, $year, false);

                    $actionPlanAdvice = new UserActionPlanAdvice(compact('costs', 'year'));
                    $actionPlanAdvice->user()->associate($user);
                    $actionPlanAdvice->measureApplication()->associate($zincReplaceMeasure);
                    $actionPlanAdvice->step()->associate($this->step);
                    $actionPlanAdvice->save();
                }
            }
            if (array_key_exists('tiles_condition', $extra)) {
                $tilesCondition = (int) $extra['tiles_condition'];
                $surface = $request->input('building_roof_types.'.$roofCat.'.roof_surface', 0);
                if ($tilesCondition > 0 && $surface > 0) {
                    $replaceMeasure = MeasureApplication::where('short', 'replace-tiles')->first();
                    // no year here. Default is this year. It is incremented by factor * maintenance years
                    $year = Carbon::now()->year;
                    $roofTilesStatus = RoofTileStatus::find($tilesCondition);

                    if ($roofTilesStatus instanceof RoofTileStatus) {
                        $factor = ($roofTilesStatus->calculate_value / 100);

                        $year = RoofInsulationCalculator::determineApplicationYear($replaceMeasure, $year, $factor);
                        $costs = Calculator::calculateMeasureApplicationCosts($replaceMeasure, $surface, $year, false);

                        $actionPlanAdvice = new UserActionPlanAdvice(compact('costs', 'year'));
                        $actionPlanAdvice->user()->associate($user);
                        $actionPlanAdvice->measureApplication()->associate($replaceMeasure);
                        $actionPlanAdvice->step()->associate($this->step);
                        $actionPlanAdvice->save();
                    }
                }
            }
            if ($isBitumenRoof && array_key_exists('bitumen_replaced_date', $extra)) {
                $bitumenReplaceYear = (int) $extra['bitumen_replaced_date'];
                if ($bitumenReplaceYear <= 0) {
                    $bitumenReplaceYear = Carbon::now()->year - 10;
                }
                $surface = $request->input('building_roof_types.'.$roofCat.'.roof_surface', 0);

                if ($bitumenReplaceYear > 0 && $surface > 0) {
                    $replaceMeasure = MeasureApplication::where('short', 'replace-roof-insulation')->first();
                    // no percentages here. We just do this to keep the determineApplicationYear definition in one place
                    $year = $bitumenReplaceYear;
                    $factor = 1;

                    $year = RoofInsulationCalculator::determineApplicationYear($replaceMeasure, $year, $factor);
                    $costs = Calculator::calculateMeasureApplicationCosts($replaceMeasure, $surface, $year, false);

                    $actionPlanAdvice = new UserActionPlanAdvice(compact('costs', 'year'));
                    $actionPlanAdvice->user()->associate($user);
                    $actionPlanAdvice->measureApplication()->associate($replaceMeasure);
                    $actionPlanAdvice->step()->associate($this->step);
                    $actionPlanAdvice->save();
                }
            }
        }
    }

    public function calculate(Request $request)
    {
        $result = [];

        /** @var Building $building */
        $building = Building::find(HoomdossierSession::getBuilding());
        $user = $building->user;

        $roofTypes = $request->input('building_roof_types', []);
        foreach ($roofTypes as $i => $details) {
            if (is_numeric($i) && is_numeric($details)) {
                $roofType = RoofType::find($details);
                if ($roofType instanceof RoofType) {
                    $cat = $this->getRoofTypeCategory($roofType);
                    // add as key to result array
                    $result[$cat] = [
                        'type' => $this->getRoofTypeSubCategory($roofType),
                    ];
                }
            }
        }

        $roofInsulation = Element::where('short', 'roof-insulation')->first();
        $adviceMap = $this->getMeasureApplicationsAdviceMap();
        $totalSurface = 0;

        foreach (array_keys($result) as $cat) {
            $totalSurface += isset($roofTypes[$cat]['insulation_roof_surface']) ? $roofTypes[$cat]['insulation_roof_surface'] : 0;
        }

        foreach (array_keys($result) as $cat) {
            // defaults
            $catData = [
                'savings_gas' => 0,
                'savings_co2' => 0,
                'savings_money' => 0,
                'cost_indication' => 0,
                'interest_comparable' => 0,
                'replace' => [
                    'costs' => 0,
                    'year' => null,
                ],
            ];

            $surface = $roofTypes[$cat]['insulation_roof_surface'] ?? 0;
            $heating = null;
            // should take the bitumen field

            // A pitched roof with bitumen could be the case earlier on.
            // Not sure if this can never be the case again in the future, but
            // we account for it in this function. Therefor we have to calculate
            // a little bit differently in the case of a pitched roof covered
            // in bitumen instead of roof tiles
            $isBitumenOnPitchedRoof = 'pitched' == $cat && $result['pitched']['type'] == 'bitumen';
            // It's a bitumen roof is the category is not pitched or none (so currently only: flat)
            $isBitumenRoof = ! in_array($cat, ['none', 'pitched']) || $isBitumenOnPitchedRoof;

            if ($isBitumenRoof) {
                $year = isset($roofTypes[$cat]['extra']['bitumen_replaced_date']) ? (int) $roofTypes[$cat]['extra']['bitumen_replaced_date'] : Carbon::now()->year - 10;
            } else {
                $year = Carbon::now()->year;
            }

            // default, changes only for roof tiles effect
            $factor = 1;

            $advice = null;
            $objAdvice = null;

            if (isset($roofTypes[$cat]['building_heating_id'])) {
                $heating = BuildingHeating::find($roofTypes[$cat]['building_heating_id']);
            }
            if (isset($roofTypes[$cat]['measure_application_id'])) {
                $measureAdvices = $adviceMap[$cat];
                foreach ($measureAdvices as $strAdvice => $measureAdvice) {
                    if ($roofTypes[$cat]['measure_application_id'] == $measureAdvice->id) {
                        $advice = $strAdvice;
                        // we do this as we don't want the advice to be in
                        // $result['insulation_advice'] as in other calculating
                        // controllers
                        $objAdvice = $measureAdvice;
                    }
                }
            }

            if (isset($roofTypes[$cat]['element_value_id'])) {
                // Current roof insulation level
                $roofInsulationValue = ElementValue::where('element_id', $roofInsulation->id)->where('id', $roofTypes[$cat]['element_value_id'])->first();

                if ($roofInsulationValue instanceof ElementValue && $heating instanceof BuildingHeating && isset($advice)) {
                    if ($user->energyHabit instanceof UserEnergyHabit) {
                        $catData['savings_gas'] = RoofInsulationCalculator::calculateGasSavings($building, $roofInsulationValue, $user->energyHabit, $heating, $surface, $totalSurface, $advice);
                    }
                    $catData['savings_co2'] = Calculator::calculateCo2Savings($catData['savings_gas']);
                    $catData['savings_money'] = round(Calculator::calculateMoneySavings($catData['savings_gas']));
                    $catData['cost_indication'] = Calculator::calculateCostIndication($surface, $objAdvice);
                    $catData['interest_comparable'] = NumberFormatter::format(BankInterestCalculator::getComparableInterest($catData['cost_indication'], $catData['savings_money']), 1);
                    // The replace year is about the replacement of bitumen..
                    $catData['replace']['year'] = RoofInsulationCalculator::determineApplicationYear($objAdvice, $year, $factor);
                }
            }

            // If tiles condition is set, use the status to calculate the replace moment
            $tilesCondition = isset($roofTypes[$cat]['extra']['tiles_condition']) ? (int) $roofTypes[$cat]['extra']['tiles_condition'] : null;
            if (! is_null($tilesCondition)) {
                $replaceMeasure = MeasureApplication::where('short', 'replace-tiles')->first();
                // no year here. Default is this year. It is incremented by factor * maintenance years
                $year = Carbon::now()->year;
                $roofTilesStatus = RoofTileStatus::find($tilesCondition);
                if ($roofTilesStatus instanceof RoofTileStatus) {
                    $factor = ($roofTilesStatus->calculate_value / 100);
                }
            }

            if ($isBitumenRoof) {
                // If it is a bitumen roof, $year is already set to the best
                // value.
                $replaceMeasure = MeasureApplication::where('short', 'replace-roof-insulation')->first();
            }

            if (isset($replaceMeasure)) {
                $surface = $roofTypes[$cat]['roof_surface'] ?? 0;
                $catData['replace']['year'] = RoofInsulationCalculator::determineApplicationYear($replaceMeasure, $year, $factor);
                $catData['replace']['costs'] = Calculator::calculateMeasureApplicationCosts($replaceMeasure, $surface, $catData['replace']['year'], false);
            }

            $result[$cat] = array_merge($result[$cat], $catData);
        }

        return response()->json($result);
    }

    /**
     * Store a newly created resource in storage.
     *
     * param  \Illuminate\Http\Request  $request
     * return \Illuminate\Http\Response
     */
    public function store(RoofInsulationFormRequest $request)
    {
        $building = Building::find(HoomdossierSession::getBuilding());
        $user = $building->user;
        $buildingId = $building->id;
        $inputSourceId = HoomdossierSession::getInputSource();

        $interests = $request->input('interest', '');
        UserInterest::saveUserInterests($user, $interests);

        // the selected roof types for the current situation
        $roofTypes = $request->input('building_roof_types', []);

        $createData = [];
        foreach ($roofTypes as $i => $details) {
            if (is_numeric($i) && is_numeric($details)) {
                $roofType = RoofType::find($details);
                if ($roofType instanceof RoofType) {
                    $cat = $this->getRoofTypeCategory($roofType);
                    // add as key to result array
                    $result[$cat] = [
                        'type' => $this->getRoofTypeSubCategory($roofType),
                    ];

                    $roofSurface = isset($roofTypes[$cat]['roof_surface']) ? $roofTypes[$cat]['roof_surface'] : 0;
                    $insulationRoofSurface = isset($roofTypes[$cat]['insulation_roof_surface']) ? $roofTypes[$cat]['insulation_roof_surface'] : 0;
                    $elementValueId = isset($roofTypes[$cat]['element_value_id']) ? $roofTypes[$cat]['element_value_id'] : null;

                    $extraMeasureApplication = isset($roofTypes[$cat]['measure_application_id']) ? $roofTypes[$cat]['measure_application_id'] : '';
                    $extraBitumenReplacedDate = isset($roofTypes[$cat]['extra']['bitumen_replaced_date']) ? $roofTypes[$cat]['extra']['bitumen_replaced_date'] : Carbon::now()->year - 10;
                    $extraZincReplacedDate = isset($roofTypes[$cat]['extra']['zinc_replaced_date']) ? $roofTypes[$cat]['extra']['zinc_replaced_date'] : '';
                    $extraTilesCondition = isset($roofTypes[$cat]['extra']['tiles_condition']) ? $roofTypes[$cat]['extra']['tiles_condition'] : '';

                    $buildingHeating = isset($roofTypes[$cat]['building_heating_id']) ? $roofTypes[$cat]['building_heating_id'] : null;
                    $comment = isset($roofTypes[$cat]['extra']['comment']) ? $roofTypes[$cat]['extra']['comment'] : null;

                    BuildingFeature::withoutGlobalScope(GetValueScope::class)->updateOrCreate(
                        [
                            'building_id' => $buildingId,
                            'input_source_id' => $inputSourceId,
                        ],
                        [
                            'roof_type_id' => $request->input('building_features.roof_type_id'),
                        ]
                    );

                    array_push($createData, [
                        'roof_type_id' => $roofType->id,
                        'element_value_id' => $elementValueId,
                        'roof_surface' => $roofSurface,
                        'insulation_roof_surface' => $insulationRoofSurface,
                        'building_heating_id' => $buildingHeating,
                        'extra' => [
                            'measure_application_id' => $extraMeasureApplication,
                            'bitumen_replaced_date' => $extraBitumenReplacedDate,
                            'zinc_replaced_date' => $extraZincReplacedDate,
                            'tiles_condition' => $extraTilesCondition,
                            'comment' => $comment,
                        ],
                    ]);
                }
            }
        }

        ModelService::deleteAndCreate(BuildingRoofType::class,
            [
                'building_id' => $buildingId,
                'input_source_id' => $inputSourceId,
            ],
            $createData
        );

        \Event::dispatch(new StepDataHasBeenChangedEvent());
        // Save progress
        $this->saveAdvices($request);
        $building->complete($this->step);
        ($this->step);
        $cooperation = Cooperation::find(HoomdossierSession::getCooperation());

        $nextStep = StepHelper::getNextStep($this->step);
        $url = route($nextStep['route'], ['cooperation' => $cooperation]);

        if (! empty($nextStep['tab_id'])) {
            $url .= '#'.$nextStep['tab_id'];
        }

        return redirect($url);
    }
}
