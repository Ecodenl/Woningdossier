<?php

namespace App\Livewire\Cooperation\Frontend\Tool\SimpleScan\MyPlan;

use App\Calculations\Heater;
use App\Helpers\Arr;
use App\Helpers\DataTypes\Caster;
use App\Helpers\Kengetallen;
use App\Helpers\KengetallenCodes;
use App\Helpers\ToolQuestionHelper;
use App\Models\Building;
use App\Models\InputSource;
use App\Models\ToolCalculationResult;
use App\Models\ToolQuestion;
use App\Services\ConditionService;
use App\Services\Kengetallen\KengetallenService;
use App\Services\Kengetallen\Resolvers\BuildingDefined;
use App\Services\Models\BuildingService;
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
    private KengetallenService $kengetallenService;

    private array $fixedData = [];

    private array $toolQuestionShorts = [
        'gas-price-euro',
        'electricity-price-euro',
        'amount-gas',
        'amount-electricity',
        'resident-count',
        'water-comfort',
        'build-year',
        'insulation-wall-surface',
        'insulation-floor-surface',
        'total-window-surface',
        'pitched-roof-surface',
        'flat-roof-surface',
        'desired-solar-panel-count',
        'new-heat-source',
        'new-heat-source-warm-tap-water',
        'new-cook-type',
        'boiler-type',
        'heat-pump-type',
    ];

    private array $toolCalculationResultShorts = [
        'sun-boiler.specs.size_boiler',
        'sun-boiler.specs.size_collector',
    ];

    public function mount(Building $building, KengetallenService $kengetallenService)
    {
        $this->kengetallenService = $kengetallenService;
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
        $this->toolQuestions = ToolQuestion::findByShortsOrdered($this->toolQuestionShorts)->get();
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

        // determine where the kengetallen would come from
        // currently (time of writng) there are only 2 "definers"
        // so its either user input or rvo / code defined
        // if in the future the cooperation would be added this has to ben herzien.
        $kengetallenService = $this->kengetallenService->forInputSource($this->masterInputSource)->forBuilding($this->building);
        $euroSavingsGasDefiner = $kengetallenService->explain(KengetallenCodes::EURO_SAVINGS_GAS);
        if ($euroSavingsGasDefiner instanceof BuildingDefined) {
            $this->toolQuestionShorts[] = 'gas-price-euro';
        } else {
            $this->tableData['cost-gas'] = [
                'name' => __('cooperation/frontend/tool.my-plan.calculations.values.gas-cost'),
                'value' => $kengetallenService->resolve(KengetallenCodes::EURO_SAVINGS_GAS) . '€ / m<sup>3</sup>',
                'source' => 'RVO'
            ];
        }
        $euroSavingsElectricityDefiner = $kengetallenService->explain(KengetallenCodes::EURO_SAVINGS_ELECTRICITY);
        if ($euroSavingsElectricityDefiner instanceof BuildingDefined) {
            $this->toolQuestionShorts[] = 'electricity-price-euro';
        } else {
            $this->tableData['cost-electricity'] = [
                'name' => __('cooperation/frontend/tool.my-plan.calculations.values.electricity-cost'),
                'value' => $kengetallenService->resolve(KengetallenCodes::EURO_SAVINGS_ELECTRICITY) . ' € / kWh',
                'source' => 'RVO'
            ];
        }

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
                        $this->building,
                        $this->masterInputSource,
                        $toolQuestion,
                        true,
                        $answerToMakeReadable
                    );
                    $this->tableData[$toolQuestion->short]['source'] = $answers[$toolQuestion->id][$firstKey]['input_source_name'] ?? null;

                    if (in_array($toolQuestion->data_type, [Caster::INT, Caster::INT_5, Caster::FLOAT])) {
                        $this->tableData[$toolQuestion->short]['value'] = Caster::init()
                            ->dataType($toolQuestion->data_type)
                            ->value($this->tableData[$toolQuestion->short]['value'])
                            ->getFormatForUser();
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
                $answer = $sunBoilerResults[$toolCalculationResult->short] ?? null;

                if (! is_null($answer)) {
                    $this->tableData[$toolCalculationResult->short]['name'] = $toolCalculationResult->name;
                    $this->tableData[$toolCalculationResult->short]['value'] = $answer;

                    if (! empty($toolCalculationResult->unit_of_measure)) {
                        $this->tableData[$toolCalculationResult->short]['value'] .= " {$toolCalculationResult->unit_of_measure}";
                    }

                    $this->tableData[$toolCalculationResult->short]['source'] = "Berekeningen";
                }
            }
        }
    }
}
