<?php

namespace App\Livewire\Cooperation\Admin\ExampleBuildings;

use App\Helpers\ExampleBuildingHelper;
use App\Helpers\DataTypes\Caster;
use App\Helpers\HoomdossierSession;
use App\Helpers\RoleHelper;
use App\Models\BuildingType;
use App\Models\Cooperation;
use App\Models\ExampleBuilding;
use App\Models\Step;
use App\Rules\LanguageRequired;
use Livewire\Component;
use Illuminate\Support\Facades\Session;

class Form extends Component
{
    public ?ExampleBuilding $exampleBuilding = null;
    public $buildingTypes;
    public $genericBuildingTypes;
    public $cooperations;
    public $exampleBuildingSteps;
    public $contents = [];
    public $contentStructure;
    public bool $isSuperAdmin = false;

    // Technically its available in the exampleBuildingSteps,
    // but this is an easier and faster way to access the datatype.
    public array $toolQuestionDataType = [];
    // Value types that should be formatted.
    public array $formatTypes = [
        Caster::INT,
        Caster::INT_5,
        Caster::FLOAT,
        Caster::NON_ROUNDING_FLOAT,
    ];

    public array $exampleBuildingValues = [
        'name' => [],
        'building_type_id' => null,
        'cooperation_id' => null,
        'is_default' => 0,
    ];

    protected function rules(): array
    {
        $rules = [
            'exampleBuildingValues.name' => ['required', new LanguageRequired()],
            'exampleBuildingValues.building_type_id' => 'required|exists:building_types,id',
            'exampleBuildingValues.is_default' => 'required|boolean',
            'exampleBuildingValues.order' => 'nullable|numeric|min:0',
            'contents.new.build_year' => 'nullable|numeric|min:1800'
        ];

        if ($this->isSuperAdmin) {
            $rules['exampleBuildingValues.cooperation_id'] = 'nullable|exists:cooperations,id';
        }

        return $rules;
    }

    public function mount(ExampleBuilding $exampleBuilding = null)
    {
        $this->isSuperAdmin = HoomdossierSession::currentRole() === RoleHelper::ROLE_SUPER_ADMIN;
        $this->buildingTypes = BuildingType::all();

        if ($this->isSuperAdmin) {
            $alreadyPickedBuildingTypes = ExampleBuilding::generic()
                ->groupBy('building_type_id')
                ->select('building_type_id')
                ->pluck('building_type_id');
            $this->genericBuildingTypes = BuildingType::whereNotIn('id', $alreadyPickedBuildingTypes->toArray())->get();

            $this->buildingTypes = BuildingType::all();
            $this->cooperations = Cooperation::all();
        }

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

        // By type-hinting it as model in the mount, it is auto-created as empty model. We instead check if it
        // exists.
        if ($exampleBuilding->exists) {
            $this->exampleBuildingValues = $exampleBuilding->attributesToArray();
            foreach ($exampleBuilding->contents as $exampleBuildingContent) {
                $content = array_merge($this->contentStructure, $exampleBuildingContent->content);
                // make sure it has all the available tool questions
                foreach ($content as $toolQuestionShort => $value) {
                    if (array_key_exists($toolQuestionShort, $this->toolQuestionDataType)) {
                        if (in_array($this->toolQuestionDataType[$toolQuestionShort], $this->formatTypes)) {
                            $content[$toolQuestionShort] = Caster::init()
                                ->dataType($this->toolQuestionDataType[$toolQuestionShort])
                                ->value($value)
                                ->getFormatForUser();
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
        // Hydrating as fast as possible again because if the save request returns a 200ok before an actual redirect
        // happens it would then mess up the view due to missing relations...
        $this->hydrateExampleBuildingSteps();

        $this->validate();

        if (! is_numeric($this->exampleBuildingValues['order'] ?? null)) {
            // Empty string isn't allowed
            $this->exampleBuildingValues['order'] = null;
        }

        if ($this->isSuperAdmin) {
            // If the super-admin wants to create a application wide example building
            // he keep the input empty
            if (empty($this->exampleBuildingValues['cooperation_id'])) {
                $this->exampleBuildingValues['cooperation_id'] = null;
            }
        } else {
            // non super-admin, so the example building will always be related to the users it cooperation
            // we alter the values for easy saving.
            $this->exampleBuildingValues['cooperation_id'] = HoomdossierSession::getCooperation();
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

            // Note: dotting and undotting won't work,
            // because it will give the array keys, and wire:model is too dumb to understand that.
            foreach ($content as $toolQuestionShort => $value) {
                if (in_array($toolQuestionShort, ExampleBuildingHelper::UNANSWERABLE_TOOL_QUESTIONS)) {
                    unset($content[$toolQuestionShort]);
                } else {
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

                    // Cast the value if needed
                    if (in_array($this->toolQuestionDataType[$toolQuestionShort], $this->formatTypes)) {
                        $content[$toolQuestionShort] = Caster::init()
                            ->dataType($this->toolQuestionDataType[$toolQuestionShort])
                            ->value($value)
                            ->reverseFormatted();
                    }
                }
            }

            if ($buildYear !== "new") {
                // While we redirect below, it seems Livewire still makes a request to the frontend. We set
                // the new content, else it throws an undefined index exception.
                $this->contents[$buildYear] = $content;
                $this->exampleBuilding->contents()->updateOrCreate(['build_year' => $buildYear], ['content' => $content]);
            }
        }

        return redirect()
            ->route('cooperation.admin.example-buildings.edit', ['cooperation' => HoomdossierSession::getCooperation(true), 'exampleBuilding' => $this->exampleBuilding])
            ->with('success', __('cooperation/admin/example-buildings.update.success'));
    }
}
