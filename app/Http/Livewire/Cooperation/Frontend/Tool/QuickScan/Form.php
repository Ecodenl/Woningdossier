<?php

namespace App\Http\Livewire\Cooperation\Frontend\Tool\QuickScan;

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
        $answers = collect();
        foreach ($this->toolQuestions as $toolQuestion) {
            $answers->push([$toolQuestion->short => $this->filledInAnswers[$toolQuestion->id]]);
        }

        foreach ($this->toolQuestions as $index => $toolQuestion) {
            if (!empty($toolQuestion->conditions)) {
                foreach ($toolQuestion->conditions as $condition) {
                    // there is a possibility the user fills the form in a unexpected order,
                    // so we have to check if the field which should match the condition is actually answered.
                    // may have to change in the future if there is some null condition thing.
                    if ($answers->where($condition['column'], '!=', null)->count() > 0) {
                        // now execute the actual condition
                        $answer = $answers->where($condition['column'], $condition['operator'], $condition['value'])->first();
                        // so this means the answer is not found, this means we have to remove the question.
                        if ($answer === null) {
                            $this->toolQuestions = $this->toolQuestions->forget($index);
                            // and unset the validation for the question.
                            unset($this->rules["filledInAnswers.{$toolQuestion->id}"]);
                        }
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
            ], $this->rules);

            if ($validator->fails()) {
                $this->setToolQuestions();
            }

            $validator->validate();
        }

        foreach ($this->filledInAnswers as $toolQuestionId => $givenAnswer) {
            /** @var ToolQuestion $toolQuestion */
            $toolQuestion = ToolQuestion::find($toolQuestionId);
            if (is_null($toolQuestion->save_in)) {
                $this->saveToolQuestionCustomValues($toolQuestion, $givenAnswer);
            } else {
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

            $answersForInputSource = $this->building->getAnswers($this->masterInputSource, $toolQuestion);

            foreach ($answersForInputSource as $answerForInputSource) {

                switch ($toolQuestion->toolQuestionType->short) {
                    case 'rating-slider':
                        $filledInAnswerOptions = json_decode($answerForInputSource, true);
                        foreach ($toolQuestion->options as $option) {

                            $this->filledInAnswers[$toolQuestion->id][$option['short']] = $filledInAnswerOptions[$option['short']] ?? 0;
                            $this->rules["filledInAnswers.{$toolQuestion->id}.{$option['short']}"] = $toolQuestion->validation;
                        }
                        break;
                    case 'slider':
                        // default it when no answer is set, otherwise if the user leaves it default and submit the validation will fail because nothing is set.
                        $this->filledInAnswers[$toolQuestion->id] = $answerForInputSource ?? $toolQuestion->options['value'];
                        $this->rules["filledInAnswers.{$toolQuestion->id}"] = $toolQuestion->validation;
                        break;
                    default:
                        $this->filledInAnswers[$toolQuestion->id][] = $answerForInputSource;
                        $this->rules["filledInAnswers.{$toolQuestion->id}"] = $toolQuestion->validation;
                }
            }
        }
    }

    private function saveToolQuestionValuables(ToolQuestion $toolQuestion, $givenAnswer)
    {
        $savedInParts = explode('.', $toolQuestion->save_in);
        $table = $savedInParts[0];
        $column = $savedInParts[1];

        if (Schema::hasColumn($table, 'user_id')) {
            $where = ['user_id' => $this->building->user_id];
        } else {
            $where = ['building_id' => $this->building->id];
        }

        // this means we have to add some thing to the where
        if (count($savedInParts) > 2) {
            // in this case the column holds a extra where value
            $where[ToolQuestionHelper::TABLE_COLUMN[$table]] = $column;
            $column = $savedInParts[2];
            // the extra column holds a array / json, so we have to transform the answer into a
            if ($savedInParts[2] == 'extra') {
                // the column to which we actually have to save the data
                $column = 'extra';
                // in this case, the fourth index holds the json key.
                $givenAnswer = [$savedInParts[3] => $givenAnswer];
            }
        }

        // we will save it on the model, this way we keep the current events behind them
        $modelName = "App\\Models\\" . Str::ucFirst(Str::camel(Str::singular($table)));

        $where['input_source_id'] = $this->currentInputSource->id;
        // now save it for both input sources.
        $modelName::allInputSources()
            ->updateOrCreate(
                $where,
                [$column => $givenAnswer]
            );

        $where['input_source_id'] = $this->masterInputSource->id;
        $modelName::allInputSources()
            ->updateOrCreate(
                $where,
                [$column => $givenAnswer]
            );
    }

    private function saveToolQuestionCustomValues(ToolQuestion $toolQuestion, $givenAnswer)
    {
        if (is_array($givenAnswer)) {
            $givenAnswer = json_encode($givenAnswer);
        }
        $where = [
            'building_id' => $this->building->id,
            'tool_question_id' => $toolQuestion->id,
        ];
        $data = [
            'building_id' => $this->building->id,
            'input_source_id' => $this->currentInputSource->id,
            'answer' => $givenAnswer,
        ];
        // Try to resolve the id is the question has custom values
        if ($toolQuestion->toolQuestionCustomValues()->exists()) {
            // if so, the given answer contains a short.
            $toolQuestionCustomValue = ToolQuestionCustomValue::findByShort($givenAnswer);
            $data['tool_question_custom_value_id'] = $toolQuestionCustomValue->id;
        }

        $where['input_source_id'] = $this->currentInputSource->id;
        // we have to do this twice, once for the current input source and once for the master input source
        $toolQuestion
            ->toolQuestionAnswers()
            ->allInputSources()
            ->updateOrCreate($where, $data)
            ->save();
        $where['input_source_id'] = $this->masterInputSource->id;
        $data['input_source_id'] = $this->masterInputSource->id;
        $toolQuestion
            ->toolQuestionAnswers()
            ->allInputSources()
            ->updateOrCreate($where, $data)
            ->save();
    }
}
