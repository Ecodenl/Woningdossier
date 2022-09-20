<?php

namespace App\Services;

use App\Helpers\Arr;
use App\Helpers\Conditions\ConditionEvaluator;
use App\Helpers\DataTypes\Caster;
use App\Helpers\ToolQuestionHelper;
use App\Jobs\ApplyExampleBuildingForChanges;
use App\Models\Building;
use App\Models\CompletedStep;
use App\Models\CompletedSubStep;
use App\Models\InputSource;
use App\Models\ToolQuestion;
use App\Models\ToolQuestionCustomValue;
use App\Models\ToolQuestionValuable;
use App\Traits\FluentCaller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ToolQuestionService {

    use FluentCaller;

    public ?Building $building;
    public ToolQuestion $toolQuestion;
    public InputSource $masterInputSource;
    public ?InputSource $currentInputSource;
    public bool $applyExampleBuilding = false;

    public function __construct(ToolQuestion $toolQuestion)
    {
        $this->toolQuestion = $toolQuestion;
        $this->masterInputSource = InputSource::findByShort(InputSource::MASTER_SHORT);
    }

    public function building(Building $building): self
    {
        $this->building = $building;
        return $this;
    }

    public function currentInputSource($inputSource): self
    {
        $this->currentInputSource = $inputSource;
        return $this;
    }

    public function applyExampleBuilding(): self
    {
        $this->applyExampleBuilding = true;
        return $this;
    }

    public function save($givenAnswer)
    {
        if (is_null($this->toolQuestion->save_in)) {
            $this->saveToolQuestionCustomValues($givenAnswer);
        } else {
            // this *can't* handle a checkbox / multiselect answer.
            $this->saveToolQuestionValuables($givenAnswer);
        }
    }

    public function saveToolQuestionCustomValues($givenAnswer)
    {
        $where = [
            'building_id' => $this->building->id,
            'tool_question_id' => $this->toolQuestion->id,
        ];
        $data = [
            'building_id' => $this->building->id,
            'input_source_id' => $this->currentInputSource->id,
        ];

        if (is_null($givenAnswer)) {
            // Answer is null. This means the answer should be removed
            $this->clearAnswer($this->toolQuestion, $where);
            return;
        }

        // We can't do a update or create, we just have to delete the old answers and create the new one.
        if ($this->toolQuestion->data_type === Caster::ARRAY) {
            $this->toolQuestion->toolQuestionAnswers()
                ->allInputSources()
                ->where($where)
                ->whereIn('input_source_id', [$this->masterInputSource->id, $this->currentInputSource->id])
                ->delete();

            foreach ($givenAnswer as $answer) {
                $toolQuestionCustomValue = ToolQuestionCustomValue::where('tool_question_id', $this->toolQuestion->id)
                    ->whereShort($answer)->first();
                $data['tool_question_custom_value_id'] = $toolQuestionCustomValue->id;
                $data['answer'] = $answer;
                $this->toolQuestion->toolQuestionAnswers()->create($data);
            }
        } else {
            if (is_array($givenAnswer)) {
                $givenAnswer = json_encode($givenAnswer);
            }

            // Try to resolve the ID if the question has custom values
            if ($this->toolQuestion->toolQuestionCustomValues()->exists()) {
                // if so, the given answer contains a short.
                $toolQuestionCustomValue = ToolQuestionCustomValue::where('tool_question_id', $this->toolQuestion->id)
                    ->whereShort($givenAnswer)->first();
                $data['tool_question_custom_value_id'] = $toolQuestionCustomValue->id;
            }

            $data['answer'] = $givenAnswer;
            $where['input_source_id'] = $this->currentInputSource->id;
            $this->toolQuestion
                ->toolQuestionAnswers()
                ->allInputSources()
                ->updateOrCreate($where, $data);
        }

       $this->checkConditionalAnswers($givenAnswer);
    }

    public function saveToolQuestionValuables($givenAnswer)
    {
        $toolQuestion = $this->toolQuestion;

        $saveIn = ToolQuestionHelper::resolveSaveIn($toolQuestion, $this->building);
        $table  = $saveIn['table'];
        $column = $saveIn['column'];
        $where  = $saveIn['where'];

        $where['input_source_id'] = $this->currentInputSource->id;

        // We will save it on the model, this way we keep the current events behind them
        $modelName = "App\\Models\\" . Str::studly(Str::singular($table));

        // We cannot use a pluck, so we must split the column
        if (Str::startsWith($column, 'extra')) {
            $columnParts = explode('.', $column);
            $column = $columnParts[0];
            // In this case, the original fourth, and in this case second index holds the json key.
            $jsonKey = $columnParts[1];

            // We fetch the model, because we need to check its JSON values
            $model = $modelName::allInputSources()->where($where)->first();
            // If it's valid, we need to check its extra values

            if ($model instanceof $modelName && !empty($model->{$column}) && is_array($model->{$column})) {
                // Get model values, and then set the given key to the given answer
                // We must do this, else all answers get overwritten
                $tempAnswer = $model->{$column};
                $tempAnswer[$jsonKey] = $givenAnswer;
                $givenAnswer = $tempAnswer;
            } else {
                $givenAnswer = [$jsonKey => $givenAnswer];
            }
        }

        $answerData = [$column => $givenAnswer];

        // Before saving, we must do one last thing. We need to check if we need to apply some more logic.
        $studlyShort = Str::studly($toolQuestion->short);
        $questionAnswerClass = "App\\Helpers\\QuestionAnswers\\{$studlyShort}";
        if (class_exists($questionAnswerClass)) {
            $additionalData = $questionAnswerClass::apply($toolQuestion, $givenAnswer);
            $answerData = array_merge($answerData, $additionalData);
        }

        if ($this->applyExampleBuilding) {
            // Detect if the example building will be changing. If so, apply it.
            // I hear you thinking: wouldn't this be better off in an observer?
            // The answer is: No. Unless you want to trigger an infinite loop
            // as applying the example building will delete and recreate records,
            // which will trigger the observer, which will start applying the
            // example building, which will delete and recreate records, which will
            // trigger the observer.. ah well: you get the idea.
            if (in_array($table, ['building_features']) && Arr::inArrayAny(['build_year', 'building_type_id', 'example_building_id'], array_keys($answerData))) {
                // set the boolean to the appropriate value. Example building will
                // be applied AFTER saving the current form (for getting the
                // appropriate values).

                Log::debug("Changes for table '{$table}':");
                Log::debug($answerData);

                $oldBuildingFeature = $this->building->buildingFeatures()->forInputSource($this->masterInputSource)->first();
                // apply the example building for the given changes.
                // we give him the old building features, otherwise we cant verify the changes
                ApplyExampleBuildingForChanges::dispatchNow($oldBuildingFeature, $answerData, $this->currentInputSource);
            }
        }

        // Now save it
        $modelName::allInputSources()
            ->updateOrCreate(
                $where,
                $answerData
            );

        $this->checkConditionalAnswers($givenAnswer);
    }

    /**
     * If any conditionally shown answer tied to this tool question is selected, unselect
     * it and incomplete related (sub) steps.
     *
     * @param $givenAnswer
     *
     * @return void
     * @throws \Exception
     */
    private function checkConditionalAnswers($givenAnswer)
    {
        // TODO: Check the format for rating-slider questions (also in the evaluator itself)
        // We build the answers ourselves to make a few less queries
        $answers = collect([
            $this->toolQuestion->short => is_array($givenAnswer) ? collect($givenAnswer) : $givenAnswer,
        ]);

        // Now we need to find any conditional answers that might be related to this question
        // Quotes around the short are important. If we don't, then MySQL throws a hissy fit.
        $conditionalCustomValues = ToolQuestionCustomValue::whereRaw('JSON_CONTAINS(conditions->"$**.column", ?, "$")', ["\"{$this->toolQuestion->short}\""])
            ->get();
        //$toolQuestionValuables = ToolQuestionValuable::whereRaw('JSON_CONTAINS(conditions->"$**.column", ?, "$")', ["\"{$this->toolQuestion->short}\""])
        //    ->get();

        $evaluator = ConditionEvaluator::init()
            ->inputSource($this->currentInputSource)
            ->building($this->building);

        $toolQuestionsToUnset = [];
        foreach ($conditionalCustomValues as $conditionalCustomValue) {
            if (! $evaluator->evaluateCollection($conditionalCustomValue->conditions, $answers)) {
                $answer = $this->building->getAnswer($this->currentInputSource, $conditionalCustomValue->toolQuestion);

                // TODO: Expand this if there are single-value answers
                if (is_array($answer) && in_array($conditionalCustomValue->short, $answer)) {
                    // Add tool question to array to use later for resetting sub steps
                    $toolQuestionsToUnset[] = $conditionalCustomValue->toolQuestion;

                    // Reset answer
                    $where = [
                        'building_id' => $this->building->id,
                        'tool_question_id' => $conditionalCustomValue->toolQuestion->id,
                        'tool_question_custom_value_id' => $conditionalCustomValue->id,
                    ];
                    $this->clearAnswer($conditionalCustomValue->toolQuestion, $where);
                }
            }
        }

        //TODO: When we get conditional valuables, we want this to check whether there are valuables
        // that no longer pass the conditions, because then the answer(s) for the related tool questions
        // should be reset

        //foreach ($toolQuestionValuables as $conditionalValuable) {
        //    if (! $evaluator->evaluateCollection($conditionalValuable->conditions, $answers)) {
        //        $answer = $this->building->getAnswer($this->currentInputSource, $conditionalValuable->toolQuestion);
        //
        //        // TODO: Expand this if there are multi-value answers
        //        if (! is_array($answer) && $conditionalValuable->tool_question_valuable_id == $answer) {
        //            // Add tool question to array to use later for resetting sub steps
        //            $toolQuestionsToUnset[] = $conditionalValuable->toolQuestion;
        //
        //            // Reset answer
        //            $where = [
        //                'building_id' => $this->building->id,
        //                'tool_question_id' => $conditionalValuable->toolQuestion->id,
        //            ];
        //            $this->clearAnswer($conditionalValuable->toolQuestion, $where);
        //        }
        //    }
        //}

        if (! empty($toolQuestionsToUnset)) {
            $processedIds = [];

            // Clear (sub) steps
            foreach ($toolQuestionsToUnset as $toolQuestion) {
                if (! in_array($toolQuestion->id, $processedIds)) {
                    foreach ($toolQuestion->subSteps as $subStep) {
                        CompletedSubStep::allInputSources()
                            ->where('building_id', $this->building->id)
                            ->whereIn('input_source_id', [$this->masterInputSource->id, $this->currentInputSource->id])
                            ->where('sub_step_id', $subStep->id)
                            ->delete();

                        CompletedStep::allInputSources()
                            ->where('building_id', $this->building->id)
                            ->whereIn('input_source_id', [$this->masterInputSource->id, $this->currentInputSource->id])
                            ->where('step_id', $subStep->step_id)
                            ->delete();
                    }

                    $processedIds[] = $toolQuestion->id;
                }
            }
        }
    }

    private function clearAnswer(ToolQuestion $toolQuestion, array $where)
    {
        if (is_null($toolQuestion->save_in)) {
            $toolQuestion->toolQuestionAnswers()
                ->allInputSources()
                ->whereIn('input_source_id', [$this->masterInputSource->id, $this->currentInputSource->id])
                ->where($where)
                ->delete();
        }

        //TODO: check how to clear save_in type answers when needed, just nulling it is not something we can do
        // since not every column is nullable
    }
}