<?php

namespace App\Http\Controllers\Cooperation\Admin;

use App\Helpers\ExampleBuildingHelper;
use App\Helpers\HoomdossierSession;
use App\Helpers\ToolHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Cooperation\Admin\ExampleBuildingRequest;
use App\Models\Building;
use App\Models\BuildingFeature;
use App\Models\BuildingType;
use App\Models\Cooperation;
use App\Models\ExampleBuilding;
use App\Models\ExampleBuildingContent;
use App\Models\InputSource;
use App\Services\ExampleBuildingService;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Arr;

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

        return view('cooperation.admin.example-buildings.index', compact('exampleBuildings'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create(Cooperation $cooperation)
    {
        $buildingTypes = BuildingType::all();

        $cooperations = collect()->push($cooperation);
        if ('super-admin' === HoomdossierSession::getRole(true)->short) {
            $cooperations = Cooperation::all();
        }

        $contentStructure = $this->onlyApplicableInputs(ToolHelper::getContentStructure());

        return view('cooperation.admin.example-buildings.create',
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
    public function store(ExampleBuildingRequest $request)
    {
        $buildingType = BuildingType::findOrFail($request->get('building_type_id'));
        $cooperation = Cooperation::find($request->get('cooperation_id'));

        $exampleBuilding = new ExampleBuilding();

        $translations = $request->input('name', []);
        $translations = array_only($translations, config('hoomdossier.supported_locales'));
        $exampleBuilding->createTranslations('name', $translations);

        $exampleBuilding->buildingType()->associate($buildingType);
        if (! is_null($cooperation)) {
            $exampleBuilding->cooperation()->associate($cooperation);
        }
        $exampleBuilding->is_default = $request->get('is_default', false);
        $exampleBuilding->order = $request->get('order', null);
        $exampleBuilding->save();

        $this->updateOrCreateContent($exampleBuilding, $request->get('new', 0), $request->input('content', []));

        return redirect()->route('cooperation.admin.example-buildings.edit', ['id' => $exampleBuilding])->with('success', __('cooperation/admin/example-buildings.store.success'));
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
        $exampleBuilding = ExampleBuilding::with([
            'contents' => function (Relation $query) {
                $query->orderBy('build_year');
            }, ])->findOrFail($id);
        $buildingTypes = BuildingType::all();
        $cooperations = Cooperation::all();

        $contentStructure = $this->onlyApplicableInputs(ToolHelper::getContentStructure());

        return view('cooperation.admin.example-buildings.edit',
            compact(
                'exampleBuilding', 'buildingTypes', 'cooperations', 'contentStructure'
            )
        );
    }

    /**
     * We only want the applicable inputs for the example building.
     *
     * NO element or service questions will be shown when already displayed in the general data page
     * NO user interest questions throughout the steps
     *
     * @param $contentStructure
     *
     * @return array
     */
    private function onlyApplicableInputs($contentStructure)
    {
        $filterOutUserInterests = function ($key) {
            return false === stristr($key, 'user_interests');
        };

        foreach (Arr::except($contentStructure, 'general-data') as $stepShort => $structureWithinStep) {
            $contentStructure[$stepShort]['-'] = array_filter($structureWithinStep['-'], $filterOutUserInterests, ARRAY_FILTER_USE_KEY);
        }

        unset(
            $contentStructure['general-data']['building-characteristics']['building_features.building_type_id'],
            $contentStructure['general-data']['building-characteristics']['building_features.build_year'],
            $contentStructure['general-data']['usage']['user_energy_habits.resident_count'],

            $contentStructure['high-efficiency-boiler']['-']['user_energy_habits.amount_gas'],
            $contentStructure['high-efficiency-boiler']['-']['user_energy_habits.amount_electricity'],
            $contentStructure['solar-panels']['-']['user_energy_habits.amount_electricity'],
            $contentStructure['high-efficiency-boiler']['-']['user_energy_habits.resident_count']
        );

        // filter out interest stuff from the interest page
        $contentStructure['general-data']['interest'] = array_filter($contentStructure['general-data']['interest'], function ($key) {
            return false === stristr($key, 'user_interest');
        }, ARRAY_FILTER_USE_KEY);

        return $contentStructure;
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

        $buildingType = BuildingType::findOrFail($request->get('building_type_id'));
        $cooperation = Cooperation::find($request->get('cooperation_id'));

        $translations = $request->input('name', []);
        foreach (config('hoomdossier.supported_locales') as $locale) {
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

        $this->updateOrCreateContent($exampleBuilding, $request->get('new', 0), $request->input('content', []));

        $exampleBuilding->save();

        $this->reApplyExampleBuilding($exampleBuilding);

        return redirect()->route('cooperation.admin.example-buildings.edit', ['id' => $id])->with('success', __('cooperation/admin/example-buildings.update.success'));
    }

    private function updateOrCreateContent(ExampleBuilding $exampleBuilding, $new, $contents)
    {
        foreach ($contents as $cid => $data) {
            if (! is_null($data['build_year'])) {
                $data['content'] = array_key_exists('content', $data) ? $data['content'] : [];

                $data['content'] = ExampleBuildingHelper::formatContent($data['content']);

                $content = null;
                if (! is_numeric($cid) && 'new' == $cid) {
                    if (1 == $new) {
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
        }
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

        return redirect()->route('cooperation.admin.example-buildings.index')->with('success', 'Example building deleted');
    }

    /**
     * Copies over a specific example building configuration (content / structure).
     *
     * @param int $id
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

        return redirect()->route('cooperation.admin.example-buildings.index')->with('success', 'Example building copied');
    }

    protected function reApplyExampleBuilding(ExampleBuilding $exampleBuilding)
    {
        $buildings = Building::where('example_building_id', $exampleBuilding->id)
            ->with(['buildingFeatures' => function($query) {
                $query->allInputSources();
            }])
            ->get();

        foreach($buildings as $building)
        {
            if ($building instanceof Building && $building->buildingFeatures instanceof BuildingFeature) {
                ExampleBuildingService::apply($exampleBuilding, $building->buildingFeatures->build_year, $building);
            }
        }
    }
}
