<?php

namespace App\Http\Controllers\Cooperation\Admin;

use App\Helpers\ExampleBuildingHelper;
use App\Helpers\HoomdossierSession;
use App\Helpers\ToolHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Cooperation\Admin\ExampleBuildingRequest;
use App\Models\BuildingType;
use App\Models\Cooperation;
use App\Models\ExampleBuilding;
use App\Models\ExampleBuildingContent;
use App\Models\Service;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

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
        $translations = Arr::only($translations, config('hoomdossier.supported_locales'));
        $exampleBuilding->name = $translations;

        $exampleBuilding->buildingType()->associate($buildingType);
        if (! is_null($cooperation)) {
            $exampleBuilding->cooperation()->associate($cooperation);
        }
        $exampleBuilding->is_default = $request->get('is_default', false);
        $exampleBuilding->order = $request->get('order', null);
        $exampleBuilding->save();

        $this->updateOrCreateContent($exampleBuilding, $request->get('new', 0), $request->input('content', []));

        return redirect()->route('cooperation.admin.example-buildings.edit', compact('exampleBuilding'))->with('success', __('cooperation/admin/example-buildings.store.success'));
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
    public function edit(Cooperation $cooperation, $exampleBuilding)
    {
        /** @var ExampleBuilding $exampleBuilding */
        $exampleBuilding = ExampleBuilding::with([
            'contents' => function (Relation $query) {
                $query->orderBy('build_year');
            }, ])->findOrFail($exampleBuilding);
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

        foreach (Arr::except($contentStructure, ['general-data', 'insulated-glazing', 'ventilation',]) as $stepShort => $structureWithinStep) {
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

        // Remove total sun panels, we require the pv panels instead
        $totalSolarPanels = Service::findByShort('total-sun-panels');
        unset($contentStructure['general-data']['current-state']["service.{$totalSolarPanels->id}.extra.value"]);
        // Remove general data considerables
        foreach (($contentStructure['general-data']['interest'] ?? []) as $interestField => $interestData) {
            if (Str::endsWith($interestField, 'is_considering')) {
                unset($contentStructure['general-data']['interest'][$interestField]);
            }
        }

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
    public function update(ExampleBuildingRequest $request, Cooperation $cooperation, ExampleBuilding $exampleBuilding)
    {
        $buildingType = BuildingType::findOrFail($request->get('building_type_id'));
        $cooperation = Cooperation::find($request->get('cooperation_id'));

        $translations = $request->input('name', []);
        $exampleBuilding->name = $translations;

        $exampleBuilding->buildingType()->associate($buildingType);
        if (! is_null($cooperation)) {
            $exampleBuilding->cooperation()->associate($cooperation);
        }
        $exampleBuilding->is_default = $request->get('is_default', false);
        $exampleBuilding->order = $request->get('order', null);

        $this->updateOrCreateContent($exampleBuilding, $request->get('new', 0), $request->input('content', []));

        $exampleBuilding->save();

        return redirect()->route('cooperation.admin.example-buildings.edit', compact('exampleBuilding'))->with('success', __('cooperation/admin/example-buildings.update.success'));
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
    public function destroy(Cooperation $cooperation, ExampleBuilding $exampleBuilding)
    {
        $exampleBuilding->delete();

        return redirect()->route('cooperation.admin.example-buildings.index')->with('success', 'Example building deleted');
    }

    /**
     * Copies over a specific example building configuration (content / structure).
     *
     * @param int $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function copy(Cooperation $cooperation, ExampleBuilding $exampleBuilding)
    {
        /** @var ExampleBuilding $exampleBuilding */
        $exampleBuildingContents = $exampleBuilding->contents;
        $translations = $exampleBuilding->getTranslations('name');
        $names = [];
        foreach ($translations as $translation) {
            $names[$translation->language] = $translation->translation.' (copy)';
        }

        $newEB = new ExampleBuilding($exampleBuilding->toArray());
        $name = $newEB->name = $names;
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
}
