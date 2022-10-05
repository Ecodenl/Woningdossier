<?php

namespace App\Http\Livewire\Cooperation\Admin\ExampleBuildings;

use App\Helpers\ExampleBuildingHelper;
use App\Helpers\HoomdossierSession;
use App\Models\BuildingType;
use App\Models\Cooperation;
use App\Models\ExampleBuilding;
use App\Models\Step;
use App\Models\ToolQuestion;
use Livewire\Component;

class Form extends Component
{
    public $exampleBuilding;
    public $buildingTypes;
    public $cooperations;
    public $exampleBuildingSteps;
    public $contents = [];
    public $contentStructure;

    public $exampleBuildingValues = [
        'name' => [],
        'building_type_id' => null,
        'cooperation_id' => null
    ];

    public function mount(ExampleBuilding $exampleBuilding)
    {
        $this->exampleBuilding = $exampleBuilding;
        $this->buildingTypes = BuildingType::all();
        $this->cooperations = Cooperation::all();
        $this->exampleBuildingValues = $exampleBuilding->attributesToArray();

        $this->contentStructure = [];

        $this->hydrateExampleBuildingSteps();

        foreach ($this->exampleBuildingSteps as $step) {
            foreach ($step->subSteps as $subStep) {
                foreach ($subStep->subSteppables as $subSteppablePivot) {
                    $this->contentStructure[$subSteppablePivot->subSteppable->short] = null;
                }
            }
        }

        foreach ($exampleBuilding->contents as $content) {
            // make sure it has all the available tool questions
            $this->contents[$content->build_year] = array_merge($this->contentStructure, $content->content);
        }

        $this->contents['new'] = $this->contentStructure;

    }

    public function hydrateExampleBuildingSteps()
    {
        $this->exampleBuildingSteps = Step::whereIn('short', [
            'building-data',
            'usage-quick-scan',
            'living-requirements',
            'residential-status',
            'ventilation',
            'wall-insulation',
            'insulated-glazing',
            'heating'
        ])
            ->orderBy('order')
            ->with(['subSteps.subSteppables' => function ($query) {
                $query->where('sub_steppable_type', ToolQuestion::class);
            }])
            ->get();

    }

    public function updated()
    {
        $this->hydrateExampleBuildingSteps();
    }

    public function save()
    {
        // previously something along the line of this was done
        // $data['content'] = ExampleBuildingHelper::formatContent($data['content']);
        // however maybe we will take a diff approach
        foreach ($this->contents as $buildYear => $content) {
            // the build year will be empty (as a key) when its a newly added one
            // in that case the build year will be manually added in the form.
            if ($buildYear === "new" && isset($content['build_year'])) {
                $buildYear = $content['build_year'];
            }

            if ($buildYear !== "new") {
                $this->exampleBuilding->contents()->updateOrCreate(['build_year' => $buildYear], ['content' => $content]);
            }
        }

        \Session::flash('success', 'cooperation/admin/example-buildings.update.success');
        return redirect()
            ->to(route('cooperation.admin.example-buildings.edit', ['cooperation' => HoomdossierSession::getCooperation(true), 'exampleBuilding' => $this->exampleBuilding]));
    }

    public function render()
    {
        return view('livewire.cooperation.admin.example-buildings.form');
    }
}
