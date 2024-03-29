<?php

namespace App\Http\Livewire\Cooperation\Frontend\Tool\ExpertScan;

use App\Calculations\Heater;
use App\Calculations\HeatPump;
use App\Calculations\HighEfficiencyBoiler;
use App\Console\Commands\Tool\RecalculateForUser;
use App\Helpers\Arr;
use App\Helpers\Conditions\Clause;
use App\Helpers\Conditions\ConditionEvaluator;
use App\Helpers\DataTypes\Caster;
use App\Helpers\Hoomdossier;
use App\Helpers\HoomdossierSession;
use App\Helpers\Str;
use App\Helpers\ToolQuestionHelper;
use App\Models\Building;
use App\Models\CompletedSubStep;
use App\Models\Cooperation;
use App\Models\InputSource;
use App\Models\Step;
use App\Models\ToolCalculationResult;
use App\Models\ToolQuestion;
use App\Services\Scans\ScanFlowService;
use App\Services\ToolQuestionService;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class Form extends Component
{
    public Step $step;
    public Collection $subSteps;
    public string $locale;

    public array $filledInAnswers = [];
    public Building $building;
    public InputSource $masterInputSource;
    public InputSource $currentInputSource;

    public array $rules = [];
    public array $succeededSubSteps = [];
    public array $failedValidationForSubSteps = [];

    public Cooperation $cooperation;

    protected $listeners = [
        'subStepValidationSucceeded' => 'subStepSucceeded',
        'failedValidationForSubSteps',
        'updateFilledInAnswers',
    ];

    public function mount(Step $step, Cooperation $cooperation)
    {
        $this->subSteps = $step->subSteps;

        $this->locale = app()->getLocale();
        $this->building = HoomdossierSession::getBuilding(true);
        $this->masterInputSource = InputSource::findByShort(InputSource::MASTER_SHORT);
        $this->currentInputSource = HoomdossierSession::getInputSource(true);
    }

    public function render()
    {
        return view('livewire.cooperation.frontend.tool.expert-scan.form');
    }

    public function failedValidationForSubSteps(array $subStep)
    {
        // Unset succeeded sub step because it fails now
        unset($this->succeededSubSteps[$subStep['slug'][$this->locale]]);

        $this->failedValidationForSubSteps[$subStep['slug'][$this->locale]] = $subStep['name'][$this->locale];
        $this->dispatchBrowserEvent('scroll-to-top');
    }

    // We will mark the given sub step as succeeded
    public function subStepSucceeded(array $subStep, array $filledInAnswers, array $rules)
    {
        // Unset failed sub step because it succeeds now
        unset($this->failedValidationForSubSteps[$subStep['slug'][$this->locale]]);

        $this->setFilledInAnswers($filledInAnswers);
        $this->setRules($rules);
        $this->succeededSubSteps[$subStep['slug'][$this->locale]] = $subStep['slug'][$this->locale];

        // Save answers if all sub steps have been successfully answered
        if ($this->allSubStepsSucceeded()) {
            $this->saveFilledInAnswers();
        }
    }

    public function setFilledInAnswers($filledInAnswers)
    {
        // We can't directly set the answers because there will be more than one sub step that is passing
        // answers. array_merge messes up the keys and addition (array + array) causes weird behaviour
        foreach ($filledInAnswers as $toolQuestionShort => $answer) {
            $this->filledInAnswers[$toolQuestionShort] = $answer;
        }
    }

    public function setRules(array $rules)
    {
        foreach ($rules as $short => $rule) {
            $this->rules[$short] = $rule;
        }
    }

    public function updateFilledInAnswers($filledInAnswers)
    {
        $this->setFilledInAnswers($filledInAnswers);
        $this->performCalculations();
    }

    public function allSubStepsSucceeded()
    {
        // Total amount of sub steps should match in both count and slugs
        $allFinished = count($this->succeededSubSteps) == $this->subSteps->count();
        $noDiff = empty(array_diff($this->subSteps->pluck('slug')->toArray(), $this->succeededSubSteps));

        return $allFinished && $noDiff;
    }

    public function saveFilledInAnswers()
    {
        $stepShortsToRecalculate = [];
        $shouldDoFullRecalculate = false;
        $dirtyToolQuestions = [];

        // Answers have been updated, we save them and dispatch a recalculate
        // at this point we already now that the form is dirty, otherwise this event wouldnt have been dispatched
        foreach ($this->filledInAnswers as $toolQuestionShort => $givenAnswer) {
            /** @var ToolQuestion $toolQuestion */
            $toolQuestion = ToolQuestion::findByShort($toolQuestionShort);
            // Rules are conditionally unset. We don't want to save unvalidated answers, but don't want to just
            // clear them either. In the case of a JSON question, the short will have sub-shorts, so we check
            // at least one starts with the tool question short
            if (array_key_exists("filledInAnswers.$toolQuestionShort", $this->rules)
                || ($toolQuestion->data_type === Caster::JSON && Str::arrKeyStartsWith(
                    $this->rules,
                    "filledInAnswers.$toolQuestionShort"
                ))
            ) {
                // Define if we should answer this question...
                $toolQuestion = ToolQuestion::findByShort($toolQuestionShort);
                if ($this->building->user->account->can('answer', $toolQuestion)) {
                    // this is horseshit but is necessary; the sub steppable component reverseFormats and goes back to human readable
                    // so when we actually start saving it we have to format it one more time
                    if ($toolQuestion->data_type === Caster::FLOAT) {
                        $givenAnswer = Caster::init()
                            ->dataType($toolQuestion->data_type)
                            ->value($givenAnswer)
                            ->reverseFormatted();
                    }

                    // TODO: this is a horrible way to trace dirty answers
                    $masterAnswer = $this->building->getAnswer($this->masterInputSource, $toolQuestion);
                    if ($masterAnswer !== $givenAnswer) {
                        $dirtyToolQuestions[$toolQuestion->short] = $givenAnswer;
                    }

                    ToolQuestionService::init()
                        ->toolQuestion($toolQuestion)
                        ->building($this->building)
                        ->currentInputSource($this->currentInputSource)
                        ->applyExampleBuilding()
                        ->save($givenAnswer);

                    if (ToolQuestionHelper::shouldToolQuestionDoFullRecalculate($toolQuestion, $this->building, $this->masterInputSource)) {
                        Log::debug("Question {$toolQuestion->short} should trigger a full recalculate");
                        $shouldDoFullRecalculate = true;
                    }

                    // get the expert step equivalent
                    // we will filter out duplicates later on.
                    $stepShortsToRecalculate = array_merge($stepShortsToRecalculate, ToolQuestionHelper::stepShortsForToolQuestion($toolQuestion, $this->building, $this->masterInputSource));
                }
            }
        }

        $flowService = ScanFlowService::init($this->step->scan, $this->building, $this->currentInputSource)
            ->forStep($this->step);

        // since we are done saving all the filled in answers, we can safely mark the sub steps as completed
        foreach ($this->subSteps as $subStep) {
            // Now mark the sub step as complete
            $completedSubStep = CompletedSubStep::allInputSources()->firstOrCreate([
                'sub_step_id' => $subStep->id,
                'building_id' => $this->building->id,
                'input_source_id' => $this->currentInputSource->id
            ]);

            if ($completedSubStep->wasRecentlyCreated) {
                // No need to check SubSteps that were recently created because they passed conditions
                $flowService->skipSubstep($subStep);
            }
        }

        $flowService->checkConditionals($dirtyToolQuestions, Hoomdossier::user());

        if ($shouldDoFullRecalculate) {
            // We should do a full recalculate because some base value that has impact on every calculation is changed.
            Log::debug("Dispatching full recalculate..");

            Artisan::call(RecalculateForUser::class, [
                '--user' => [$this->building->user->id],
                '--input-source' => [$this->currentInputSource->short],
                // we are doing a full recalculate, we want to keep the user his advices organised as they are at the moment.
                '--with-old-advices' => true,
            ]);

            // only when there are steps to recalculate, otherwise the command would just do a FULL recalculate.
        } elseif (! empty($stepShortsToRecalculate)) {
            $stepShortsToRecalculate = array_unique($stepShortsToRecalculate);
            // since we are just re-calculating specific parts of the tool we do it without the old advices
            // it will keep the advices that are not correlated to the steps we are calculating at their current category and order
            // but it moves the re-calculated advices to the proper column.
            Artisan::call(RecalculateForUser::class, [
                '--user' => [$this->building->user->id],
                '--input-source' => [$this->currentInputSource->short],
                '--step-short' => $stepShortsToRecalculate,
                '--with-old-advices' => false,
            ]);
        }

        return redirect()->to(
            ScanFlowService::init($this->step->scan, $this->building, $this->currentInputSource)
                ->forStep($this->step)
                ->forSubStep($this->subSteps->sortByDesc('order')->first()) // Always last, as expert can't have a next SubStep
                ->resolveNextUrl()
        );
    }

    public function performCalculations()
    {
        $conditions = $this->getCalculatorConditions('hr-boiler');

        $evaluator = ConditionEvaluator::init()
            ->building($this->building)
            ->inputSource($this->masterInputSource);

        // We can reuse these answers because all below calculators use the same questions for their conditional logic
        $evaluatableAnswers = $evaluator->getToolAnswersForConditions($conditions, collect($this->filledInAnswers));

        $calculations = [];
        $calculate = [
            'hr-boiler' => HighEfficiencyBoiler::class,
            'sun-boiler' => Heater::class,
            'heat-pump' => HeatPump::class,
        ];

        foreach ($calculate as $short => $calculator) {
            $performedCalculations = [];
            $conditions = $this->getCalculatorConditions($short);
            if ($evaluator->evaluateCollection($conditions, $evaluatableAnswers)) {
                $performedCalculations = $calculator::calculate($this->building, $this->masterInputSource, collect($this->filledInAnswers));
            }

            foreach (Arr::dot($performedCalculations) as $resultShort => $value) {
                $result = ToolCalculationResult::findByShort("{$short}.{$resultShort}");

                // Could be an unused result
                if ($result instanceof ToolCalculationResult) {
                    Arr::set(
                        $performedCalculations,
                        $resultShort,
                        Caster::init()->dataType($result->data_type)->value($value)->getFormatForUser()
                    );
                }
            }

            $calculations[$short] = $performedCalculations;
        }

        $this->emit('calculationsPerformed', $calculations);
    }

    private function getCalculatorConditions(string $short): array
    {
        return [
            [
                [
                    'column' => 'new-heat-source',
                    'operator' => Clause::CONTAINS,
                    'value' => $short,
                ],
            ],
            [
                [
                    'column' => 'new-heat-source-warm-tap-water',
                    'operator' => Clause::CONTAINS,
                    'value' => $short,
                ],
            ],
        ];
    }
}
