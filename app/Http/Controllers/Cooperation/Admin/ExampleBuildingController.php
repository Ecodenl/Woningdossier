<?php

namespace App\Http\Controllers\Cooperation\Admin;

use App\Helpers\HoomdossierSession;
use App\Helpers\RoleHelper;
use App\Http\Controllers\Controller;
use App\Models\BuildingType;
use App\Models\Cooperation;
use App\Models\ExampleBuilding;
use App\Models\ExampleBuildingContent;
use Illuminate\Database\Eloquent\Relations\Relation;

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
