<?php

namespace App\Http\Livewire\Cooperation\Frontend\Tool\SimpleScan\MyPlan;

use App\Helpers\Arr;
use App\Helpers\DataTypes\Caster;
use App\Helpers\ToolQuestionHelper;
use App\Models\Building;
use App\Models\InputSource;
use App\Models\ToolQuestion;
use App\Services\BuildingService;
use App\Services\ConditionService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Collection;
use Livewire\Component;

class CalculationsTable extends Component
{
    use AuthorizesRequests;

    public Collection $toolQuestions;
    public array $answers;
    public Building $building;
    public InputSource $masterInputSource;

    private $toolQuestionShorts = [
        'amount-gas', 'amount-electricity', 'resident-count', 'water-comfort',
    ];

    public function mount(Building $building)
    {
        $this->building = $building;
        $this->masterInputSource = InputSource::findByShort(InputSource::MASTER_SHORT);
        $this->setToolQuestions();
        $this->setAnswers();
    }

    public function render()
    {
        return view('livewire.cooperation.frontend.tool.simple-scan.my-plan.calculations-table');
    }

    protected function setToolQuestions()
    {
        $this->toolQuestions = ToolQuestion::findByShorts($this->toolQuestionShorts);

        $conditionService = ConditionService::init()
            ->building($this->building)
            ->inputSource($this->masterInputSource);

        foreach ($this->toolQuestions as $index => $toolQuestion) {
            if (! $conditionService->forModel($toolQuestion)->isViewable()) {
                unset($this->toolQuestions[$index]);
            }
        }
    }

    protected function setAnswers()
    {
        $answers = BuildingService::init($this->building)->getSourcedAnswers($this->toolQuestions)->toArray();

        foreach ($this->toolQuestions as $toolQuestion) {
            if (array_key_exists($toolQuestion->id, $answers)) {
                $answerToMakeReadable = $toolQuestion->data_type === Caster::ARRAY
                    ? Arr::pluck($answers[$toolQuestion->id], 'answer')
                    : Arr::first($answers[$toolQuestion->id])['answer'];

                $this->answers[$toolQuestion->short]['answer'] = ToolQuestionHelper::getHumanReadableAnswer(
                    $this->building, $this->masterInputSource, $toolQuestion, true, $answerToMakeReadable
                );
                $this->answers[$toolQuestion->short]['input_source_name'] = Arr::first($answers[$toolQuestion->id])['input_source_name'];

                if (in_array($toolQuestion->data_type, [Caster::INT, Caster::INT_5, Caster::FLOAT])) {
                    $this->answers[$toolQuestion->short]['answer'] = Caster::init(
                        $toolQuestion->data_type, $this->answers[$toolQuestion->short]['answer']
                    )->getFormatForUser();
                }

                if (! empty($toolQuestion->unit_of_measure)) {
                    $this->answers[$toolQuestion->short]['answer'] .= " {$toolQuestion->unit_of_measure}";
                }
            }
        }
    }
}
