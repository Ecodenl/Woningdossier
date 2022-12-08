<?php

namespace App\Http\Livewire\Cooperation\Frontend\Tool\SimpleScan;

use App\Console\Commands\Tool\RecalculateForUser;
use App\Helpers\DataTypes\Caster;
use App\Helpers\HoomdossierSession;
use App\Helpers\ToolQuestionHelper;
use App\Http\Livewire\Cooperation\Frontend\Tool\Scannable;
use App\Models\CompletedSubStep;
use App\Models\Scan;
use App\Models\Step;
use App\Models\SubStep;
use App\Models\ToolQuestion;
use App\Services\Scans\ScanFlowService;
use App\Services\ToolQuestionService;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class Form extends Scannable
{
    public Scan $scan;
    public Step $step;
    public SubStep $subStep;

    public function mount(Scan $scan, Step $step, SubStep $subStep)
    {
        $this->scan = $scan;
        Log::debug("mounting form [Step: {$step->id}] [SubStep: {$subStep->id}]");


        $subStep->load([
            'subSteppables' => function ($query) {
                $query
                    ->orderBy('order')
                    ->with(['subSteppable', 'toolQuestionType']);
            },
            'toolQuestions' => function ($query) {
                $query->orderBy('order');
            },
            'subStepTemplate',
        ]);

        $this->build();
    }

    public function hydrateToolQuestions()
    {
        $this->rehydrateToolQuestions();
    }

    public function rehydrateToolQuestions()
    {
        $this->toolQuestions = $this->subStep->toolQuestions;
    }

    public function render()
    {
        $this->rehydrateToolQuestions();
        return view('livewire.cooperation.frontend.tool.simple-scan.form');
    }

    public function save()
    {
        $flowService = ScanFlowService::init($this->step->scan, $this->building, $this->currentInputSource)
            ->forStep($this->step)
            ->forSubStep($this->subStep);

        if (HoomdossierSession::isUserObserving()) {
            return redirect()->to($flowService->resolveNextUrl());
        }

        // Before we can validate (and save), we must reset the formatting from text to mathable
        foreach ($this->toolQuestions as $toolQuestion) {
            if ($toolQuestion->data_type === Caster::FLOAT) {
                $this->filledInAnswers[$toolQuestion->short] = Caster::init(
                    $toolQuestion->data_type, $this->filledInAnswers[$toolQuestion->short]
                )->reverseFormatted();
            }
        }


        if (! empty($this->rules)) {
            $validator = Validator::make([
                'filledInAnswers' => $this->filledInAnswers
            ], $this->rules, [], $this->attributes);

            // Translate values also
            $defaultValues = __('validation.values.defaults');

            foreach ($this->filledInAnswers as $toolQuestionShort => $answer) {
                $validator->addCustomValues([
                    "filledInAnswers.{$toolQuestionShort}" => $defaultValues,
                ]);
            }

            if ($validator->fails()) {
                // Validator failed, let's put it back as the user format
                foreach ($this->toolQuestions as $toolQuestion) {
                    if (in_array($toolQuestion->data_type, [Caster::INT, Caster::FLOAT])) {
                        $this->filledInAnswers[$toolQuestion->short] = Caster::init($toolQuestion->data_type, $this->filledInAnswers[$toolQuestion->short])->getFormatForUser();
                    }
                }

                $this->hydrateToolQuestions();

                $this->setValidationForToolQuestions();

                $this->evaluateToolQuestions();

                $this->dispatchBrowserEvent('validation-failed');
            }

            $validator->validate();
        }

        // we will use this to check the conditionals later on.
        $dirtyToolQuestions = [];
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
                        $dirtyToolQuestions[$toolQuestion->short] = $toolQuestion;
                        break;
                    }
                }
            }
        }


        $stepShortsToRecalculate = [];
        $shouldDoFullRecalculate = false;

        $masterHasCompletedQuickScan = $this->building->hasCompletedQuickScan($this->masterInputSource);
        // Answers have been updated, we save them and dispatch a recalculate
        if ($this->dirty) {
            foreach ($this->filledInAnswers as $toolQuestionShort => $givenAnswer) {
                // Define if we should answer this question...
                /** @var ToolQuestion $toolQuestion */
                $toolQuestion = ToolQuestion::findByShort($toolQuestionShort);
                if ($this->building->user->account->can('answer', $toolQuestion)) {

                    $masterAnswer = $this->building->getAnswer($this->masterInputSource, $toolQuestion);
                    if ($masterAnswer !== $givenAnswer) {
                        $dirtyToolQuestions[$toolQuestion->short] = $toolQuestion;
                    }

                    ToolQuestionService::init($toolQuestion)
                        ->building($this->building)
                        ->currentInputSource($this->currentInputSource)
                        ->applyExampleBuilding()
                        ->save($givenAnswer);

                    if (ToolQuestionHelper::shouldToolQuestionDoFullRecalculate($toolQuestion, $this->building, $this->masterInputSource) && $masterHasCompletedQuickScan) {
                        Log::debug("Question {$toolQuestion->short} should trigger a full recalculate");
                        $shouldDoFullRecalculate = true;
                    }

                    // get the expert step equivalent
                    // we will filter out duplicates later on.
                    $stepShortsToRecalculate = array_merge($stepShortsToRecalculate, ToolQuestionHelper::stepShortsForToolQuestion($toolQuestion, $this->building, $this->masterInputSource));
                }
            }
        }


        // the INITIAL calculation will be handled by the CompletedSubStepObserver
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
        } else if ($masterHasCompletedQuickScan && ! empty($stepShortsToRecalculate)) {
            // the user already has completed the quick scan, so we will only recalculate specific parts of the advices.
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

        // Now mark the sub step as complete
        $completedSubStep = CompletedSubStep::firstOrCreate([
            'sub_step_id' => $this->subStep->id,
            'building_id' => $this->building->id,
            'input_source_id' => $this->currentInputSource->id
        ]);

        $flowService = ScanFlowService::init($this->step->scan, $this->building, $this->currentInputSource)
            ->forStep($this->step)
            ->forSubStep($this->subStep);

        if ($completedSubStep->wasRecentlyCreated) {
            // No need to check SubSteps that were recently created because they passed conditions
            $flowService->skipSubstep($completedSubStep->subStep);
        }

        $flowService->checkConditionals($dirtyToolQuestions);

        // TODO: We might have to generate the $nextUrl in real time if conditional steps follow a related question
        return redirect()->to($flowService->resolveNextUrl());
    }
}
