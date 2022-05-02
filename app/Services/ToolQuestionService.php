<?php

namespace App\Services;

use App\Models\Building;
use App\Models\InputSource;
use App\Models\ToolQuestion;
use App\Models\ToolQuestionCustomValue;
use App\Traits\FluentCaller;

class ToolQuestionService {

    use FluentCaller;

    public $building;
    public $toolQuestion;
    public $currentInputSource;
    public $masterInputSource;

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
}