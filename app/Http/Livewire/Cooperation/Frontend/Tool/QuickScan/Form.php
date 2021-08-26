<?php

namespace App\Http\Livewire\Cooperation\Frontend\Tool\QuickScan;

use App\Helpers\Conditions\ConditionEvaluator;
use App\Helpers\HoomdossierSession;
use App\Helpers\StepHelper;
use App\Helpers\ToolQuestionHelper;
use App\Models\Building;
use App\Models\CompletedSubStep;
use App\Models\InputSource;
use App\Models\Step;
use App\Models\SubStep;
use App\Models\ToolQuestion;
use App\Models\ToolQuestionCustomValue;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Livewire\Component;

class Form extends Component
{
    /*
     *
     * NOTE: When programmatically updating variables, ensure the updated method is called! This triggers a browser
     * event, which can be caught by the frontend and set visuals correct, e.g. with the sliders.
     *
     */

    protected $listeners = ['update', 'updated', 'save',];

    public $rules;
    public $attributes;

    /** @var Building */
    public $building;

    public $masterInputSource;
    public $currentInputSource;

    public $step;
    public $subStep;

    public $toolQuestions;

    public $filledInAnswers = [];
    public $filledInAnswersForAllInputSources = [];

    public function mount(Step $step, SubStep $subStep)
    {
        $subStep->load(['toolQuestions', 'subStepTemplate']);

        // set default steps, the checks will come later on.
        $this->step = $step;
        $this->subStep = $subStep;
        $this->building = HoomdossierSession::getBuilding(true);
        $this->masterInputSource = InputSource::findByShort(InputSource::MASTER_SHORT);

        $this->toolQuestions = $subStep->toolQuestions()->orderBy('order')->get();

        $this->currentInputSource = HoomdossierSession::getInputSource(true);

        $this->setFilledInAnswers();
    }


    public function render()
    {
        return view('livewire.cooperation.frontend.tool.quick-scan.form');
    }

