<?php

namespace App\Http\Livewire\Cooperation\Frontend\Tool\ExpertScan;

use App\Console\Commands\Tool\RecalculateForUser;
use App\Helpers\Conditions\ConditionEvaluator;
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

class Form extends Scannable
{
    public $step;

    public function mount(Step $step)
    {
        $this->step = $step;
        $this->boot();
    }

    public function hydrateToolQuestions()
    {
        $toolQuestions = [];
        foreach ($this->step->subSteps as $subStep) {
            foreach ($subStep->toolQuestions()->orderBy('order')->get() as $toolQuestion) {
                $toolQuestions[$toolQuestion->id] = $toolQuestion;
            }
        }

        $this->toolQuestions = new EloquentCollection($toolQuestions);
    }

    public function render()
    {
        return view('livewire.cooperation.frontend.tool.expert-scan.form');
    }

    public function save()
    {
        // Before we can validate (and save), we must reset the formatting from text to mathable
        foreach ($this->toolQuestions as $toolQuestion) {
            if ($toolQuestion->toolQuestionType->short === 'text' && \App\Helpers\Str::arrContains($toolQuestion->validation, 'numeric') && !\App\Helpers\Str::arrContains($toolQuestion->validation, 'integer')) {
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
                    if ($toolQuestion->toolQuestionType->short === 'text' && \App\Helpers\Str::arrContains($toolQuestion->validation, 'numeric')) {
                        $isInteger = \App\Helpers\Str::arrContains($toolQuestion->validation, 'integer');
                        $this->filledInAnswers[$toolQuestion->id] = NumberFormatter::formatNumberForUser($this->filledInAnswers[$toolQuestion->id],
                            $isInteger, false);
                    }
                }

                $this->rehydrateToolQuestions();
                $this->setValidationForToolQuestions();
                $this->evaluateToolQuestions();

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
                        $this->dirty = true;
                        break;
                    }
                }
            }
        }


        $stepShortsToRecalculate = [];
        $shouldDoFullRecalculate = false;

        $masterHasCompletedQuickScan = $this->building->hasCompletedQuickScan($this->masterInputSource);
        // Answers have been updated, we save them and dispatch a recalculate
        if ($this->dirty) {
            foreach ($this->filledInAnswers as $toolQuestionId => $givenAnswer) {
                // Define if we should answer this question...
                /** @var ToolQuestion $toolQuestion */
                $toolQuestion = ToolQuestion::where('id', $toolQuestionId)->with('toolQuestionType')->first();
                if ($this->building->user->account->can('answer', $toolQuestion)) {
                    ToolQuestionService::init($toolQuestion)
                        ->building($this->building)
                        ->currentInputSource($this->currentInputSource)
                        ->applyExampleBuilding()
                        ->save($givenAnswer);

                    if (ToolQuestionHelper::shouldToolQuestionDoFullRecalculate($toolQuestion) && $masterHasCompletedQuickScan) {
                        Log::debug("Question {$toolQuestion->short} should trigger a full recalculate");
                        $shouldDoFullRecalculate = true;
                    }

                    // get the expert step equivalent
                    // we will filter out duplicates later on.
                    $stepShortsToRecalculate = array_merge($stepShortsToRecalculate, ToolQuestionHelper::stepShortsForToolQuestion($toolQuestion));
                }
            }
        }


        // the INITIAL calculation will be handled by the CompletedSubStepObserver
        if ($shouldDoFullRecalculate) {
            // We should do a full recalculate because some base value that has impact on every calculation is changed.
            Log::debug("Dispatching full recalculate..");

            Artisan::call(RecalculateForUser::class, [
                '--user' => [$this->building->user->id],
                '--input-source' => [$this->currentInputSource->short],
                // we are doing a full recalculate, we want to keep the user his advices organised as they are at the moment.
                '--with-old-advices' => true,
            ]);

            // only when there are steps to recalculate, otherwise the command would just do a FULL recalculate.
        } else if ($masterHasCompletedQuickScan && ! empty($stepShortsToRecalculate)) {
            // the user already has completed the quick scan, so we will only recalculate specific parts of the advices.
            $stepShortsToRecalculate = array_unique($stepShortsToRecalculate);
            // since we are just re-calculating specific parts of the tool we do it without the old advices
            // it will keep the advices that are not correlated to the steps we are calculating at their current category and order
            // but it moves the re-calculated advices to the proper column.
            Artisan::call(RecalculateForUser::class, [
                '--user' => [$this->building->user->id],
                '--input-source' => [$this->currentInputSource->short],
                '--step-short' => $stepShortsToRecalculate,
                '--with-old-advices' => false,
            ]);
        }

        // Now mark the sub step as complete
