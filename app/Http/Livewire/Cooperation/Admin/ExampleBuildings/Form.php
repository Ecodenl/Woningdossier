<?php

namespace App\Http\Livewire\Cooperation\Admin\ExampleBuildings;

use App\Helpers\ExampleBuildingHelper;
use App\Helpers\HoomdossierSession;
use App\Models\BuildingType;
use App\Models\Cooperation;
use App\Models\ExampleBuilding;
use App\Models\Log;
use App\Models\Step;
use App\Models\ToolQuestion;
use Livewire\Component;

class Form extends Component
{
    public $exampleBuilding = null;
    public $buildingTypes;
    public $cooperations;
    public $exampleBuildingSteps;
    public $contents = [];
    public $contentStructure;

    public $exampleBuildingValues = [
        'name' => [],
        'building_type_id' => null,
        'cooperation_id' => null,
        'is_default' => 0,
    ];

    // tool questions which should be allowed to be set, for whatever reason..
    public $hideTheseToolQuestions = [
        'building-type',
        'build-year',
        'specific-example-building',
        'building-data-comment-resident',
        'building-data-comment-coach',
        'usage-quick-scan-comment-resident',
        'usage-quick-scan-comment-coach',
        'living-requirements-comment-resident',
        'living-requirements-comment-coach',
        'residential-status-element-comment-resident',
        'residential-status-element-comment-coach',
        'residential-status-service-comment-resident',
        'residential-status-service-comment-coach',
    ];

    public function mount(ExampleBuilding $exampleBuilding = null)
    {
        $this->exampleBuilding = $exampleBuilding;
        $this->buildingTypes = BuildingType::all();
        $this->cooperations = Cooperation::all();

        $this->contentStructure = [];

        $this->hydrateExampleBuildingSteps();

        foreach ($this->exampleBuildingSteps as $step) {
            foreach ($step->subSteps as $subStep) {
                foreach ($subStep->subSteppables as $subSteppablePivot) {
                    $this->contentStructure[$subSteppablePivot->subSteppable->short] = null;
                }
            }
        }

        if ($exampleBuilding instanceof ExampleBuilding) {
            $this->exampleBuildingValues = $exampleBuilding->attributesToArray();
            foreach ($exampleBuilding->contents as $content) {
                // make sure it has all the available tool questions
                $this->contents[$content->build_year] = array_merge($this->contentStructure, $content->content);
            }
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

    public function updated($key, $value)
    {
        $this->hydrateExampleBuildingSteps();
        if ($key === "exampleBuildingValues.building_type_id") {
            data_set($this->contents, '*.building-type-category', $value);
        }
    }

    public function save()
    {
        // hydrating as fast as possible again because if the save request returns a 200ok before a actual redirect happens
        // which would then mess up the view due to missing relations..
        $this->hydrateExampleBuildingSteps();

        $this->validate([
            'exampleBuildingValues.building_type_id' => 'required|exists:building_types,id',
            'exampleBuildingValues.cooperation_id' => 'nullable|exists:cooperations,id',
            'exampleBuildingValues.is_default' => 'required|boolean',
            'exampleBuildingValues.order' => 'nullable|numeric|min:0',
            'contents.new.build_year' => 'nullable|numeric|min:0'
        ]);
        // update or create
        if ($this->exampleBuilding instanceof ExampleBuilding) {
            $this->exampleBuilding->update($this->exampleBuildingValues);
        } else {
            $this->exampleBuilding = ExampleBuilding::create($this->exampleBuildingValues);
        }
        // previously something along the line of this was done
        // $data['content'] = ExampleBuildingHelper::formatContent($data['content']);
        // however maybe we will take a diff approach
        foreach ($this->contents as $buildYear => $content) {
            // the build year will be empty (as a key) when its a newly added one
            // in that case the build year will be manually added in the form.
            if (($buildYear === "new" && isset($content['build_year'])) && !empty($content['build_year'])) {
                $buildYear = $content['build_year'];
                // unset it as field name.
                unset($content['build_year']);
                // set it as a tool question short so the apply method picks it up correctly
                // plus to stay consistent
                $content['build-year'] = $buildYear;
            }

            if ($buildYear !== "new") {
                $this->exampleBuilding->contents()->updateOrCreate(['build_year' => $buildYear], ['content' => $content]);
            }
        }

        // normally we could use with, however this is livewire 1 and no support
        // we do it the "old" way
        \Session::flash('success', __('cooperation/admin/example-buildings.update.success'));
        return redirect()
            ->to(route('cooperation.admin.example-buildings.edit', ['cooperation' => HoomdossierSession::getCooperation(true), 'exampleBuilding' => $this->exampleBuilding]));
    }

    public function render()
    {
        return view('livewire.cooperation.admin.example-buildings.form');
    }
}
