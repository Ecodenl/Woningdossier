<?php

namespace App\Http\Livewire\Cooperation\Frontend\Tool\ExpertScan;

use App\Calculations\HighEfficiencyBoiler;
use App\Console\Commands\Tool\RecalculateForUser;
use App\Helpers\HoomdossierSession;
use App\Helpers\ToolQuestionHelper;
use App\Models\CompletedSubStep;
use App\Models\Cooperation;
use App\Models\InputSource;
use App\Models\Step;
use App\Models\ToolQuestion;
use App\Services\ToolQuestionService;
use Artisan;
use Illuminate\Support\Arr;
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

    protected $listeners = [
        'subStepValidationSucceeded' => 'subStepSucceeded',
        'failedValidationForSubSteps',
        'updateFilledInAnswers',
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

    public function render()
    {
        return view('livewire.cooperation.frontend.tool.expert-scan.form');
    }

    public function activeSubStep($subStepSlug)
    {
        $this->activeSubStep = $subStepSlug;
    }


    public function failedValidationForSubSteps(array $subStep)
    {
        // Unset succeeded sub step because it fails now
        unset($this->succeededSubSteps[$subStep['slug'][$this->locale]]);

        $this->failedValidationForSubSteps[$subStep['slug'][$this->locale]] = $subStep['name'][$this->locale];
        $this->dispatchBrowserEvent('scroll-to-top');
    }

    // We will mark the given sub step as succeeded
    public function subStepSucceeded(array $subStep, array $filledInAnswers)
    {
        // Unset failed sub step because it succeeds now
        unset($this->failedValidationForSubSteps[$subStep['slug'][$this->locale]]);

        $this->setFilledInAnswers($filledInAnswers);
        $this->succeededSubSteps[$subStep['slug'][$this->locale]] = $subStep['slug'][$this->locale];

        // Save answers if all sub steps have been succesfully answered
        if ($this->allSubStepsSucceeded()) {
            $this->saveFilledInAnswers();
        }
    }

    public function setFilledInAnswers($filledInAnswers)
    {
        // We can't directly set the answers because there will be more than one sub step that is passing
        // answers. array_merge messes up the keys and addition (array + array) causes weird behaviour
        foreach ($filledInAnswers as $toolQuestionId => $answer) {
            $this->filledInAnswers[$toolQuestionId] = $answer;
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

    public function performCalculations()
    {
        $considerableQuestion = ToolQuestion::findByShort('heat-source-considerable');
        $considerables = $this->filledInAnswers[$considerableQuestion->id]
            ?? $this->building->getAnswer($this->masterInputSource, $considerableQuestion);

        $energyHabit = $this->building->user->energyHabit()->forInputSource($this->masterInputSource)->first();

        $hrBoilerCalculations = [];
        $sunBoilerCalculations = [];
        $heatPumpCalculations = [];

        // No need to calculate if we're not considering it
        if (in_array('hr-boiler', $considerables)) {
            // key = tool question short
            // value = custom key for the calculate data, sometimes we can use the save in
            // for ex; new-boiler-type is a "new" question, previously we just had this as building_services for the current situation
            // the HR boiler calculate class is not adjusted to the old / new situation. It only knows that building_services.service_value_id is a boiler.
            // ideally this gets refactored when the time is ripe
            $saveInToolQuestionShorts = [
                'amount-gas' => null,
                'new-boiler-type' => 'building_services.service_value_id',
                // this question is not asked on this page, which means we should retrieve it.
                'boiler-placed-date' => 'building_services.extra.date',
            ];

            $calculateData = [];
            foreach($saveInToolQuestionShorts as $toolQuestionShort => $key) {
                $toolQuestion = ToolQuestion::findByShort($toolQuestionShort);

                // it may be possible that the tool question is not present in the filled in answers.
                // that simply means the tool question is not available for the user on the current page
                // however it may be filled elsewhere, so we will get it through the getAnswer
                $answer = $this->filledInAnswers[$toolQuestion->id] ?? $this->building->getAnswer($this->masterInputSource,
                        $toolQuestion);

                Arr::set($calculateData, $key ?? $toolQuestion->save_in, $answer);
            }

            // the HR boiler and solar boiler are not built with the tool questions in mind, we have to work with it for the time being
            $hrBoilerCalculations = HighEfficiencyBoiler::calculate($energyHabit, $calculateData);
        }

        if (in_array('sun-boiler', $considerables)) {

        }

        if (in_array('heat-pump', $considerables)) {
            // Only the heat pump will be built with the tool questions in mind.

        }

        $this->emit('calculationsPerformed', [
            'hr-boiler' => $hrBoilerCalculations,
            'sun-boiler' => $sunBoilerCalculations,
            'heat-pump' => $heatPumpCalculations,
        ]);
    }
}
