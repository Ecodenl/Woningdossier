<?php

namespace App\Livewire\Cooperation\Frontend\Tool\ExpertScan;

use App\Calculations\Heater;
use App\Calculations\HeatPump;
use App\Calculations\HighEfficiencyBoiler;
use App\Console\Commands\Tool\RecalculateForUser;
use App\Helpers\Arr;
use App\Helpers\Conditions\Clause;
use App\Helpers\Conditions\ConditionEvaluator;
use App\Helpers\DataTypes\Caster;
use App\Helpers\Hoomdossier;
use App\Helpers\ToolQuestionHelper;
use App\Livewire\Cooperation\Frontend\Tool\Scannable;
use App\Models\CompletedSubStep;
use App\Models\Cooperation;
use App\Models\Step;
use App\Models\ToolCalculationResult;
use App\Models\ToolQuestion;
use App\Services\Scans\ScanFlowService;
use App\Services\ToolQuestionService;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class Form extends Scannable
{
    public Step $step;
    public Collection $subSteps;

    public array $calculationResults = [];
    public array $failedValidationForSubSteps = [];

    public bool $loading = false;

    protected $listeners = [
        'save',
    ];

    public function mount(Step $step, Cooperation $cooperation)
    {
        $this->subSteps = $step->subSteps()->with([
            'toolQuestions' => function ($query) {
                $query->with('forSpecificInputSource');
            },
            'subStepTemplate',
        ])->get();

        $this->build();
        $this->performCalculations();
    }

    public function render()
    {
        if ($this->loading) {
            $this->dispatch('input-updated');
        }
        return view('livewire.cooperation.frontend.tool.expert-scan.form');
    }

    public function getSubSteppablesProperty()
    {
        $subSteppables = collect();
        foreach ($this->subSteps as $subStep) {
            $subSteppables = $subSteppables->merge($subStep->subSteppables()->orderBy('order')
                ->with(['subSteppable', 'toolQuestionType'])
                ->get());
        }

        return $subSteppables;
    }

    public function getToolQuestionsProperty()
    {
        $toolQuestions = collect();
        foreach ($this->subSteps as $subStep) {
            // Eager loaded in hydration
            $toolQuestions = $toolQuestions->merge($subStep->toolQuestions);
        }

        return $toolQuestions;
    }

    public function inputUpdated()
    {
        $this->loading = true;
    }

    public function updated($field, $value)
    {
        parent::updated($field, $value);

        $this->failedValidationForSubSteps = [];
        $this->performCalculations();
        $this->dispatch('input-update-processed');
        $this->loading = false;
    }

    public function save()
    {
        // Before we can validate (and save), we must reset the formatting from text to mathable
        foreach ($this->toolQuestions as $toolQuestion) {
            if ($toolQuestion->data_type === Caster::FLOAT) {
                $this->filledInAnswers[$toolQuestion->short] = Caster::init()
                    ->dataType($toolQuestion->data_type)
                    ->value($this->filledInAnswers[$toolQuestion->short])
                    ->reverseFormatted();
            }
        }

        if (! empty($this->rules)) {
            $validator = Validator::make([
                'filledInAnswers' => $this->filledInAnswers
            ], $this->rules, [], $this->attributeTranslations);

            // Translate values also (otherwise we get weird translations for e.g. a required_if)
            $defaultValues = __('validation.values.defaults');

            foreach ($this->filledInAnswers as $toolQuestionId => $answer) {
                $validator->addCustomValues([
                    "filledInAnswers.{$toolQuestionId}" => $defaultValues,
                ]);
            }

            foreach ($this->toolQuestions as $toolQuestion) {
                if (in_array($toolQuestion->data_type, [Caster::INT, Caster::FLOAT])) {
                    $this->filledInAnswers[$toolQuestion->short] = Caster::init()
                        ->dataType($toolQuestion->data_type)
                        ->value($this->filledInAnswers[$toolQuestion->short])
                        ->getFormatForUser();
                }
            }

            if ($validator->fails()) {
                foreach ($validator->errors()->messages() as $field => $messages) {
                    $short = explode('.', $field, 2)[1];
                    $tq = $this->toolQuestions->where('short', $short)->first();

                    $subStepId = $tq->pivot->sub_step_id;
                    if (! array_key_exists($subStepId, $this->failedValidationForSubSteps)) {
                        $this->failedValidationForSubSteps[$subStepId] = $this->subSteps->where('id', $subStepId)
                            ->first()
                            ->name;
                    }
                }


                // Validator failed, let's put it back as the user format
                // notify the main form that validation failed for this particular sub step.

                $this->dispatch('validation-failed');
            }

            $validator->validate();
        }

        // Turns out, default values exist! We need to check if the tool questions have answers, else
        // they might not save...
        if (! $this->dirty) {
            foreach ($this->filledInAnswers as $toolQuestionShort => $givenAnswer) {
                $toolQuestion = ToolQuestion::findByShort($toolQuestionShort);

                // Define if we should check this question...
                if ($this->building->user->account->can('answer', $toolQuestion)) {
                    $currentAnswer = $this->building->getAnswer($toolQuestion->forSpecificInputSource ?? $this->currentInputSource, $toolQuestion);
                    $masterAnswer = $this->building->getAnswer($this->masterInputSource, $toolQuestion);

                    // Master input source is important. Ensure both are set
                    if (is_null($currentAnswer) || is_null($masterAnswer)) {
                        $this->setDirty(true);
                        break;
                    }
                }
            }
        }

        $this->saveFilledInAnswers();
    }

    public function saveFilledInAnswers()
    {
        if ($this->dirty) {

            $stepShortsToRecalculate = [];
            $shouldDoFullRecalculate = false;
            $dirtyToolQuestions = [];

            // Answers have been updated, we save them and dispatch a recalculate
            // at this point we already know that the form is dirty, otherwise this event wouldnt have been dispatched
            foreach ($this->filledInAnswers as $toolQuestionShort => $givenAnswer) {
                // Rules are conditionally unset. We don't want to save unvalidated answers, but don't want to just
                // clear them either.
                if (array_key_exists("filledInAnswers.$toolQuestionShort", $this->rules)) {
                    // Define if we should answer this question...
                    /** @var ToolQuestion $toolQuestion */
                    $toolQuestion = ToolQuestion::findByShort($toolQuestionShort);
                    if ($this->building->user->account->can('answer', $toolQuestion)) {

                        // Although redundant, this step is necessary.
                        // The sub steppable component 'reverseFormats' converts the data back to a human-readable form.
                        // So when we actually start saving it we have to format it one more time
                        if ($toolQuestion->data_type === Caster::FLOAT) {
                            $givenAnswer = Caster::init()
                                ->dataType($toolQuestion->data_type)
                                ->value($givenAnswer)
                                ->reverseFormatted();
                        }

                        // TODO: this is a horrible way to trace dirty answers
                        $masterAnswer = $this->building->getAnswer($this->masterInputSource, $toolQuestion);
                        if ($masterAnswer !== $givenAnswer) {
                            $dirtyToolQuestions[$toolQuestion->short] = $toolQuestion;
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
                $completedSubStep = CompletedSubStep::firstOrCreate([
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
        $evaluator->setAnswers($evaluator->getToolAnswersForConditions($conditions, collect($this->filledInAnswers)));

        $calculations = [];
        $calculate = [
            'hr-boiler' => HighEfficiencyBoiler::class,
            'sun-boiler' => Heater::class,
            'heat-pump' => HeatPump::class,
        ];

        foreach ($calculate as $short => $calculator) {
            $performedCalculations = [];
            $conditions = $this->getCalculatorConditions($short);
            if ($evaluator->evaluate($conditions)) {
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

        $this->calculationResults = $calculations;
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
