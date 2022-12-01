<?php

namespace App\Http\Livewire\Cooperation\Admin\ExampleBuildings;

use App\Helpers\ExampleBuildingHelper;
use App\Helpers\DataTypes\Caster;
use App\Helpers\HoomdossierSession;
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

    // technically its available in the exampleBuildingSteps
    // But this is a easy and faster way to access the datatype.
    public $toolQuestionDataType = [];

    public $exampleBuildingValues = [
        'name' => [],
        'building_type_id' => null,
        'cooperation_id' => null,
        'is_default' => 0,
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
                foreach ($subStep->toolQuestions as $toolQuestion) {
                    // create the default structure
                    $this->contentStructure[$toolQuestion->short] = null;
                    // save the data type in a easy to access structure
                    $this->toolQuestionDataType[$toolQuestion->short] = $toolQuestion->data_type;
                }
            }
        }

        if ($exampleBuilding instanceof ExampleBuilding) {
            $this->exampleBuildingValues = $exampleBuilding->attributesToArray();
            foreach ($exampleBuilding->contents as $exampleBuildingContent) {

                $content = array_merge($this->contentStructure, $exampleBuildingContent->content);
                // make sure it has all the available tool questions
                foreach ($content as $toolQuestionShort => $value) {
                    if (array_key_exists($toolQuestionShort, $this->toolQuestionDataType)) {
                        if ($this->toolQuestionDataType[$toolQuestionShort] === \App\Helpers\DataTypes\Caster::FLOAT) {
                            $content[$toolQuestionShort] = Caster::init(Caster::FLOAT, $value)->getFormatForUser();
                        }
                    } else {
                        // If it's not found it means it should not be set
                        unset($content[$toolQuestionShort]);
                    }
                }
                $this->contents[$exampleBuildingContent->build_year] = $content;
            }
        }

        $this->contents['new'] = $this->contentStructure;
    }

    public function render()
    {
        return view('livewire.cooperation.admin.example-buildings.form');
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
            ->with(['subSteps.toolQuestions' => function ($query) {
                $query->whereNotIn('tool_questions.short', ExampleBuildingHelper::UNANSWERABLE_TOOL_QUESTIONS);
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
        if (empty($this->exampleBuildingValues['cooperation_id'])) {
            $this->exampleBuildingValues['cooperation_id'] = null;
        }
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

                // cast the value to a database value (a int)
                if ($this->toolQuestionDataType[$toolQuestionShort] === Caster::FLOAT) {
                    $content[$toolQuestionShort] = Caster::init(Caster::FLOAT, $value)->reverseFormatted();
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
}
