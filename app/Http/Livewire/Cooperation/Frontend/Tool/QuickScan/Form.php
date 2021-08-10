<?php

namespace App\Http\Livewire\Cooperation\Frontend\Tool\QuickScan;

use App\Helpers\HoomdossierSession;
use App\Helpers\QuickScanHelper;
use App\Helpers\StepHelper;
use App\Helpers\ToolQuestionHelper;
use App\Models\Building;
use App\Models\CompletedSubStep;
use App\Models\InputSource;
use App\Models\Question;
use App\Models\Step;
use App\Models\SubStep;
use App\Models\ToolQuestion;
use App\Models\ToolQuestionCustomValue;
use App\Models\User;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Livewire\Component;
use function Clue\StreamFilter\fun;

class Form extends Component
{
    /*
     *
     * NOTE: When programmatically updating variables, ensure the updated method is called! This triggers a browser
     * event, which can be caught by the frontend and set visuals correct, e.g. with the sliders.
     *
     */

    protected $listeners = ['update', 'updated', 'save',];

    private $rules;

    /** @var Building */
    public $building;

    public $masterInputSource;
    public $currentInputSource;

    public $step;
    public $subStep;

    public $toolQuestions;

    public $filledInAnswers = [];

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

    public function update($field, $value, $triggerUpdate = true)
    {
        // If we should, tell Livewire we're updating
        if ($triggerUpdate) {
            $oldValue = $this->getPropertyValue($field);
            $this->updating($field, $oldValue);
        }

        // Set value the same way that Livewire retrieves its properties
        $variable = $this->beforeFirstDot($field);

        if ($this->containsDots($field)) {
            data_set($this->{$variable}, $this->afterFirstDot($field), $value);
        } else {
            $this->{$variable} = $value;
        }

        // If we should, tell Livewire we have updated
        if ($triggerUpdate) {
            $this->updated($field, $value);
        }
    }

    public function updated($field, $value)
    {
        // TODO: Deprecate this dispatch in Livewire V2
        $this->dispatchBrowserEvent('element:updated', ['field' => $field, 'value' => $value]);

        // Filter out the questions that do not match the condition
        $this->toolQuestions = $this->subStep->toolQuestions()->orderBy('order')->get();

        // now collect the given answers
        $answers = collect();
        foreach ($this->toolQuestions as $toolQuestion) {
            $answers->push([$toolQuestion->short => $this->filledInAnswers[$toolQuestion->id]]);
        }

        foreach ($this->toolQuestions as $index => $toolQuestion) {
            if (!empty($toolQuestion->conditions)) {
                foreach ($toolQuestion->conditions as $condition) {
                    $answer = $answers->where($condition['column'], $condition['operator'], $condition['value'])->first();
                    // so this means the answer is not found, this means we have to remove the question.
                    if ($answer === null) {
                        $this->toolQuestions = $this->toolQuestions->forget($index);
                    }
                }
            }
        }
    }

    public function save($nextUrl)
    {
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

        return redirect()->to($nextUrl);
    }

    private function setFilledInAnswers()
    {
        // base key where every answer is stored
        foreach ($this->toolQuestions as $index => $toolQuestion) {
            $validationKeys[$index][] = 'filledInAnswers';
            $validationKeys[$index][] = $toolQuestion->id;

            $answerForInputSource = $this->building->getAnswer($this->masterInputSource, $toolQuestion);
            if ($toolQuestion->toolQuestionType->short == 'rating-slider') {
                foreach ($toolQuestion->options as $option) {
                    $this->filledInAnswers[$toolQuestion->id][$option['short']] = $answerForInputSource;
                    $validationKeys[$index] = $option['short'];

                }
            } else {
                $this->filledInAnswers[$toolQuestion->id] = $answerForInputSource;
            }

            $this->rules[implode('.', $validationKeys[$index])] = $toolQuestion->validation;
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
//            $data['tool_question_custom_value_id'] = $toolQuestionCustomValue->id;
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
