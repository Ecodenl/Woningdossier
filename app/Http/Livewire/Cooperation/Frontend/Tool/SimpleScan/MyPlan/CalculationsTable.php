<?php

namespace App\Http\Livewire\Cooperation\Frontend\Tool\SimpleScan\MyPlan;

use App\Calculations\Heater;
use App\Helpers\Arr;
use App\Helpers\DataTypes\Caster;
use App\Helpers\Kengetallen;
use App\Helpers\ToolQuestionHelper;
use App\Models\Building;
use App\Models\InputSource;
use App\Models\ToolCalculationResult;
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
    public Collection $toolCalculationResults;
    public array $tableData;
    public Building $building;
    public InputSource $masterInputSource;

    private array $fixedData = [
        'cost-gas' => [
            'name' => 'cooperation/frontend/tool.my-plan.calculations.values.gas-cost',
            'value' => Kengetallen::EURO_SAVINGS_GAS,
            'source' => 'RVO',
        ],
        'cost-electricity' => [
            'name' => 'cooperation/frontend/tool.my-plan.calculations.values.electricity-cost',
            'value' => Kengetallen::EURO_SAVINGS_ELECTRICITY,
            'source' => 'RVO',
        ],
    ];

    private array $toolQuestionShorts = [
        'amount-gas', 'amount-electricity', 'resident-count', 'water-comfort', 'build-year',
        'insulation-wall-surface', 'insulation-floor-surface', 'total-window-surface',
        'pitched-roof-surface', 'flat-roof-surface', 'desired-solar-panel-count',
        'new-heat-source', 'new-heat-source-warm-tap-water', 'new-cook-type', 'boiler-type', 'heat-pump-type',
    ];

    private array $toolCalculationResultShorts = [
        'sun-boiler.specs.size_boiler', 'sun-boiler.specs.size_collector',
    ];

    public function mount(Building $building)
    {
        $this->building = $building;
        $this->masterInputSource = InputSource::findByShort(InputSource::MASTER_SHORT);
        $this->setModels();
        $this->setTableData();
    }

    public function render()
    {
        return view('livewire.cooperation.frontend.tool.simple-scan.my-plan.calculations-table');
    }

    protected function setModels()
    {
        $this->toolQuestions = ToolQuestion::findByShorts($this->toolQuestionShorts);
        $this->toolCalculationResults = ToolCalculationResult::findByShorts($this->toolCalculationResultShorts);

        $conditionService = ConditionService::init()
            ->building($this->building)
            ->inputSource($this->masterInputSource);

        foreach ($this->toolQuestions as $index => $toolQuestion) {
            if (! $conditionService->forModel($toolQuestion)->isViewable()) {
                unset($this->toolQuestions[$index]);
            }
        }
        foreach ($this->toolCalculationResults as $index => $toolCalculationResult) {
            if (! $conditionService->forModel($toolCalculationResult)->isViewable()) {
                unset($this->toolCalculationResults[$index]);
            }
        }
    }

    protected function setTableData()
    {
        $answers = BuildingService::init($this->building)->getSourcedAnswers($this->toolQuestions)->toArray();

        $fixedData = $this->fixedData;
        foreach ($fixedData as $index => $data) {
            $data['name'] = __($data['name']);
            $fixedData[$index] = $data;
        }

        $this->tableData = $fixedData;

        foreach ($this->toolQuestions as $toolQuestion) {
            if (array_key_exists($toolQuestion->id, $answers)) {
                $firstKey = array_key_first($answers[$toolQuestion->id]);

                $answerToMakeReadable = $toolQuestion->data_type === Caster::ARRAY
                    ? Arr::pluck($answers[$toolQuestion->id], 'answer')
                    : $answers[$toolQuestion->id][$firstKey]['answer'] ?? null;

                // Answer might be null, e.g. roof type can have null surface if for example created via mapping
                if (! is_null($answerToMakeReadable)) {
                    $this->tableData[$toolQuestion->short]['name'] = $toolQuestion->name;
                    $this->tableData[$toolQuestion->short]['value'] = ToolQuestionHelper::getHumanReadableAnswer(
                        $this->building, $this->masterInputSource, $toolQuestion, true, $answerToMakeReadable
                    );
                    $this->tableData[$toolQuestion->short]['source'] = $answers[$toolQuestion->id][$firstKey]['input_source_name'] ?? null;

                    if (in_array($toolQuestion->data_type, [Caster::INT, Caster::INT_5, Caster::FLOAT])) {
                        $this->tableData[$toolQuestion->short]['value'] = Caster::init(
                            $toolQuestion->data_type, $this->tableData[$toolQuestion->short]['value']
                        )->getFormatForUser();
                    }

                    if (! empty($toolQuestion->unit_of_measure)) {
                        $this->tableData[$toolQuestion->short]['value'] .= " {$toolQuestion->unit_of_measure}";
                    }
                }
            }
        }

        if ($this->toolCalculationResults->isNotEmpty()) {
            // TODO: make this into some sort of service
            $sunBoilerResults = Heater::calculate($this->building, $this->masterInputSource);

            foreach ($this->toolCalculationResults as $toolCalculationResult) {
                $this->tableData[$toolCalculationResult->short]['name'] = $toolCalculationResult->name;
                $this->tableData[$toolCalculationResult->short]['value'] = $sunBoilerResults[$toolCalculationResult->short] ?? null;
                $this->tableData[$toolCalculationResult->short]['source'] = "Berekeningen";
            }
        }
    }
}
