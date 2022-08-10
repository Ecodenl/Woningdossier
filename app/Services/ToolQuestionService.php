<?php

namespace App\Services;

use App\Helpers\Arr;
use App\Helpers\ToolQuestionHelper;
use App\Jobs\ApplyExampleBuildingForChanges;
use App\Models\Building;
use App\Models\InputSource;
use App\Models\ToolQuestion;
use App\Models\ToolQuestionCustomValue;
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
            ToolQuestionService::init($this->toolQuestion)
                ->building($this->building)
                ->currentInputSource($this->currentInputSource)
                ->saveToolQuestionCustomValues($givenAnswer);
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

        // we can't do a update or create, we just have to delete the old answers and create the new one.
        if ($this->toolQuestion->toolQuestionType->short == 'checkbox-icon') {

            $this->toolQuestion->toolQuestionAnswers()
                ->allInputSources()
                ->where($where)
                ->whereIn('input_source_id', [$this->masterInputSource->id, $this->currentInputSource->id])
                ->delete();

            foreach ($givenAnswer as $answer) {
                $toolQuestionCustomValue = ToolQuestionCustomValue::findByShort($answer);
                $data['tool_question_custom_value_id'] = $toolQuestionCustomValue->id;
                $data['answer'] = $answer;
                $this->toolQuestion->toolQuestionAnswers()->create($data);
            }

        } else {
            if (is_array($givenAnswer)) {
                $givenAnswer = json_encode($givenAnswer);
            }

            // Try to resolve the id is the question has custom values
            if ($this->toolQuestion->toolQuestionCustomValues()->exists()) {
                // if so, the given answer contains a short.
                $toolQuestionCustomValue = ToolQuestionCustomValue::findByShort($givenAnswer);
                $data['tool_question_custom_value_id'] = $toolQuestionCustomValue->id;
            }

            $data['answer'] = $givenAnswer;
            $where['input_source_id'] = $this->currentInputSource->id;
            // we have to do this twice, once for the current input source and once for the master input source
            $this->toolQuestion
                ->toolQuestionAnswers()
                ->allInputSources()
                ->updateOrCreate($where, $data);
        }
    }

    public function saveToolQuestionValuables($givenAnswer)
    {
        $toolQuestion = $this->toolQuestion;

        $saveIn = ToolQuestionHelper::resolveSaveIn($toolQuestion, $this->building);
        $table  = $saveIn['table'];
        $column = $saveIn['column'];
        $where  = $saveIn['where'];

        $where[] = ['input_source_id', '=', $this->currentInputSource->id];

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
    }
}