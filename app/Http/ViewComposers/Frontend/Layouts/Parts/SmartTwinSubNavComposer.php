<?php

namespace App\Http\ViewComposers\Frontend\Layouts\Parts;

use App\Helpers\Blade\RouteLogic;
use App\Helpers\HoomdossierSession;
use App\Helpers\SmallMeasuresSettingHelper;
use App\Models\Building;
use App\Models\InputSource;
use App\Models\Scan;
use App\Models\Step;
use App\Models\SubStep;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * The sub navigation for the SmartTwin mode. Where the regular sub navigation walks every step of
 * the scan, this one is a fixed list of four: SmartTwin asks the technical questions, so only the
 * ones Hoomdossier still asks itself appear here, followed by the woonplan.
 *
 * The list is assembled here rather than in the view because deciding what is completed, current or
 * still ahead is not markup, and because the first entry is a sub step while the two after it are
 * steps — a difference the view should not have to care about.
 */
class SmartTwinSubNavComposer
{
    /** The sub step that holds the resident's own questions; it leads the navigation. */
    public const string MY_QUESTIONS_SLUG = 'mijn-vragen';

    public const string STATE_ACTIVE = 'active';
    public const string STATE_COMPLETED = 'completed';
    public const string STATE_DEFAULT = 'default';

    public function __construct(private Request $request)
    {
    }

    public function create(View $view): void
    {
        /** @var Scan $scan */
        $scan = $this->request->route('scan');
        $building = HoomdossierSession::getBuilding(true);
        $master = InputSource::master();

        $currentStep = $this->request->route('step');
        $currentSubStep = $this->request->route('subStep');

        $items = [];

        $livingRequirements = $scan->steps->firstWhere('short', 'living-requirements');

        if ($livingRequirements instanceof Step) {
            $myQuestions = SubStep::bySlug(static::MY_QUESTIONS_SLUG)
                ->where('step_id', $livingRequirements->id)
                ->first();

            if ($myQuestions instanceof SubStep) {
                $onMyQuestions = $currentSubStep instanceof SubStep && $currentSubStep->id === $myQuestions->id;

                $items[] = [
                    'label' => __('cooperation/frontend/layouts.sub-nav.my-questions'),
                    'url' => route('cooperation.frontend.tool.simple-scan.index', [
                        'scan' => $scan, 'step' => $livingRequirements, 'subStep' => $myQuestions,
                    ]),
                    'state' => $this->state(
                        $onMyQuestions,
                        $this->hasCompletedSubStep($building, $myQuestions, $master),
                    ),
                ];

                // The resident's questions live under Woonwensen, so being on them would light up
                // both entries. This keeps Woonwensen dark while its first sub step is showing.
                $items[] = $this->stepItem(
                    $livingRequirements,
                    __('cooperation/frontend/layouts.sub-nav.living-requirements'),
                    $scan,
                    $building,
                    $master,
                    $currentStep instanceof Step && $currentStep->id === $livingRequirements->id && ! $onMyQuestions,
                );
            }
        }

        $smallMeasures = $scan->steps->firstWhere('short', 'small-measures');

        if ($smallMeasures instanceof Step
            && $building instanceof Building
            && SmallMeasuresSettingHelper::isEnabledForBuilding($building, $scan)) {
            $items[] = $this->stepItem(
                $smallMeasures,
                __('cooperation/frontend/layouts.sub-nav.small-measures'),
                $scan,
                $building,
                $master,
                $currentStep instanceof Step && $currentStep->id === $smallMeasures->id,
            );
        }

        $inMyPlan = RouteLogic::inMyPlan($this->request->route());

        $items[] = [
            'label' => __('cooperation/frontend/tool.my-plan.label'),
            'url' => route('cooperation.frontend.tool.simple-scan.my-plan.index', compact('scan')),
            'state' => $inMyPlan ? static::STATE_ACTIVE : static::STATE_DEFAULT,
        ];

        $view->with('items', $items);
    }

    /**
     * @return array{label: string, url: string, state: string}
     */
    private function stepItem(
        Step $step,
        string $label,
        Scan $scan,
        ?Building $building,
        InputSource $master,
        bool $isActive,
    ): array
    {
        $completed = $building instanceof Building && $building->hasCompleted($step, $master);

        // Land on the last sub step of a step that is done, the first of one that isn't; the same
        // rule the regular sub navigation uses.
        $subStep = $completed
            ? $step->subSteps()->orderByDesc('order')->first()
            : $step->subSteps()->orderBy('order')->first();

        return [
            'label' => $label,
            'url' => route('cooperation.frontend.tool.simple-scan.index', [
                'scan' => $scan, 'step' => $step, 'subStep' => $subStep,
            ]),
            'state' => $this->state($isActive, $completed),
        ];
    }

    private function state(bool $isActive, bool $isCompleted): string
    {
        if ($isActive) {
            return static::STATE_ACTIVE;
        }

        return $isCompleted ? static::STATE_COMPLETED : static::STATE_DEFAULT;
    }

    private function hasCompletedSubStep(?Building $building, SubStep $subStep, InputSource $inputSource): bool
    {
        if (! $building instanceof Building) {
            return false;
        }

        return $building->completedSubSteps()
            ->forInputSource($inputSource)
            ->where('sub_step_id', $subStep->id)
            ->exists();
    }
}
