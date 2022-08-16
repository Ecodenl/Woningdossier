<?php

namespace App\Http\Livewire\Cooperation\Frontend\Tool\ExpertScan;

use App\Helpers\Conditions\ConditionEvaluator;
use App\Helpers\NumberFormatter;
use App\Models\Step;
use App\Models\SubStep;
use App\Models\ToolQuestion;
use Livewire\Component;

class Inputs extends Component
{

    public $step;
    public $subStep;

    public $toolQuestions;

    public function mount(Step $step, SubStep $subStep)
    {
        $this->step = $step;
        $this->subStep = $subStep;

        $this->setFilledInAnswers();
    }

    private function setToolQuestions()
    {
        // each request, the toolQuestions will be rehydrated. But not completely (no pivot) so we have to do this each time
        $this->toolQuestions = $this->subStep->toolQuestions()->orderBy('order')->get();

        // Filter out the questions that do not match the condition
        // now collect the given answers
        $dynamicAnswers = [];
        foreach ($this->toolQuestions as $toolQuestion) {
            $dynamicAnswers[$toolQuestion->short] = $this->filledInAnswers[$toolQuestion->id];
        }

        foreach ($this->toolQuestions as $index => $toolQuestion) {
            $this->setValidationForToolQuestion($toolQuestion);

            $answers = $dynamicAnswers;

            if (!empty($toolQuestion->conditions)) {
                foreach ($toolQuestion->conditions as $conditionSet) {
                    foreach ($conditionSet as $condition) {
                        // There is a possibility that the answer we're looking for is for a tool question not
                        // on this page. We find it, and add the answer to our list
                        if ($this->toolQuestions->where('short', $condition['column'])->count() === 0) {
                            $otherSubStepToolQuestion = ToolQuestion::where('short', $condition['column'])->first();
                            if ($otherSubStepToolQuestion instanceof ToolQuestion) {
                                $otherSubStepAnswer = $this->building->getAnswer($this->masterInputSource,
                                    $otherSubStepToolQuestion);

                                $answers[$otherSubStepToolQuestion->short] = $otherSubStepAnswer;
                            }
                        }
                    }
                }

                $evaluatableAnswers = collect($answers);

                $evaluation = ConditionEvaluator::init()->evaluateCollection($toolQuestion->conditions, $evaluatableAnswers);

                if (!$evaluation) {
                    $this->toolQuestions = $this->toolQuestions->forget($index);

                    // We will unset the answers the user has given. If the user then changes their mind, they
                    // will have to fill in the data again. We don't want to save values to the database
                    // that are unvalidated (or not relevant).

                    // Normally we'd use $this->reset(), but it doesn't seem like it likes nested items per dot
                    $this->filledInAnswers[$toolQuestion->id] = null;

                    // and unset the validation for the question based on type.
                    switch ($toolQuestion->toolQuestionType->short) {
                        case 'rating-slider':
                            foreach ($toolQuestion->options as $option) {
                                unset($this->rules["filledInAnswers.{$toolQuestion->id}.{$option['short']}"]);
                            }
                            break;

                        case 'checkbox-icon':
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

        // User's previous values could be defined, which means conditional questions should be hidden
        $this->setToolQuestions();

        $this->dirty = false;
    }

    public function render()
    {
        return view('livewire.cooperation.frontend.tool.expert-scan.inputs');
    }
}
