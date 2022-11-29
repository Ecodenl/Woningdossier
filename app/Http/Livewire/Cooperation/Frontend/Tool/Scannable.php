<?php

namespace App\Http\Livewire\Cooperation\Frontend\Tool;

use App\Helpers\Conditions\ConditionEvaluator;
use App\Helpers\DataTypes\Caster;
use App\Helpers\HoomdossierSession;
use App\Models\Building;
use App\Models\Cooperation;
use App\Models\InputSource;
use App\Models\ToolQuestion;
use App\Services\ToolQuestionService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Livewire\Component;

abstract class Scannable extends Component
{
    protected $listeners = ['update', 'updated', 'save'];

    public Building $building;

    public InputSource $masterInputSource;
    public InputSource $currentInputSource;
    public InputSource $residentInputSource;
    public InputSource $coachInputSource;

    public Cooperation $cooperation;

    public array $rules = [];
    public array $attributes = [];

    public Collection $toolQuestions;
    public array $originalAnswers = [];
    public array $filledInAnswers = [];
    public array $filledInAnswersForAllInputSources = [];

    public bool $dirty = false;

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
        // add the validation for the tool questions
        $this->setValidationForToolQuestions();
        // and evaluate the conditions for the tool questions, because we may have to hide questions upon load.
        $this->evaluateToolQuestions();

