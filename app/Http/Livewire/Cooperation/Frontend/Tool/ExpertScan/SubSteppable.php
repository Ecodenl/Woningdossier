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


    public array $intercontinentalAnswers = [];

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
    }

    public function render()
    {
        if ($this->componentReady) {
            $this->performEvaluation();
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



    private function performEvaluation()
    {
        $this->setValidationForToolQuestions();
        $this->evaluateToolQuestions();
    }
}