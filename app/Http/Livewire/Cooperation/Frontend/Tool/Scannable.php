<?php

namespace App\Http\Livewire\Cooperation\Frontend\Tool;

use App\Helpers\Arr;
use App\Helpers\Conditions\ConditionEvaluator;
use App\Helpers\DataTypes\Caster;
use App\Helpers\HoomdossierSession;
use App\Models\Building;
use App\Models\Cooperation;
use App\Models\InputSource;
use App\Models\ToolQuestion;
use App\Services\ToolQuestionService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Livewire\Component;

abstract class Scannable extends Component
{
    protected $listeners = ['update', 'updated', 'save'];

    public Building $building;

    public InputSource $masterInputSource;
    public InputSource $currentInputSource;

    public Cooperation $cooperation;

    public array $rules = [];
    public array $attributes = [];

    public array $originalAnswers = [];
    public array $filledInAnswers = [];
    public array $filledInAnswersForAllInputSources = [];

    public bool $dirty = false;
    public bool $automaticallyEvaluate = true;

    public function build()
    {
        $this->cooperation = HoomdossierSession::getCooperation(true);
        $this->building = HoomdossierSession::getBuilding(true);
        $this->masterInputSource = InputSource::findByShort(InputSource::MASTER_SHORT);
        $this->currentInputSource = HoomdossierSession::getInputSource(true);

        // after that we can fill up the user his given answers
        $this->setFilledInAnswers();

        if ($this->automaticallyEvaluate) {
            // add the validation for the tool questions
            $this->setValidationForToolQuestions();
            // and evaluate the conditions for the tool questions, because we may have to hide questions upon load.
            $this->evaluateToolQuestions();
        }
    }

    abstract function getSubSteppablesProperty();

    abstract function getToolQuestionsProperty();

