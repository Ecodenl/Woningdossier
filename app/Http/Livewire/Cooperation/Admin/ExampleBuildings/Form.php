<?php

namespace App\Http\Livewire\Cooperation\Admin\ExampleBuildings;

use App\Helpers\ExampleBuildingHelper;
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

    public function addContentYear($year)
    {
        $this->contents[$year] = [];
    }

    public function mount(ExampleBuilding $exampleBuilding)
    {
        $this->exampleBuilding = $exampleBuilding;
        $this->buildingTypes = BuildingType::all();
        $this->cooperations = Cooperation::all();
        $this->exampleBuildingValues = $exampleBuilding->attributesToArray();

        $this->contentStructure = [];

        $this->exampleBuildingSteps = Step::whereIn('short', [
            'building-data', 'usage-quick-scan', 'living-requirements', 'residential-status',
            'ventilation', 'wall-insulation', 'insulated-glazing'
        ])
            ->with(['subSteps.subSteppables' => function ($query) {
                $query->where('sub_steppable_type', ToolQuestion::class);
            }])
            ->get();


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

    }

    public function save()
    {
        // previously something along the line of this was done
        // $data['content'] = ExampleBuildingHelper::formatContent($data['content']);
        // however maybe we will take a diff approach
        foreach ($this->contents as $buildYear => $content) {
            $this->exampleBuilding->contents()->updateOrCreate(['build_year' => $buildYear], $content);
        }
    }

    public function render()
    {
        return view('livewire.cooperation.admin.example-buildings.form');
    }
}
