<?php

namespace App\Http\Livewire\Cooperation\Admin\ExampleBuildings;

use App\Helpers\ExampleBuildingHelper;
use App\Helpers\HoomdossierSession;
use App\Helpers\QuestionAnswers\BuildingTypeCategory;
use App\Models\BuildingType;
use App\Models\Cooperation;
use App\Models\ExampleBuilding;
use App\Models\Step;
use App\Models\ToolQuestion;
use Livewire\Component;
use Illuminate\Support\Facades\Session;

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

    // Tool questions which should not be allowed to be set by the example building
    public $hideTheseToolQuestions = ExampleBuildingHelper::UNANSWERABLE_TOOL_QUESTIONS;

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
            'floor-insulation',
            'roof-insulation',
            'solar-panels',
            'heating',
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

            // note: dotting and undotting wont work
            // will give the array keys, and wire:model is to dumb to understand that.
            foreach ($content as $toolQuestionShort => $value) {

                if (is_array($value)) {
                    // multiselects
                    // we dont need and dont WANT the keys
                    // just the values, filter out null and only set the values.
                    $value = array_filter($value, fn($value) => $value !== "null");
                    $value = array_values($value);
                    $content[$toolQuestionShort] = $value;
                }
                if ($value === null || $value === "null") {
                    unset($content[$toolQuestionShort]);
                }
            }

            if ($buildYear !== "new") {
                $this->exampleBuilding->contents()->updateOrCreate(['build_year' => $buildYear], ['content' => $content]);
            }
        }

        // normally we could use with, however this is livewire 1 and no support
        // we do it the "old" way
        Session::flash('success', __('cooperation/admin/example-buildings.update.success'));
        return redirect()
            ->to(route('cooperation.admin.example-buildings.edit', ['cooperation' => HoomdossierSession::getCooperation(true), 'exampleBuilding' => $this->exampleBuilding]));
    }

    public function render()
    {
        return view('livewire.cooperation.admin.example-buildings.form');
    }
}