    abstract function save();

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
        $this->emitTo('cooperation.frontend.layouts.parts.alerts', 'refreshAlerts', $this->filledInAnswers);
    }

    public function updated($field, $value)
    {
        if (Str::contains($field, 'filledInAnswers')) {
            $toolQuestionShort = Str::replaceFirst('filledInAnswers.', '', $field);
            $toolQuestion = ToolQuestion::findByShort($toolQuestionShort);
            if ($toolQuestion instanceof ToolQuestion) {
                // If it's an INT, we want to ensure the value set is also an INT
                if ($toolQuestion->data_type === Caster::INT) {
                    // So, if a value is an empty string, we will nullify it. If it's an empty string, it will get cast
                    // to a 0. We don't want that. If we do that, a user can never "reset" their answer. It will only
                    // ever be an empty string, when it's user input.

                    if ($value === '') {
                        $value = null;
                        $this->fill([$field => $value]);
                    }

                    $caster = Caster::init()->dataType(Caster::INT)->value($value);
                    $value = $caster->value($caster->reverseFormatted())->getFormatForUser();
                    $this->filledInAnswers[$toolQuestionShort] = $value;
                }
            }
        }

        if ($this->automaticallyEvaluate) {
            $this->setValidationForToolQuestions();
            $this->evaluateToolQuestions();
        }

        $this->refreshAlerts();

        $this->setDirty(true);
    }

    protected function evaluateToolQuestions()
    {
        $evaluator = ConditionEvaluator::init()
            ->building($this->building)
            ->inputSource($this->masterInputSource);

        // First fetch all conditions, so we can retrieve any required related answers in one go
        $conditionsForAllSubSteppables = $this->subSteppables->pluck('conditions')->flatten(1)->filter()->all();

        $answers = collect($this->filledInAnswers);
        // The expert scan has flown over answers. We want to add those for evaluation also, if they exist.
        // This way, we can reuse this method in both cases.
        if ($this->hasProperty('intercontinentalAnswers')) {
            $answers = $answers->merge(collect($this->intercontinentalAnswers));
        }

        $answersForAllSubSteppables = $evaluator->getToolAnswersForConditions(
            $conditionsForAllSubSteppables,
            $answers
        );

        $evaluator->setAnswers($answersForAllSubSteppables);

        foreach ($this->subSteppables as $index => $subSteppablePivot) {
            $toolQuestion = $subSteppablePivot->subSteppable;

            if (! empty($subSteppablePivot->conditions)) {
                $conditions = $subSteppablePivot->conditions;

                if (! $evaluator->evaluate($conditions)) {
                    $this->subSteppables->forget($index);

                    // We will unset the answers the user has given. If the user then changes their mind, they
                    // will have to fill in the data again. We don't want to save values to the database
                    // that are unvalidated (or not relevant).

                    // Normally we'd use $this->reset(), but it doesn't seem like it likes nested items per dot

                    // we will only unset the rules if its a tool question, not relevant for other sub steppables.
                    if ($subSteppablePivot->isToolQuestion()) {
                        // unset the validation for the question based on type.
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

    // specific for the popup questions
    public function resetToOriginalAnswer($toolQuestionShort)
    {
        $this->filledInAnswers[$toolQuestionShort] = $this->originalAnswers[$toolQuestionShort];
        $this->dispatchBrowserEvent('reset-question', ['short' => $toolQuestionShort]);
    }

    // specific to the popup question
    public function saveSpecificToolQuestion($toolQuestionShort)
    {
        abort_if(HoomdossierSession::isUserObserving(), 403);

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
                    $this->filledInAnswers[$toolQuestion->short] = Caster::init()->dataType($toolQuestion->data_type)->value($this->filledInAnswers[$toolQuestion->short])->getFormatForUser();
                }

                // TODO: Check if this should be subject to $this->automaticallyEvaluate
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
                    ToolQuestionService::init()
                        ->toolQuestion($toolQuestion)
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
            // We get all answers, including for the master. This way we reduce amount of queries needed.
            $this->filledInAnswersForAllInputSources[$toolQuestion->short] = $this->building->getAnswerForAllInputSources($toolQuestion, true);

            // We pluck the answer from the master input (unless a specific is requested)
            $answerForInputSource = null;
            $source = $toolQuestion->forSpecificInputSource ?? $this->masterInputSource;
            $answersForInputSource = $this->filledInAnswersForAllInputSources[$toolQuestion->short][$source->short] ?? null;
            if (! is_null($answersForInputSource)) {
                $values = Arr::pluck($answersForInputSource, 'value');

                if ($toolQuestion->data_type !== Caster::ARRAY) {
                    $values = Arr::first($values);
                }

                $answerForInputSource = $values;
            }

            // We unset the master so we don't show it in the source select dropdowns.
            unset($this->filledInAnswersForAllInputSources[$toolQuestion->short][$this->masterInputSource->short]);

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
                    $answerForInputSource = $answerForInputSource ?? $toolQuestion->options['value'] ?? null;
                    if (in_array($toolQuestion->data_type, [Caster::INT, Caster::FLOAT])) {
                        // Before we would set sliders and text answers differently. Now, because they are mapped by the
                        // same (by data type) it could be that value is not set.
                        $answerForInputSource = Caster::init()
                            ->dataType($toolQuestion->data_type)
                            ->value($answerForInputSource)
                            ->getFormatForUser();
                    }
                    $this->filledInAnswers[$toolQuestion->short] = $answerForInputSource;
                    $this->attributes["filledInAnswers.{$toolQuestion->short}"] = $toolQuestion->name;
                    break;
            }
        }

        $this->originalAnswers = $this->filledInAnswers;
    }

    private function prepareValidationRule(array $validation): array
    {
        // We need to check if the validation contains shorts to other tool questions, so we can set the short

        foreach ($validation as $index => $rule) {
            // Short is always on the right side of a colon
            if (Str::contains($rule, ':')) {
                $ruleParams = explode(':', $rule);
                // But can contain extra params

                // All rules that support linking to another field. No point in making calls if it's not this
                $supportedRules = [
                    'gt', 'gte', 'lt', 'lte', 'required_if', 'different', 'same', 'lowercase', 'uppercase',
                    'accepted_if', 'declined_if', 'exclude_if', 'exclude_unless', 'exclude_with', 'exclude_without',
                    'in_array', 'prohibited_if', 'prohibited_unless', 'prohibits',
                ];

                if (! empty($ruleParams[1]) && in_array($ruleParams[0], $supportedRules)) {
                    // Even if it's not in the string, explode will always put the result as first in the array.
                    $short = explode(',', $ruleParams[1])[0];

                    if (! empty($short)) {
                        $toolQuestion = ToolQuestion::findByShort($short);

                        // TODO: This doesn't take answers into account that aren't in the current FORM
                        if ($toolQuestion instanceof ToolQuestion) {
                            $validation[$index] = $ruleParams[0] . ':' . str_replace($short,
                                    "filledInAnswers.{$toolQuestion->short}", $ruleParams[1]);
                        }
                    }
                }
            }
        }

        return $validation;
    }

    protected function setDirty(bool $dirty)
    {
        $this->dirty = $dirty;
    }
}