        $this->originalAnswers = $this->filledInAnswers;
    }


    abstract function hydrateToolQuestions();

    abstract function save();

    abstract function rehydrateToolQuestions();

    protected function setValidationForToolQuestions()
    {
        foreach ($this->toolQuestions as $index => $toolQuestion) {
            switch ($toolQuestion->data_type) {
                case Caster::JSON:
                    foreach ($toolQuestion->options as $option) {
                        $this->rules["filledInAnswers.{$toolQuestion->short}.{$option['short']}"] = $this->prepareValidationRule($toolQuestion->validation);
                    }
                    break;


                case Caster::ARRAY:
                    // If this is set, it won't validate if nothing is clicked. We check if the validation is required,
                    // and then also set required for the main question
                    $this->rules["filledInAnswers.{$toolQuestion->short}.*"] = $this->prepareValidationRule($toolQuestion->validation);

                    if (in_array('required', $toolQuestion->validation)) {
                        $this->rules["filledInAnswers.{$toolQuestion->short}"] = ['required'];
                    }
                    break;

                default:
                    $this->rules["filledInAnswers.{$toolQuestion->short}"] = $this->prepareValidationRule($toolQuestion->validation);
                    break;
            }
        }
    }

    protected function refreshAlerts()
    {
        $answers = $this->prepareAnswersForEvaluation();

        $this->emitTo('cooperation.frontend.layouts.parts.alerts', 'refreshAlerts', $answers);
    }

    public function prepareAnswersForEvaluation(): array
    {
        $answers = [];
        foreach ($this->toolQuestions as $toolQuestion) {
            $answers[$toolQuestion->short] = $this->filledInAnswers[$toolQuestion->short];
        }

        return $answers;
    }

    public function updated($field, $value)
    {
        $toolQuestionShort = Str::replaceFirst('filledInAnswers.', '', $field);
        $toolQuestion = ToolQuestion::findByShort($toolQuestionShort);
        if ($toolQuestion instanceof ToolQuestion) {
            // If it's an INT, we want to ensure the value set is also an INT
            if ($toolQuestion->data_type === Caster::INT) {
                $value = Caster::init(Caster::INT, Caster::init(Caster::INT, $value)->reverseFormatted())->getFormatForUser();
                $this->filledInAnswers[$toolQuestionShort] = $value;
            }
        }

        $this->rehydrateToolQuestions();
        $this->setValidationForToolQuestions();
        $this->evaluateToolQuestions();
        $this->refreshAlerts();

        $this->setDirty(true);
    }

    protected function evaluateToolQuestions()
    {
        $evaluator = ConditionEvaluator::init()
            ->building($this->building)
            ->inputSource($this->masterInputSource);

        // First fetch all conditions, so we can retrieve any required related answers in one go
        $conditionsForAllQuestions = [];
        foreach (array_filter($this->toolQuestions->pluck('pivot.conditions')->all()) as $condition) {
            $conditionsForAllQuestions = array_merge($conditionsForAllQuestions, $condition);
        }
        $answersForAllQuestions = $evaluator->getToolAnswersForConditions($conditionsForAllQuestions, collect($this->filledInAnswers));

        foreach ($this->toolQuestions as $index => $toolQuestion) {
            if (! empty($toolQuestion->pivot->conditions)) {
                $conditions = $toolQuestion->pivot->conditions;

                if (! $evaluator->evaluateCollection($conditions, $answersForAllQuestions)) {
                    $this->toolQuestions = $this->toolQuestions->forget($index);

                    // We will unset the answers the user has given. If the user then changes their mind, they
                    // will have to fill in the data again. We don't want to save values to the database
                    // that are unvalidated (or not relevant).

                    // Normally we'd use $this->reset(), but it doesn't seem like it likes nested items per dot
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

    // specific for the popup questions
    public function resetToOriginalAnswer($toolQuestionShort)
    {
        $this->filledInAnswers[$toolQuestionShort] = $this->originalAnswers[$toolQuestionShort];
    }

    // specific to the popup question
    public function saveSpecificToolQuestion($toolQuestionShort)
    {
        if (HoomdossierSession::isUserObserving()) {
            return null;
        }
        if (! empty($this->rules)) {
            $validator = Validator::make([
                "filledInAnswers.{$toolQuestionShort}" => $this->filledInAnswers[$toolQuestionShort]
            ], $this->rules["filledInAnswers.{$toolQuestionShort}"], [], $this->attributes);

            // Translate values also
            $defaultValues = __('validation.values.defaults');

            $validator->addCustomValues([
                "filledInAnswers.{$toolQuestionShort}" => $defaultValues,
            ]);

            if ($validator->fails()) {
                $toolQuestion = $this->toolQuestions->where('short', $toolQuestionShort)->first();
                // Validator failed, let's put it back as the user format
                if (in_array($toolQuestion->data_type, [Caster::INT, Caster::FLOAT])) {
                    $this->filledInAnswers[$toolQuestion->short] = Caster::init($toolQuestion->data_type, $this->filledInAnswers[$toolQuestion->short])->getFormatForUser();
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
            $toolQuestion = ToolQuestion::findByShort($toolQuestionShort);

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
            foreach ($this->filledInAnswers as $toolQuestionShort => $givenAnswer) {
                // Define if we should answer this question...
                /** @var ToolQuestion $toolQuestion */
                $toolQuestion = ToolQuestion::findByShort($toolQuestionShort);
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

    protected function setFilledInAnswers()
    {
        // base key where every answer is stored
        foreach ($this->toolQuestions as $index => $toolQuestion) {

            $this->filledInAnswersForAllInputSources[$toolQuestion->short] = $this->building->getAnswerForAllInputSources($toolQuestion);

            /** @var array|string $answerForInputSource */
            $answerForInputSource = $this->building->getAnswer($toolQuestion->forSpecificInputSource ?? $this->masterInputSource, $toolQuestion);

            // We don't have to set rules here, as that's done in the setToolQuestions function which gets called
            switch ($toolQuestion->data_type) {
                case Caster::JSON:
                    $filledInAnswerOptions = json_decode($answerForInputSource, true);
                    foreach ($toolQuestion->options as $option) {
                        $this->filledInAnswers[$toolQuestion->short][$option['short']] = $filledInAnswerOptions[$option['short']] ?? $option['value'] ?? 0;
                        $this->attributes["filledInAnswers.{$toolQuestion->short}.{$option['short']}"] = $option['name'];
                    }
                    break;
                case Caster::ARRAY:
                    /** @var array $answerForInputSource */
                    $answerForInputSource = $answerForInputSource ?? $toolQuestion->options['value'] ?? [];
                    $this->filledInAnswers[$toolQuestion->short] = [];
                    foreach ($answerForInputSource as $answer) {
                        $this->filledInAnswers[$toolQuestion->short][] = $answer;
                    }
                    $this->attributes["filledInAnswers.{$toolQuestion->short}"] = $toolQuestion->name;
                    $this->attributes["filledInAnswers.{$toolQuestion->short}.*"] = $toolQuestion->name;
                    break;
                default:
                    if (in_array($toolQuestion->data_type, [Caster::INT, Caster::FLOAT])) {
                        // Before we would set sliders and text answers differently. Now, because they are mapped by the
                        // same (by data type) it could be that value is not set.
                        $answer = $answerForInputSource ?? $toolQuestion->options['value'] ?? 0;
                        $answerForInputSource = Caster::init($toolQuestion->data_type, $answer)->getFormatForUser();
                    }

                    $this->filledInAnswers[$toolQuestion->short] = $answerForInputSource;
                    $this->attributes["filledInAnswers.{$toolQuestion->short}"] = $toolQuestion->name;
                    break;
            }
        }
    }

    private function prepareValidationRule(array $validation): array
    {
        // In the future we will make things such as dates dynamic in the ruleset, so we keep this function to
        // minimize refactor/flow work. However, for now it won't do anything, as the old code which used to be
        // present here is no longer relevant.
        return $validation;
    }

    protected function setDirty(bool $dirty)
    {
        $this->dirty = $dirty;
    }
}