<?php

namespace App\Http\Livewire\Cooperation\Frontend\Tool;

use App\Helpers\Conditions\ConditionEvaluator;
use App\Helpers\HoomdossierSession;
use App\Helpers\NumberFormatter;
use App\Models\ToolQuestion;
use App\Services\ToolQuestionService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Livewire\Component;

abstract class Scannable extends Component {


    public $rules;
    public $attributes;

    public $initialToolQuestions;
    public $toolQuestions;
    public $originalAnswers = [];
    public $filledInAnswers = [];
    public $filledInAnswersForAllInputSources = [];

    public bool $dirty;

    public function mount()
    {
        // first we have to hydrate the tool questions
        $this->hydrateToolQuestions();
        // after that we can fill up the user his given answers
        $this->setFilledInAnswers();
        // add the validation for the tool questions
        $this->setValidationForToolQuestions();
        // and evaluate the conditions for the tool questions, because we may have to hide questions upon load.
        $this->evaluateToolQuestions();

        $this->originalAnswers = $this->filledInAnswers;
        $this->initialToolQuestions = $this->toolQuestions;
    }
    abstract function hydrateToolQuestions();

    abstract function save();

    public function rehydrateToolQuestions()
    {
        // so we could also use the hydrateToolQuestions method
        // however this saves us a shitload of queries each request.
        $this->toolQuestions = $this->initialToolQuestions;
    }


    private function setValidationForToolQuestions()
    {
        foreach ($this->toolQuestions as $index => $toolQuestion) {
            switch ($toolQuestion->toolQuestionType->short) {
                case 'rating-slider':
                    foreach ($toolQuestion->options as $option) {
                        $this->rules["filledInAnswers.{$toolQuestion->id}.{$option['short']}"] = $this->prepareValidationRule($toolQuestion->validation);
                    }
                    break;

                case 'checkbox-icon':
                    // If this is set, it won't validate if nothing is clicked. We check if the validation is required,
                    // and then also set required for the main question
                    $this->rules["filledInAnswers.{$toolQuestion->id}.*"] = $this->prepareValidationRule($toolQuestion->validation);

                    if (in_array('required', $toolQuestion->validation)) {
                        $this->rules["filledInAnswers.{$toolQuestion->id}"] = ['required'];
                    }
                    break;

                default:
                    $this->rules["filledInAnswers.{$toolQuestion->id}"] = $this->prepareValidationRule($toolQuestion->validation);
                    break;
            }
        }
    }

    private function evaluateToolQuestions()
    {
        // Filter out the questions that do not match the condition
        // now collect the given answers
        $dynamicAnswers = [];
        foreach ($this->toolQuestions as $toolQuestion) {
            $dynamicAnswers[$toolQuestion->short] = $this->filledInAnswers[$toolQuestion->id];
        }

        foreach ($this->toolQuestions as $index => $toolQuestion) {

            $answers = $dynamicAnswers;

            if (! empty($toolQuestion->conditions)) {
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

    // specific for the popup questions
    public function resetToOriginalAnswer($toolQuestionId)
    {
        $this->filledInAnswers[$toolQuestionId] = $this->originalAnswers[$toolQuestionId];
    }

    public function updated($field, $value)
    {
        // TODO: Deprecate this dispatch in Livewire V2
        $this->dispatchBrowserEvent('element:updated', ['field' => $field, 'value' => $value]);

        $this->rehydrateToolQuestions();
        $this->setValidationForToolQuestions();
        $this->evaluateToolQuestions();

        $this->setDirty(true);
    }

    // specific to the popup question
    public function saveSpecificToolQuestion($toolQuestionId)
    {
        if (HoomdossierSession::isUserObserving()) {
            return null;
        }
        if (! empty($this->rules)) {
            $validator = Validator::make([
                "filledInAnswers.{$toolQuestionId}" => $this->filledInAnswers[$toolQuestionId]
            ], $this->rules["filledInAnswers.{$toolQuestionId}"], [], $this->attributes);

            // Translate values also
            $defaultValues = __('validation.values.defaults');

            $validator->addCustomValues([
                "filledInAnswers.{$toolQuestionId}" => $defaultValues,
            ]);

            if ($validator->fails()) {
                $toolQuestion = $this->toolQuestions->find($toolQuestionId);
                // Validator failed, let's put it back as the user format
                if ($toolQuestion->toolQuestionType->short === 'text' && \App\Helpers\Str::arrContains($toolQuestion->validation, 'numeric')) {
                    $isInteger = \App\Helpers\Str::arrContains($toolQuestion->validation, 'integer');
                    $this->filledInAnswers[$toolQuestion->id] = NumberFormatter::formatNumberForUser($this->filledInAnswers[$toolQuestion->id],
                        $isInteger, false);
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
        if (!$this->dirty) {
            $toolQuestion = ToolQuestion::find($toolQuestionId);

            // Define if we should check this question...
            if ($this->building->user->account->can('answer', $toolQuestion)) {
                $currentAnswer = $this->building->getAnswer($toolQuestion->forSpecificInputSource ?? $this->currentInputSource, $toolQuestion);
                $masterAnswer = $this->building->getAnswer($this->masterInputSource, $toolQuestion);

                // Master input source is important. Ensure both are set
                if (is_null($currentAnswer) || is_null($masterAnswer)) {
                    $this->setDirty(true);
                }
            }
        }

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

    private function setDirty(bool $dirty)
    {
        $this->dirty = $dirty;
    }


}