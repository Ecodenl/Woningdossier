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
            'toolQuestions' => function ($query) {
                $query->with('forSpecificInputSource');
            },
            'subStepTemplate',
        ]);

        // This is important, as we want to perform evaluation pre-render
        $this->automaticallyEvaluate = false;
        $this->build();
    }

    public function render()
    {
        if ($this->componentReady) {
            $this->performEvaluation();
        }
        if ($this->loading) {
            $this->dispatchBrowserEvent('input-updated');
        }
        return view('livewire.cooperation.frontend.tool.expert-scan.sub-steppable');
    }

    public function getSubSteppablesProperty()
    {
        return $this->subStep->subSteppables()->orderBy('order')->with(['subSteppable', 'toolQuestionType'])->get();
    }

    public function getToolQuestionsProperty()
    {
        // Eager loaded in hydration
        return $this->subStep->toolQuestions;
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

    public function updated($field, $value)
    {
        parent::updated($field, $value);

        $this->emit('updateFilledInAnswers', $this->filledInAnswers, $this->id);
    }

    public function calculationsPerformed($calculationResults)
    {
        $this->calculationResults = $calculationResults;
        $this->dispatchBrowserEvent('input-update-processed');
        $this->loading = false;
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

    private function performEvaluation()
    {
        $this->setValidationForToolQuestions();
        $this->evaluateToolQuestions();
    }
}