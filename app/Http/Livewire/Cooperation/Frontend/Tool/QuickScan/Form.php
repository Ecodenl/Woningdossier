<?php

namespace App\Http\Livewire\Cooperation\Frontend\Tool\QuickScan;

use App\Helpers\HoomdossierSession;
use App\Helpers\ToolQuestionHelper;
use App\Models\Building;
use App\Models\InputSource;
use App\Models\Step;
use App\Models\SubStep;
use App\Models\ToolQuestion;
use Illuminate\Support\Str;
use Livewire\Component;

class Form extends Component
{
    protected $listeners = ['save'];

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
        $this->toolQuestions = $subStep->toolQuestions;
        $this->masterInputSource = InputSource::findByShort(InputSource::MASTER_SHORT);
        $this->currentInputSource = HoomdossierSession::getInputSource(true);

        $this->setFilledInAnswers();
    }

    private function setFilledInAnswers()
    {
        foreach ($this->toolQuestions as $toolQuestion) {

            $answerForInputSource = $this->building->getAnswer($this->currentInputSource, $toolQuestion);

            $this->filledInAnswers[$toolQuestion->id] = $answerForInputSource;
        }
    }

    public function render()
    {
        return view('livewire.cooperation.frontend.tool.quick-scan.form');
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
        return redirect()->to($nextUrl);
    }

    private function saveToolQuestionValuables(ToolQuestion $toolQuestion, $givenAnswer)
    {
        $savedInParts = explode('.', $toolQuestion->save_in);
        $table = $savedInParts[0];
        $column = $savedInParts[1];

        $where = ['input_source_id' => $this->currentInputSource->id, 'building_id' => $this->building->id];

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

        // now save it for both input sources.
        $modelName::allInputSources()
            ->updateOrCreate(
                $where,
                [$column => $givenAnswer]
            );

        $modelName::allInputSources()
            ->updateOrCreate(
                $where,
                [$column => $givenAnswer]
            );
    }


    private function saveToolQuestionCustomValues(ToolQuestion $toolQuestion, $givenAnswer)
    {
        // we have to do this twice, once for the current input source and once for the master input source
        $toolQuestion
            ->toolQuestionAnswers()
            ->create([
                'building_id' => $this->building->id,
                'input_source_id' => $this->currentInputSource->id,
                'tool_question_custom_value_id' => $givenAnswer,

            ])
            ->replicate(['input_source_id'])
            ->fill(['input_source_id' => $this->masterInputSource->id])
            ->save();
    }
}
