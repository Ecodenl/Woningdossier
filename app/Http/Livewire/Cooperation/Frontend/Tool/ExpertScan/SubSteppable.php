<?php

namespace App\Http\Livewire\Cooperation\Frontend\Tool\ExpertScan;

use App\Helpers\Conditions\ConditionEvaluator;
use App\Helpers\DataTypes\Caster;
use App\Helpers\HoomdossierSession;
use App\Http\Livewire\Cooperation\Frontend\Tool\Scannable;
use App\Models\InputSource;
use App\Models\Step;
use App\Models\SubStep;
use App\Models\ToolQuestion;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class SubSteppable extends Scannable
{
    public Step $step;
    public SubStep $subStep;

    public array $calculationResults = [];

    public array $intercontinentalAnswers = [];

    public bool $loading = false;
    public bool $componentReady = false;

    protected $listeners = [
        'calculationsPerformed',
        'updateFilledInAnswers',
        'save',
        'inputUpdated',
    ];

    public function mount(Step $step, SubStep $subStep)
    {
        $subStep->load([
            'subSteppables' => function ($query) {
                $query
                    ->orderBy('order')
                    ->with(['subSteppable', 'toolQuestionType']);
            },
            'toolQuestions' => function ($query) {
                $query->orderBy('order')->with('forSpecificInputSource');
            },
            'subStepTemplate',
        ]);

        $this->build();
    }

    public function build()
    {
        $this->building = HoomdossierSession::getBuilding(true);
        $this->masterInputSource = InputSource::findByShort(InputSource::MASTER_SHORT);
        $this->currentInputSource = HoomdossierSession::getInputSource(true);
        $this->residentInputSource = InputSource::findByShort(InputSource::RESIDENT_SHORT);
        $this->coachInputSource = InputSource::findByShort(InputSource::COACH_SHORT);

        // first we have to hydrate the tool questions
        $this->hydrateToolQuestions();
        // after that we can fill up the user his given answers

        $this->setFilledInAnswers();

        $this->originalAnswers = $this->filledInAnswers;
    }

    public function render()
    {
        if ($this->componentReady) {
            $this->rehydrateToolQuestions();
        }
        if ($this->loading) {
            $this->dispatchBrowserEvent('input-updated');
        }
        return view('livewire.cooperation.frontend.tool.expert-scan.sub-steppable');
    }

    public function init()
    {
        // Emits don't work before the first render of a component is processed. Therefore, we only emit after the first
        // load (also known as the init or initialization). We need to pass the answers to the main component so it
        // can perform calculations
        $this->emit('updateFilledInAnswers', $this->filledInAnswers, $this->id);
        $this->dispatchBrowserEvent('component-ready', ['id' => $this->id]);
        $this->componentReady = true;
    }

    public function inputUpdated()
    {
        $this->loading = true;
    }

    public function hydrateToolQuestions()
    {
        $this->toolQuestions = $this->subStep->toolQuestions;
    }

    public function rehydrateToolQuestions()
    {
        $this->toolQuestions = $this->subStep->toolQuestions;

        $this->setValidationForToolQuestions();
        $this->evaluateToolQuestions();
    }

    public function updated($field, $value)
    {
        if (Str::contains($field, 'filledInAnswers')) {
            $toolQuestionShort = Str::replaceFirst('filledInAnswers.', '', $field);
            $toolQuestion = ToolQuestion::findByShort($toolQuestionShort);
            if ($toolQuestion instanceof ToolQuestion) {
                // If it's an INT, we want to ensure the value set is also an INT
                if ($toolQuestion->data_type === Caster::INT) {
                    $value = Caster::init(Caster::INT, Caster::init(Caster::INT, $value)->reverseFormatted())->getFormatForUser();
                    $this->filledInAnswers[$toolQuestionShort] = $value;
                }
            }
        }

        $this->refreshAlerts();

        $this->setDirty(true);

        $this->emit('updateFilledInAnswers', $this->filledInAnswers, $this->id);
    }

    public function calculationsPerformed($calculationResults)
    {
        $this->calculationResults = $calculationResults;
        $this->dispatchBrowserEvent('input-update-processed');
        $this->loading = false;
    }

    protected function evaluateToolQuestions()
    {
        $evaluator = ConditionEvaluator::init()
            ->building($this->building)
            ->inputSource($this->masterInputSource);

        // First fetch all conditions, so we can retrieve any required related answers in one go
        $conditionsForAllSubSteppables = [];
        foreach (array_filter($this->subStep->subSteppables->pluck('conditions')->all()) as $condition) {
            $conditionsForAllSubSteppables = array_merge($conditionsForAllSubSteppables, $condition);
        }
        $answersForAllSubSteppables = $evaluator->getToolAnswersForConditions($conditionsForAllSubSteppables,
            collect($this->filledInAnswers)->merge(collect($this->intercontinentalAnswers)));

        foreach ($this->subStep->subSteppables as $index => $subSteppablePivot) {
            $toolQuestion = $subSteppablePivot->subSteppable;

            if (! empty($subSteppablePivot->conditions)) {
                $conditions = $subSteppablePivot->conditions;

                if (! $evaluator->evaluateCollection($conditions, $answersForAllSubSteppables)) {
                    $this->subStep->subSteppables = $this->subStep->subSteppables->forget($index);

                    // We will unset the answers the user has given. If the user then changes their mind, they
                    // will have to fill in the data again. We don't want to save values to the database
                    // that are unvalidated (or not relevant).

                    // Normally we'd use $this->reset(), but it doesn't seem like it likes nested items per dot

                    // we will only unset the rules if its a tool question, not relevant for other sub steppables.
                    if ($subSteppablePivot->isToolQuestion()) {
                        $this->filledInAnswers[$toolQuestion->short] = null;

                        // and unset the validation for the question based on type.
                        switch ($toolQuestion->data_type) {
                            case Caster::JSON:
                                foreach ($toolQuestion->options as $option) {
                                    unset($this->rules["filledInAnswers.{$toolQuestion->short}.{$option['short']}"]);
                                }
                                break;

                            case Caster::ARRAY:
                                unset($this->rules["filledInAnswers.{$toolQuestion->short}"]);
                                unset($this->rules["filledInAnswers.{$toolQuestion->short}.*"]);
                                break;

                            default:
                                unset($this->rules["filledInAnswers.{$toolQuestion->short}"]);
                                break;
                        }
                    }
                }
            }
        }
    }

    public function updateFilledInAnswers(array $filledInAnswers, string $id)
    {
        if ($id !== $this->id) {
            foreach ($filledInAnswers as $toolQuestionShort => $answer) {
                $this->intercontinentalAnswers[$toolQuestionShort] = $answer;
            }
        }
    }

    public function save()
    {
        // Before we can validate (and save), we must reset the formatting from text to mathable
        foreach ($this->toolQuestions as $toolQuestion) {
            if ($toolQuestion->data_type === Caster::FLOAT) {
                $this->filledInAnswers[$toolQuestion->short] = Caster::init(
                    $toolQuestion->data_type, $this->filledInAnswers[$toolQuestion->short]
                )->reverseFormatted();
            }
        }

        if (! empty($this->rules)) {
            $validator = Validator::make([
                'filledInAnswers' => $this->filledInAnswers
            ], $this->rules, [], $this->attributes);

            // Translate values also
            $defaultValues = __('validation.values.defaults');

            foreach ($this->filledInAnswers as $toolQuestionId => $answer) {
                $validator->addCustomValues([
                    "filledInAnswers.{$toolQuestionId}" => $defaultValues,
                ]);
            }

            Log::debug("Sub step {$this->subStep->name} " . ($validator->fails() ? 'fails validation' : 'passes validation'));
            foreach ($this->toolQuestions as $toolQuestion) {
                if (in_array($toolQuestion->data_type, [Caster::INT, Caster::FLOAT])) {
                    $this->filledInAnswers[$toolQuestion->short] = Caster::init(
                        $toolQuestion->data_type, $this->filledInAnswers[$toolQuestion->short]
                    )->getFormatForUser();
                }
            }
            if ($validator->fails()) {
                // Validator failed, let's put it back as the user format
                // notify the main form that validation failed for this particular sub step.
                $this->emitUp('failedValidationForSubSteps', $this->subStep);

                $this->dispatchBrowserEvent('validation-failed');
            }

            $validator->validate();
        }

        // Turns out, default values exist! We need to check if the tool questions have answers, else
        // they might not save...
        if (! $this->dirty) {
            foreach ($this->filledInAnswers as $toolQuestionShort => $givenAnswer) {
                $toolQuestion = ToolQuestion::findByShort($toolQuestionShort);

                // Define if we should check this question...
                if ($this->building->user->account->can('answer', $toolQuestion)) {
                    $currentAnswer = $this->building->getAnswer($toolQuestion->forSpecificInputSource ?? $this->currentInputSource, $toolQuestion);
                    $masterAnswer = $this->building->getAnswer($this->masterInputSource, $toolQuestion);

                    // Master input source is important. Ensure both are set
                    if (is_null($currentAnswer) || is_null($masterAnswer)) {
                        $this->setDirty(true);
                        break;
                    }
                }
            }
        }

        $answers = [];

        // if it's not dirty, we don't want to pass the answers, as we don't need to update them.
        if ($this->dirty) {
            $answers = $this->filledInAnswers;
        }

        $this->emitUp('subStepValidationSucceeded', $this->subStep, $answers);
    }

    protected function dehydrateProperty($property, $value)
    {
        // Wow, dehydration custom logic? Why, you ask? Well, Livewire uses Laravel serialization under the hood
        // to make PHP properties available for JS, since it gets serialized to JSON, which works for JS as well.
        // However, any eager loaded relation will be handled accordingly when serialized and unserialized. When a
        // collection comes along, it always grabs the _first_ relation. In the case that this collection alters, we
        // might see that something that has worked all along suddenly is causing issues. In our current case, a
        // tool label might be removed, and instead a tool question is shown. Then, it will try and eager load
        // forSpecificInputSource on all subSteppable morphs related to the subStep. This causes an issue because
        // tool labels don't have this relation. Therefore, we unset the relation so this issue can never be caused
        // ever again. Note for the future: ALWAYS unset relations that don't exist on all models that are eager loaded.
        if ($property == 'subStep') {
            if ($value->relationLoaded('subSteppables')) {
                foreach ($value->subSteppables as $subSteppablePivot) {
                    if ($subSteppablePivot->relationLoaded('subSteppable')) {
                        $subSteppablePivot->subSteppable->unsetRelation('forSpecificInputSource');
                    }
                }
            }
        }

        return $value;
    }
}