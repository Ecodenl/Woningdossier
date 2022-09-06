<?php

namespace App\Http\Livewire\Cooperation\Frontend\Tool\ExpertScan;

use App\Calculations\HighEfficiencyBoiler;
use App\Helpers\Conditions\ConditionEvaluator;
use App\Helpers\DataTypes\Caster;
use App\Helpers\HoomdossierSession;
use App\Helpers\NumberFormatter;
use App\Http\Livewire\Cooperation\Frontend\Tool\Scannable;
use App\Models\InputSource;
use App\Models\Step;
use App\Models\SubStep;
use App\Models\ToolQuestion;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class SubSteppable extends Scannable
{
    public $step;
    public $subStep;
    public $nextUrl;

    public $calculationResults = [];

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
        $this->setDirty(true);

        $this->doCalculations();
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

    public function doCalculations()
    {
        //TODO: See if we need to check the considerables before we start our calculations, perhaps we could save
        // resources. However, it might require some sort of state knowledge which could prove difficult.

        // key = tool question short
        // value  = custom key for the calculate data, sometimes we can use the save in
        // for ex; new-boiler-type is a "new" question, previously we just had this as building_services for the current situation
        // the HR boiler calculate class is not adjusted to the old / new situation. It only knows that building_services.service_value_id is a boiler.
        // ideally this gets refactored when the time is ripe
        $saveInToolQuestionShorts = [
            'amount-gas' => null,
            'resident-count' => null,
            'new-boiler-type' => 'building_services.service_value_id',
            // this question is not asked on this page, which means we should retrieve it.
            'boiler-placed-date' => 'building_services.extra.date'
        ];

        $calculateData = [];
        foreach($saveInToolQuestionShorts as $toolQuestionShort => $key) {
            $toolQuestion = ToolQuestion::findByShort($toolQuestionShort);

            // it may be possible that the tool question is not present in the filled in answers.
            // that simply means the tool question is not available for the user on the current page
            // however it may be filled elsewhere, so we will get it through the getAnswer
            $answer = $this->filledInAnswers[$toolQuestion->id] ?? $this->building->getAnswer($this->masterInputSource,
                    $toolQuestion);

            Arr::set($calculateData, $key ?? $toolQuestion->save_in, $answer);
        }
        $energyHabit = $this->building->user->energyHabit()->forInputSource($this->masterInputSource)->first();

        // the HR boiler and solar boiler are not built with the tool questions in mind, we have to work with it for the time being
        $calculation = HighEfficiencyBoiler::calculate($energyHabit, $calculateData);

        // TODO: Heater + Heat pump, gotta check logic on the sub steps

        // Only the heat pump will be built with the tool questions in mind.

        $this->calculationResults = [
            'hr-boiler' => $calculation,
        ];
        Log::debug($this->calculationResults);
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
}