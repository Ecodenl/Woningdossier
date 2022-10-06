<?php

namespace App\Http\Controllers\Cooperation\Admin;

use App\Helpers\ExampleBuildingHelper;
use App\Helpers\HoomdossierSession;
use App\Helpers\RoleHelper;
use App\Helpers\ToolHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Cooperation\Admin\ExampleBuildingRequest;
use App\Models\BuildingType;
use App\Models\Cooperation;
use App\Models\ExampleBuilding;
use App\Models\ExampleBuildingContent;
use App\Models\Service;
use App\Models\Step;
use App\Models\ToolQuestion;
use App\Services\ContentStructureService;
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
    public function index(Cooperation $cooperation)
    {
        $exampleBuildingsQuery = ExampleBuilding::orderBy('cooperation_id')
            ->orderBy('order');

        if (HoomdossierSession::getRole(true)->name !== RoleHelper::ROLE_SUPER_ADMIN) {
            $exampleBuildingsQuery->forMyCooperation();
        }

        $exampleBuildings = $exampleBuildingsQuery->get();

        return view('cooperation.admin.example-buildings.index', compact('exampleBuildings', 'cooperation'));
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create(Cooperation $cooperation)
    {
        $buildingTypes = BuildingType::all();
        $cooperations = Cooperation::all();

        return view('cooperation.admin.example-buildings.create',
            compact(
                'buildingTypes', 'cooperations')
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
        if (!is_null($cooperation)) {
            $exampleBuilding->cooperation()->associate($cooperation);
        }
        $exampleBuilding->is_default = $request->get('is_default', false);
        $exampleBuilding->order = $request->get('order', null);
        $exampleBuilding->save();


        $this->updateOrCreateContent($exampleBuilding, $request->get('new', 0), $request->input('content', []));

        return redirect()->route('cooperation.admin.example-buildings.edit', compact('exampleBuilding'))->with('success', __('cooperation/admin/example-buildings.store.success'));
    }


    public function edit(Cooperation $cooperation, $exampleBuilding)
    {
        /** @var ExampleBuilding $exampleBuilding */
        $exampleBuilding = ExampleBuilding::with([
            'contents' => function (Relation $query) {
                $query->orderBy('build_year');
            },])->findOrFail($exampleBuilding);
        $buildingTypes = BuildingType::all();
        $cooperations = Cooperation::all();

        return view('cooperation.admin.example-buildings.edit',
            compact(
                'exampleBuilding', 'buildingTypes', 'cooperations')
        );
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @param  $cooperation
     *
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public
    function destroy(Cooperation $cooperation, ExampleBuilding $exampleBuilding)
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
    public
    function copy(Cooperation $cooperation, ExampleBuilding $exampleBuilding)
    {
        /** @var ExampleBuilding $exampleBuilding */
        $exampleBuildingContents = $exampleBuilding->contents;
        $translations = $exampleBuilding->getTranslations('name');
        $names = [];
        foreach ($translations as $locale => $translation) {
            $names[$locale] = $translation . ' (copy)';
        }

        $newEB = new ExampleBuilding($exampleBuilding->toArray());
        $newEB->name = $names;
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
