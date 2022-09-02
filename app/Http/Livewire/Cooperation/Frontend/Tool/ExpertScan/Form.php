<?php

namespace App\Http\Livewire\Cooperation\Frontend\Tool\ExpertScan;

use App\Console\Commands\Tool\RecalculateForUser;
use App\Helpers\HoomdossierSession;
use App\Helpers\ToolQuestionHelper;
use App\Models\CompletedSubStep;
use App\Models\Cooperation;
use App\Models\InputSource;
use App\Models\Step;
use App\Models\SubStep;
use App\Models\ToolQuestion;
use App\Services\ToolQuestionService;
use Artisan;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class Form extends Component
{
    public $step;
    public $subSteps;
    public $locale;

    public $filledInAnswers = [];
    public $building;
    public $masterInputSource;
    public $currentInputSource;

    public $succeededSubSteps = [];
    public $failedValidationForSubSteps = [];

    public $cooperation;

    public $activeSubStep;

    public $listeners = [
        'subStepValidationSucceeded' => 'subStepSucceeded',
        'failedValidationForSubSteps',
        'setFilledInAnswers',
    ];

    public function mount(Step $step, Cooperation $cooperation)
    {
        $this->step = $step;
        $this->subSteps = $step->subSteps;

        $this->activeSubStep = $step->subSteps->first()->slug;
        $this->cooperation = $cooperation;
        $this->locale = app()->getLocale();
        $this->building = HoomdossierSession::getBuilding(true);
        $this->masterInputSource = InputSource::findByShort(InputSource::MASTER_SHORT);
        $this->currentInputSource = HoomdossierSession::getInputSource(true);
    }

    public function activeSubStep($subStepSlug)
    {
        $this->activeSubStep = $subStepSlug;
    }


    public function failedValidationForSubSteps(array $subStep)
    {
        $this->failedValidationForSubSteps[$subStep['slug'][$this->locale]] = $subStep['name'][$this->locale];
        $this->dispatchBrowserEvent('scroll-to-top');
    }

    // We will mark the given substep as succeeded
    public function subStepSucceeded(array $subStep)
    {
        $this->succeededSubSteps[] = $subStep['slug'][$this->locale];

        if ($this->allSubStepsSucceeded()) {
            $this->saveFilledInAnswers();
        }
    }

    public function setFilledInAnswers($filledInAnswers)
    {
        $this->filledInAnswers = $filledInAnswers;
        if ($this->allSubStepsSucceeded()) {
            $this->saveFilledInAnswers();
        }
    }

    public function allSubStepsSucceeded()
    {
        //
        $allFinished = count($this->succeededSubSteps) == $this->subSteps->count();
        $noDiff = count(array_diff($this->succeededSubSteps, $this->subSteps->pluck('slug')->toArray())) === 0;

        Log::debug("Finished $allFinished && diff ".$noDiff);

        return $allFinished && $noDiff;
    }

    public function saveFilledInAnswers()
    {
        Log::debug("Attempting to save the filled in answers");
        if (empty($this->filledInAnswers)) {
            Log::debug("filledInAnswers is empty...");
        } else {
            Log::debug("FilledInAnswers are present, we will save them now");
            $stepShortsToRecalculate = [];
            $shouldDoFullRecalculate = false;

            $masterHasCompletedQuickScan = $this->building->hasCompletedQuickScan($this->masterInputSource);
            // Answers have been updated, we save them and dispatch a recalculate
            // at this point we already now that the form is dirty, otherwise this event wouldnt have been dispatched
            foreach ($this->filledInAnswers as $toolQuestionId => $givenAnswer) {
                // Define if we should answer this question...
                /** @var ToolQuestion $toolQuestion */
                $toolQuestion = ToolQuestion::where('id', $toolQuestionId)->first();
                if ($this->building->user->account->can('answer', $toolQuestion)) {
                    ToolQuestionService::init($toolQuestion)
                        ->building($this->building)
                        ->currentInputSource($this->currentInputSource)
                        ->applyExampleBuilding()
                        ->save($givenAnswer);

                    if (ToolQuestionHelper::shouldToolQuestionDoFullRecalculate($toolQuestion) && $masterHasCompletedQuickScan) {
                        Log::debug("Question {$toolQuestion->short} should trigger a full recalculate");
                        $shouldDoFullRecalculate = true;
                    }

                    // get the expert step equivalent
                    // we will filter out duplicates later on.
                    $stepShortsToRecalculate = array_merge($stepShortsToRecalculate, ToolQuestionHelper::stepShortsForToolQuestion($toolQuestion));
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
            } else if ($masterHasCompletedQuickScan && !empty($stepShortsToRecalculate)) {
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

            // since we are done saving all the filled in answers, we can safely mark the sub steps as completed
            foreach ($this->subSteps as $subStep) {
                // Now mark the sub step as complete
                CompletedSubStep::firstOrCreate([
                    'sub_step_id' => $subStep->id,
                    'building_id' => $this->building->id,
                    'input_source_id' => $this->currentInputSource->id
                ]);
            }

            return redirect()->route('cooperation.frontend.tool.quick-scan.my-plan.index', ['cooperation' => $this->cooperation]);

        }
    }

    public function render()
    {
        return view('livewire.cooperation.frontend.tool.expert-scan.form');
    }
}
