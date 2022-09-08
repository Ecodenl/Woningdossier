<?php

namespace App\Http\Livewire\Cooperation\Frontend\Tool\ExpertScan;

use App\Helpers\Conditions\ConditionEvaluator;
use App\Helpers\DataTypes\Caster;
use App\Helpers\HoomdossierSession;
use App\Helpers\NumberFormatter;
use App\Http\Livewire\Cooperation\Frontend\Tool\Scannable;
use App\Models\InputSource;
use App\Models\Step;
use App\Models\SubStep;
use App\Models\ToolQuestion;
use Illuminate\Support\Facades\Validator;

class SubSteppable extends Scannable
{
    public $step;
    public $subStep;
    public $nextUrl;

    public function mount(Step $step, SubStep $subStep)
    {
        $this->step = $step;

        $subStep->load([
            'subSteppables' => function ($query) {
                $query
                    ->orderBy('order')
                    ->with(['subSteppable', 'toolQuestionType']);
            },
            'toolQuestions' => function ($query) {
                $query->orderBy('order');
            },
            'subStepTemplate',
        ]);

        $this->subStep = $subStep;
        $this->nextUrl = route('cooperation.frontend.tool.expert-scan.index', compact('step'));
        $this->boot();
    }

    public function boot()
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
        // TODO: Deprecate this dispatch in Livewire V2
        $this->dispatchBrowserEvent('element:updated', ['field' => $field, 'value' => $value]);
        $this->refreshAlerts();

        $this->setDirty(true);
    }

    protected function evaluateToolQuestions()
    {
        // Filter out the questions that do not match the condition
        // now collect the given answers
        $dynamicAnswers = [];
        foreach ($this->subStep->subSteppables as $subSteppablePivot) {
            if ($subSteppablePivot->isToolQuestion()) {
                $dynamicAnswers[$subSteppablePivot->subSteppable->short] = $this->filledInAnswers[$subSteppablePivot->subSteppable->id];
            }
        }

        foreach ($this->subStep->subSteppables as $index => $subSteppablePivot) {
            $toolQuestion = $subSteppablePivot->subSteppable;

            $answers = $dynamicAnswers;

            if (! empty($subSteppablePivot->conditions)) {
                $conditions = $subSteppablePivot->conditions;

                foreach ($conditions as $conditionSet) {
                    foreach ($conditionSet as $condition) {
                        // There is a possibility that the answer we're looking for is for a tool question not
                        // on this page. We find it, and add the answer to our list

                        if ($this->toolQuestions->where('short', $condition['column'])->count() === 0) {
                            $otherSubStepToolQuestion = ToolQuestion::where('short', $condition['column'])->first();
                            if ($otherSubStepToolQuestion instanceof ToolQuestion) {

                                $otherSubStepAnswer = $this->building
                                    ->getAnswer($this->masterInputSource, $otherSubStepToolQuestion);

                                $answers[$otherSubStepToolQuestion->short] = $otherSubStepAnswer;
                            }
                        }
                    }
                }

                $evaluatableAnswers = collect($answers);

                $evaluation = ConditionEvaluator::init()->evaluateCollection($conditions, $evaluatableAnswers);

                if (! $evaluation) {
                    $this->subStep->subSteppables = $this->subStep->subSteppables->forget($index);

                    // We will unset the answers the user has given. If the user then changes their mind, they
                    // will have to fill in the data again. We don't want to save values to the database
                    // that are unvalidated (or not relevant).

                    // Normally we'd use $this->reset(), but it doesn't seem like it likes nested items per dot

                    // we will only unset the rules if its a tool question, not relevant for other sub steppables.
                    if ($subSteppablePivot->isToolQuestion()) {
                        $this->filledInAnswers[$toolQuestion->id] = null;

                        // and unset the validation for the question based on type.
                        switch ($toolQuestion->data_type) {
                            case Caster::JSON:
                                foreach ($toolQuestion->options as $option) {
                                    unset($this->rules["filledInAnswers.{$toolQuestion->id}.{$option['short']}"]);
                                }
                                break;

                            case Caster::ARRAY:
                                unset($this->rules["filledInAnswers.{$toolQuestion->id}"]);
                                unset($this->rules["filledInAnswers.{$toolQuestion->id}.*"]);
                                break;

                            default:
                                unset($this->rules["filledInAnswers.{$toolQuestion->id}"]);
                                break;
                        }
                    }
                }
            }
        }
    }

    public function render()
    {
        $this->rehydrateToolQuestions();
        return view('livewire.cooperation.frontend.tool.expert-scan.sub-steppable');
    }

    public function save($nextUrl = "")
    {
        // Before we can validate (and save), we must reset the formatting from text to mathable
        foreach ($this->toolQuestions as $toolQuestion) {
            if ($toolQuestion->data_type === Caster::FLOAT) {
                $this->filledInAnswers[$toolQuestion->id] = NumberFormatter::mathableFormat(str_replace('.', '', $this->filledInAnswers[$toolQuestion->id]), 2);
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

            if ($validator->fails()) {
                // Validator failed, let's put it back as the user format
                foreach ($this->toolQuestions as $toolQuestion) {
                    if ($toolQuestion->data_type === Caster::INT || $toolQuestion->data_type === Caster::FLOAT) {
                        $this->filledInAnswers[$toolQuestion->id] = Caster::init($toolQuestion->data_type, $this->filledInAnswers[$toolQuestion->id])->getFormatForUser();
                    }
                }

                // notify the main form that validation failed for this particular sub step.
                $this->emitUp('failedValidationForSubSteps', $this->subStep);

                $this->dispatchBrowserEvent('validation-failed');
            }

            $validator->validate();
        }

        // Turns out, default values exist! We need to check if the tool questions have answers, else
        // they might not save...
        if (! $this->dirty) {
            foreach ($this->filledInAnswers as $toolQuestionId => $givenAnswer) {
                $toolQuestion = ToolQuestion::find($toolQuestionId);

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