//        CompletedSubStep::firstOrCreate([
//            'sub_step_id' => $this->subStep->id,
//            'building_id' => $this->building->id,
//            'input_source_id' => $this->currentInputSource->id
//        ]);

        return redirect()->back();
    }

    private function setFilledInAnswers()
    {
        // base key where every answer is stored
        foreach ($this->toolQuestions as $index => $toolQuestion) {

            $this->filledInAnswersForAllInputSources[$toolQuestion->id] = $this->building->getAnswerForAllInputSources($toolQuestion);

            /** @var array|string $answerForInputSource */
            $answerForInputSource = $this->building->getAnswer($toolQuestion->forSpecificInputSource ?? $this->masterInputSource, $toolQuestion);


            // We don't have to set rules here, as that's done in the setToolQuestions function which gets called
            switch ($toolQuestion->toolQuestionType->short) {
                case 'rating-slider':
                    $filledInAnswerOptions = json_decode($answerForInputSource, true);
                    foreach ($toolQuestion->options as $option) {
                        $this->filledInAnswers[$toolQuestion->id][$option['short']] = $filledInAnswerOptions[$option['short']] ?? $option['value'] ?? 0;
                        $this->attributes["filledInAnswers.{$toolQuestion->id}.{$option['short']}"] = $option['name'];
                    }
                    break;
                case 'slider':
                    // Default is required here when no answer is set, otherwise if the user leaves it default
                    // and submits, the validation will fail because nothing is set.

                    // Format answer to remove leading decimals
                    $this->filledInAnswers[$toolQuestion->id] = NumberFormatter::formatNumberForUser(($answerForInputSource ?? $toolQuestion->options['value']),
                        true, false);
                    $this->attributes["filledInAnswers.{$toolQuestion->id}"] = $toolQuestion->name;
                    break;
                case 'checkbox-icon':
                    /** @var array $answerForInputSource */
                    $answerForInputSource = $answerForInputSource ?? $toolQuestion->options['value'] ?? [];
                    $this->filledInAnswers[$toolQuestion->id] = [];
                    foreach ($answerForInputSource as $answer) {
                        $this->filledInAnswers[$toolQuestion->id][] = $answer;
                    }
                    $this->attributes["filledInAnswers.{$toolQuestion->id}"] = $toolQuestion->name;
                    $this->attributes["filledInAnswers.{$toolQuestion->id}.*"] = $toolQuestion->name;
                    break;
                default:
                    // Check if question type is text, so we can format it if it's numeric
                    if ($toolQuestion->toolQuestionType->short === 'text' && \App\Helpers\Str::arrContains($toolQuestion->validation, 'numeric')) {
                        $isInteger = \App\Helpers\Str::arrContains($toolQuestion->validation, 'integer');
                        $answerForInputSource = NumberFormatter::formatNumberForUser($answerForInputSource, $isInteger, false);
                    }

                    $this->filledInAnswers[$toolQuestion->id] = $answerForInputSource;
                    $this->attributes["filledInAnswers.{$toolQuestion->id}"] = $toolQuestion->name;
                    break;
            }
        }

        $this->dirty = false;
    }


    private function prepareValidationRule(array $validation): array
    {
        // We need to check if the validation contains shorts to other tool questions, so we can set the ID

        foreach ($validation as $index => $rule) {
            // Short is always on the right side of a colon
            if (Str::contains($rule, ':')) {
                $ruleParams = explode(':', $rule);
                // But can contain extra params

                if (! empty($ruleParams[1])) {
                    $short = Str::contains($ruleParams[1], ',') ? explode(',', $ruleParams[1])[0]
                        : $ruleParams[1];

                    if (! empty($short)) {
                        $toolQuestion = ToolQuestion::findByShort($short);
                        $toolQuestion = $toolQuestion instanceof ToolQuestion ? $toolQuestion : ToolQuestion::findByShort(Str::kebab(Str::camel($short)));

                        if ($toolQuestion instanceof ToolQuestion) {
                            $validation[$index] = $ruleParams[0] . ':' . str_replace($short,
                                    "filledInAnswers.{$toolQuestion->id}", $ruleParams[1]);
                        }
                    }
                }
            }
        }

        return $validation;
    }
}
