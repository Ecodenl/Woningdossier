<?php

namespace App\Http\Livewire\Cooperation\Frontend\Tool\SimpleScan;

use App\Console\Commands\Tool\RecalculateForUser;
use App\Helpers\DataTypes\Caster;
use App\Helpers\HoomdossierSession;
use App\Helpers\ToolQuestionHelper;
use App\Http\Livewire\Cooperation\Frontend\Tool\Scannable;
use App\Models\CompletedSubStep;
use App\Helpers\Hoomdossier;
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
        Log::debug("mounting form [Step: {$step->id}] [SubStep: {$subStep->id}]");

        // Only load in properties that are not order inclusive
        $subStep->load([
            'toolQuestions' => function ($query) {
                $query->with('forSpecificInputSource');
            },
            'subStepTemplate',
        ]);
        $this->subStep = $subStep;

        $this->build();
    }

    public function render()
    {
        return view('livewire.cooperation.frontend.tool.simple-scan.form');
    }

    public function getSubSteppablesProperty()
    {
        return $this->subStep->subSteppables()->orderBy('order')->with(['subSteppable', 'toolQuestionType'])->get();
    }

    public function getToolQuestionsProperty()
    {
        // Eager loaded in hydration
        return $this->subStep->toolQuestions;
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


                    if (ToolQuestionHelper::shouldToolQuestionDoFullRecalculate($toolQuestion, $this->building, $this->masterInputSource)) {
                        Log::debug("Question {$toolQuestion->short} should trigger a full recalculate");
                        $shouldDoFullRecalculate = true;
                    }

                    $stepShortsToRecalculate = array_merge($stepShortsToRecalculate, ToolQuestionHelper::stepShortsForToolQuestion($toolQuestion, $this->building, $this->masterInputSource));
                }
            }
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

        $flowService->checkConditionals($dirtyToolQuestions, Hoomdossier::user());

        $quickScan = Scan::findByShort(Scan::QUICK);
        $masterHasCompletedScan = $this->building->hasCompletedScan($this->scan, $this->masterInputSource);

        // so this is another exception to the rule which needs some explaination..
        // we will only calculate the small measure when the user is currently on the lite scan and did not complete the quick-scan
        // this is done so when the user only uses the lite-scan the woonplan only gets small-measure, measureApplications.
        // else we will just do the regular recalculate/
        if ($masterHasCompletedScan) {
            if ($this->scan->isLiteScan()) {
                // so the full recalculate may be turned on due to the question (ToolQuestionHelper::shouldToolQuestionDoFullRecalculate)
                // however, the quick scan is not completed. A full recalculate is not correct at this time.
                // when the user is on the lite scan and its uncomplete
                // we are only allowed to recalculate the small measures
                // however, when the user complete the quick scan we CAN recalculate other steps.
                $userHasUncompletedQuickScan = $this->building->hasNotCompletedScan($quickScan,  $this->masterInputSource);
                $cooperationDoesntHaveQuickScan = $this->cooperation->scans()->where('scans.id', $quickScan->id)->doesntExist();
                if ($userHasUncompletedQuickScan || $cooperationDoesntHaveQuickScan) {
                    $shouldDoFullRecalculate = false;
                    // A non-full recalculate moves advices. If we shouldn't recalculate this step, we won't do anything.
                    // However, in the case the user completes the steps in a different order, we will force the calculation.
                    if (in_array('small-measures', $stepShortsToRecalculate) || $completedSubStep->wasRecentlyCreated) {
                        $stepShortsToRecalculate = ['small-measures'];
                    } else {
                        $stepShortsToRecalculate = [];
                    }
                }
            } else {
                // could be any scan, only the quick-scan is actualy possible atm.
                // also the quick scan is the only scan that is allowed to peform a full recalculate.
                if ($completedSubStep->wasRecentlyCreated) {
                    $shouldDoFullRecalculate = true;
                }
            }

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
        }

        return redirect()->to($flowService->resolveNextUrl());
    }
}
