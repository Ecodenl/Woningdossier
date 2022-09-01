<?php

namespace App\Http\Livewire\Cooperation\Frontend\Tool\ExpertScan;

use App\Console\Commands\Tool\RecalculateForUser;
use App\Helpers\Conditions\ConditionEvaluator;
use App\Helpers\DataTypes\Caster;
use App\Helpers\HoomdossierSession;
use App\Helpers\NumberFormatter;
use App\Helpers\ToolQuestionHelper;
use App\Http\Livewire\Cooperation\Frontend\Tool\Scannable;
use App\Models\Building;
use App\Models\CompletedSubStep;
use App\Models\InputSource;
use App\Models\Step;
use App\Models\SubStep;
use App\Models\ToolQuestion;
use App\Services\ToolQuestionService;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Livewire\Component;

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

    public function hydrateToolQuestions()
    {
        $this->rehydrateToolQuestions();
    }

    public function rehydrateToolQuestions()
    {
        $this->toolQuestions = $this->subStep->toolQuestions;
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

                $this->rehydrateToolQuestions();
                $this->setValidationForToolQuestions();
                $this->evaluateToolQuestions();

                $this->dispatchBrowserEvent('validation-failed');
            } else {
                // the validator did not fail, so we will notify the main form that its saved.
                $this->emitUp('subStepValidationSucceeded', $this->subStep);
            }

            $validator->validate();
        }

        // Turns out, default values exist! We need to check if the tool questions have answers, else
        // they might not save...
        if (!$this->dirty) {
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

        if ($this->dirty && !$validator->fails()) {
            Log::debug('dirty, setting filledInAnswers');
            $this->emitUp('setFilledInAnswers', $this->filledInAnswers);
        }
    }

}
