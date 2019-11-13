<?php

namespace App\Http\Controllers\Cooperation\Admin;

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

        return view('cooperation.admin.example-buildings.index', compact('exampleBuildings'));
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

        $contentStructure = $this->filterOutUserInterestQuestions(ToolHelper::getContentStructure());

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
        if (!is_null($cooperation)) {
            $exampleBuilding->cooperation()->associate($cooperation);
        }
        $exampleBuilding->is_default = $request->get('is_default', false);
        $exampleBuilding->order = $request->get('order', null);
        $exampleBuilding->save();

        $contents = $request->input('content', []);

        foreach ($contents as $cid => $data) {
            $data['content'] = array_key_exists('content', $data) ? $this->array_undot($data['content']) : [];
            if (!is_numeric($cid) && 'new' == $cid) {
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

        return redirect()->route('cooperation.admin.example-buildings.edit', ['id' => $exampleBuilding])->with('success', 'This example building was added');
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
            }])->findOrFail($id);
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
     * We only want the applicable inputs for the example building
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
        $ventilation = Service::where('short', 'house-ventilation')->first();
        $filterOutUserInterests = function ($key) {
            return stristr($key, 'user_interests') === false;
        };

        foreach (Arr::except($contentStructure, 'general-data') as $stepShort => $structureWithinStep) {
            $contentStructure[$stepShort]['-'] = array_filter($structureWithinStep['-'], $filterOutUserInterests, ARRAY_FILTER_USE_KEY);
        }
        unset(
            $contentStructure['general-data']['building-characteristics']['building_features.building_type_id'],
            $contentStructure['general-data']['building-characteristics']['building_features.build_year'],
            // not requested feature with dropdowns.
            $contentStructure['general-data']['current-state']['service.'.$ventilation->id.'.extra.heat_recovery'],
            $contentStructure['general-data']['current-state']['service.'.$ventilation->id.'.extra.demand_driven']
        );

        // filter out interest stuff from the interest page
        $contentStructure['general-data']['interest'] = array_filter($contentStructure['general-data']['interest'], function ($key) {
            return stristr($key, 'user_interest') === false;
        }, ARRAY_FILTER_USE_KEY);

        return $contentStructure;
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
     * @param int $id
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
            if (isset($translations[$locale]) && !empty($translations[$locale])) {
                $exampleBuilding->updateTranslation('name', $translations[$locale], $locale);
            }
        }

        $exampleBuilding->buildingType()->associate($buildingType);
        if (!is_null($cooperation)) {
            $exampleBuilding->cooperation()->associate($cooperation);
        }
        $exampleBuilding->is_default = $request->get('is_default', false);
        $exampleBuilding->order = $request->get('order', null);

        $contents = $request->input('content', []);

        foreach ($contents as $cid => $data) {
            $data['content'] = array_key_exists('content', $data) ? $this->array_undot($data['content']) : [];

            $content = null;
            if (!is_numeric($cid) && 'new' == $cid) {
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

        return redirect()->route('cooperation.admin.example-buildings.edit', ['id' => $id])->with('success', 'Example building updated');
    }

    protected function array_undot($content)
    {
        $array = [];
        foreach ($content as $step => $values) {
            foreach ($values as $subStep => $subStepValues) {
                foreach ($subStepValues as $tableColumn => $value) {
                    array_set($array, $step.'.'.$subStep.'.'.$tableColumn, $value);
                }
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

        return redirect()->route('cooperation.admin.example-buildings.index')->with('success', 'Example building deleted');
    }

    /**
     * Copies over a specific example building configuration (content / structure).
     *
     * @param Cooperation $cooperation
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
            $names[$translation->language] = $translation->translation . ' (copy)';
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
}