    public function updated($field, $value)
    {
        // TODO: Deprecate this dispatch in Livewire V2
        $this->dispatchBrowserEvent('element:updated', ['field' => $field, 'value' => $value]);

        $this->setToolQuestions();

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

            if (! empty($toolQuestion->conditions)) {
                foreach ($toolQuestion->conditions as $conditionSet) {
                    foreach ($conditionSet as $condition) {
                        // There is a possibility that the answer we're looking for is for a tool question not
                        // on this page. We find it, and add the answer to our list
                        if ($this->toolQuestions->where('short', $condition['column'])->count() === 0) {
                            $otherSubStepToolQuestion = ToolQuestion::where('short', $condition['column'])->first();
                            if ($otherSubStepToolQuestion instanceof ToolQuestion) {
                                $otherSubStepAnswer = $this->building->getAnswer($this->currentInputSource,
                                    $otherSubStepToolQuestion);

                                $answers[$otherSubStepToolQuestion->short] = $otherSubStepAnswer;
                            }
                        }
                    }
                }

                $evaluatableAnswers = collect($answers);

                $evaluation = ConditionEvaluator::init()->evaluateCollection($toolQuestion->conditions, $evaluatableAnswers);

                if (! $evaluation) {
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

    public function save($nextUrl)
    {
        if (!empty($this->rules)) {
            $validator = Validator::make([
                'filledInAnswers' => $this->filledInAnswers
            ], $this->rules, [], $this->attributes);

            if ($validator->fails()) {
                $this->setToolQuestions();
            }

            $validator->validate();
        }


        foreach ($this->filledInAnswers as $toolQuestionId => $givenAnswer) {
            /** @var ToolQuestion $toolQuestion */
            $toolQuestion = ToolQuestion::where('id', $toolQuestionId)->with('toolQuestionType')->first();
            if (is_null($toolQuestion->save_in)) {
                $this->saveToolQuestionCustomValues($toolQuestion, $givenAnswer);
            } else {
                // this *can't* handle a checkbox / multiselect answer.
                $this->saveToolQuestionValuables($toolQuestion, $givenAnswer);
            }
        }

        $this->toolQuestions = $this->subStep->toolQuestions;

        // now mark the sub step as complete
        CompletedSubStep::firstOrCreate([
            'sub_step_id' => $this->subStep->id,
            'building_id' => $this->building->id,
            'input_source_id' => $this->currentInputSource->id
        ]);

        $lastSubStepForStep = $this->step->subSteps()->orderByDesc('order')->first();
        // last substep is done, now we can complete the main step
        if ($this->subStep->id === $lastSubStepForStep->id) {
            StepHelper::complete($this->step, $this->building, $this->currentInputSource);
        }

        return redirect()->to($nextUrl);
    }

    private function setFilledInAnswers()
    {
        // base key where every answer is stored
        foreach ($this->toolQuestions as $index => $toolQuestion) {

            $this->filledInAnswersForAllInputSources[$toolQuestion->id] = $this->building->getAnswerForAllInputSources($toolQuestion);

            /** @var array|string $answerForInputSource */
            $answerForInputSource = $this->building->getAnswer($this->masterInputSource, $toolQuestion);

            // We don't have to set rules here, as that's done in the setToolQuestions function which gets called
            switch ($toolQuestion->toolQuestionType->short) {
                case 'rating-slider':
                    $filledInAnswerOptions = json_decode($answerForInputSource, true);
                    foreach ($toolQuestion->options as $option) {

                        $this->filledInAnswers[$toolQuestion->id][$option['short']] = $filledInAnswerOptions[$option['short']] ?? 0;
                        $this->attributes["filledInAnswers.{$toolQuestion->id}.{$option['short']}"] = $option['name'];
                    }
                    break;
                case 'slider':
                    // default it when no answer is set, otherwise if the user leaves it default and submit the validation will fail because nothing is set.
                    $this->filledInAnswers[$toolQuestion->id] = $answerForInputSource ?? $toolQuestion->options['value'];
                    $this->attributes["filledInAnswers.{$toolQuestion->id}"] = $toolQuestion->name;
                    break;
                case 'checkbox-icon':
                    /** @var Collection $questionValues */
                    $answerForInputSource = $answerForInputSource ?? [];
                    $this->filledInAnswers[$toolQuestion->id] = [];
                    foreach ($answerForInputSource as $answer) {
                        $this->filledInAnswers[$toolQuestion->id][] = $answer;
                    }
                    $this->attributes["filledInAnswers.{$toolQuestion->id}"] = $toolQuestion->name;
                    $this->attributes["filledInAnswers.{$toolQuestion->id}.*"] = $toolQuestion->name;
                    break;
                default:
                    $this->filledInAnswers[$toolQuestion->id] = $answerForInputSource;
                    $this->attributes["filledInAnswers.{$toolQuestion->id}"] = $toolQuestion->name;
                    break;
            }
        }

        // User's previous values could be defined, which means conditional questions should be hidden
        $this->setToolQuestions();
    }

    private function setValidationForToolQuestion(ToolQuestion $toolQuestion)
    {
        switch ($toolQuestion->toolQuestionType->short) {
            case 'rating-slider':
                foreach ($toolQuestion->options as $option) {
                    $this->rules["filledInAnswers.{$toolQuestion->id}.{$option['short']}"] = $toolQuestion->validation;
                }
                break;

            case 'checkbox-icon':
                // If this is set, it won't validate if nothing is clicked. We check if the validation is required,
                // and then also set required for the main question
                $this->rules["filledInAnswers.{$toolQuestion->id}.*"] = $toolQuestion->validation;

                if (in_array('required', $toolQuestion->validation)) {
                    $this->rules["filledInAnswers.{$toolQuestion->id}"] = ['required'];
                }
                break;

            default:
            $this->rules["filledInAnswers.{$toolQuestion->id}"] = $toolQuestion->validation;
                break;
        }
    }

    private function saveToolQuestionValuables(ToolQuestion $toolQuestion, $givenAnswer)
    {
        $savedInParts = explode('.', $toolQuestion->save_in);
        $table = $savedInParts[0];
        $column = $savedInParts[1];

        // We will save it on the model, this way we keep the current events behind them
        $modelName = "App\\Models\\" . Str::ucFirst(Str::camel(Str::singular($table)));

        if (Schema::hasColumn($table, 'user_id')) {
            $where = ['user_id' => $this->building->user_id];
        } else {
            $where = ['building_id' => $this->building->id];
        }

        $where['input_source_id'] = $this->currentInputSource->id;

        // This means we have to add some thing to the where
        if (count($savedInParts) > 2) {
            // In this case the column holds a extra where value
            $where[ToolQuestionHelper::TABLE_COLUMN[$table]] = $column;
            $column = $savedInParts[2];
            // the extra column holds an array / JSON, so we have to transform the answer into an array
            if ($savedInParts[2] == 'extra') {
                // The column to which we actually have to save the data
                $column = 'extra';
                // In this case, the fourth index holds the json key.
                $jsonKey = $savedInParts[3];

                // We fetch the model, because we need to check its JSON values
                $model = $modelName::allInputSources()->where($where)->first();
                // If it's valid, we need to check its extra values

                if ($model instanceof $modelName && ! empty($model->{$column}) && is_array($model->{$column})) {
                    // Get model values, and then set the given key to the given answer
                    // We must do this, else all answers get overwritten
                    $tempAnswer = $model->{$column};
                    $tempAnswer[$jsonKey] = $givenAnswer;
                    $givenAnswer = $tempAnswer;
                } else {
                    $givenAnswer = [$jsonKey => $givenAnswer];
                }
            }
        }

        // Now save it
        $modelName::allInputSources()
            ->updateOrCreate(
                $where,
                [$column => $givenAnswer]
            );

//        $where['input_source_id'] = $this->masterInputSource->id;
//        $modelName::allInputSources()
//            ->updateOrCreate(
//                $where,
//                [$column => $givenAnswer]
//            );
    }

    private function saveToolQuestionCustomValues(ToolQuestion $toolQuestion, $givenAnswer)
    {
        $where = [
            'building_id' => $this->building->id,
            'tool_question_id' => $toolQuestion->id,
        ];
        $data = [
            'building_id' => $this->building->id,
            'input_source_id' => $this->currentInputSource->id,
        ];
//        dd($givenAnswer, $this->filledInAnswers);

        // we can't do a update or create, we just have to delete the old answers and create the new one.
        if ($toolQuestion->toolQuestionType->short == 'checkbox-icon') {

            $toolQuestion->toolQuestionAnswers()
                ->allInputSources()
                ->where($where)
                ->whereIn('input_source_id', [$this->masterInputSource->id, $this->currentInputSource->id])
                ->delete();

            foreach ($givenAnswer as $answer) {
                $toolQuestionCustomValue = ToolQuestionCustomValue::findByShort($answer);
                $data['tool_question_custom_value_id'] = $toolQuestionCustomValue->id;
                $data['answer'] = $answer;
                $toolQuestion->toolQuestionAnswers()->create($data)->replicate()->fill([
                    'input_source_id' => $this->masterInputSource->id
                ])->save();
            }

        } else {
            if (is_array($givenAnswer)) {
                $givenAnswer = json_encode($givenAnswer);
            }

            // Try to resolve the id is the question has custom values
            if ($toolQuestion->toolQuestionCustomValues()->exists()) {
                // if so, the given answer contains a short.
                $toolQuestionCustomValue               = ToolQuestionCustomValue::findByShort($givenAnswer);
                $data['tool_question_custom_value_id'] = $toolQuestionCustomValue->id;
            }

            $data['answer']           = $givenAnswer;
            $where['input_source_id'] = $this->currentInputSource->id;
            // we have to do this twice, once for the current input source and once for the master input source
            $toolQuestion
                ->toolQuestionAnswers()
                ->allInputSources()
                ->updateOrCreate($where, $data);
//        $where['input_source_id'] = $this->masterInputSource->id;
//        $data['input_source_id'] = $this->masterInputSource->id;
//        $toolQuestion
//            ->toolQuestionAnswers()
//            ->allInputSources()
//            ->updateOrCreate($where, $data);
        }
    }
}
