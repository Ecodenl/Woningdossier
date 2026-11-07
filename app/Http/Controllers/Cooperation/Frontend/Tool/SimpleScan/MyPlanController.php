<?php

namespace App\Http\Controllers\Cooperation\Frontend\Tool\SimpleScan;

use Illuminate\Support\Facades\Gate;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use App\Enums\SmartTwin\EventType;
use App\Helpers\Hoomdossier;
use App\Helpers\HoomdossierSession;
use App\Http\Controllers\Controller;
use App\Jobs\RecalculateStepForUser;
use App\Models\Building;
use App\Models\Cooperation;
use App\Models\InputSource;
use App\Models\Media;
use App\Models\Scan;
use App\Models\Step;
use App\Models\SubStep;
use App\Services\Models\NotificationService;
use App\Enums\SmartTwin\WoonplanState;
use App\Services\SmartTwin\HouseData;
use App\Services\SmartTwin\SmartTwinHandOff;
use App\Services\SmartTwin\WoonplanLock;
use App\Services\WoonplanService;
use Illuminate\Http\Request;

class MyPlanController extends Controller
{
    public function index(Cooperation $cooperation, Scan $scan): View|RedirectResponse
    {
        /** @var Building $building */
        $building = HoomdossierSession::getBuilding(true);
        $masterInputSource = InputSource::master();

        $woonplanService = WoonplanService::init($building)
            ->scan($scan);

        if (HoomdossierSession::isUserObserving()) {
            $woonplanService = $woonplanService->userIsObserving();
        }

        // A coach working on the plan in SmartTwin locks it for the resident; see WoonplanLock. While
        // the coach has not delivered a plan yet, there is nothing to show but "Nog even geduld".
        $lock = app(WoonplanLock::class);
        $woonplanState = $lock->stateFor($building, HoomdossierSession::getInputSource(true), HoomdossierSession::isUserObserving());

        if (WoonplanState::AWAITING_COACH === $woonplanState) {
            return view('cooperation.frontend.tool.simple-scan.my-plan.awaiting-coach', compact('scan', 'building'));
        }

        // Unlocked again with the coach's plan in it: the coach is done.
        $coachIsDone = WoonplanState::OPEN === $woonplanState
            && WoonplanLock::appliesTo(HoomdossierSession::getInputSource(true), HoomdossierSession::isUserObserving())
            && $lock->hasCoachPlan($building);

        // With SmartTwin the woonplan is the advice SmartTwin produced, so what decides whether there
        // is one to show is whether that advice has arrived -- not how far the scan got. Asking the
        // scan would show three empty columns to a building that filled it in before SmartTwin was
        // switched on, and to everyone on an environment where the woonplan guard is skipped.
        // A building with only the lite scan is not sent to SmartTwin, so it keeps the old guard.
        if (Hoomdossier::smartTwinFillsWoonplanFor($building)) {
            if (! $woonplanService->hasAdvices()) {
                return $this->smartTwinEmptyState($scan, $building);
            }
        } elseif (! $woonplanService->canAccessWoonplan()) {
            // Redirect him back to the first incomplete step + substep.
            $firstIncompleteStep = $building->getFirstIncompleteStep($scan, $masterInputSource);

            // There are incomplete steps left, set the sub step
            if ($firstIncompleteStep instanceof Step) {
                $firstIncompleteSubStep = $building->getFirstIncompleteSubStep($firstIncompleteStep, $masterInputSource);

                if ($firstIncompleteSubStep instanceof SubStep) {
                    return to_route('cooperation.frontend.tool.simple-scan.index', [
                        'scan' => $scan,
                        'step' => $firstIncompleteStep,
                        'subStep' => $firstIncompleteSubStep,
                    ]);
                }
            }
        }

        $activeNotification = NotificationService::init()
            ->forInputSource($masterInputSource)
            ->forBuilding($building)
            ->setType(RecalculateStepForUser::class)
            ->isActive();

        $inputSource = HoomdossierSession::getInputSource(true);
        $smartTwinOwnsWoonplan = Hoomdossier::smartTwinOwnsWoonplanFor($building);
        // Once the woonplan is SmartTwin's, the hand-off sits under its results as "Maatregelpakket
        // aanpassen", with its warning; see MyPlan\Form.
        $canHandOff = SmartTwinHandOff::isOfferedFor($building) && ! $smartTwinOwnsWoonplan;
        // "Gegevens woning bekijken" once there is a SmartTwin plan to show it for; see houseData().
        $showHouseData = $smartTwinOwnsWoonplan;

        return view('cooperation.frontend.tool.simple-scan.my-plan.index', compact(
            'scan',
            'building',
            'inputSource',
            'activeNotification',
            'canHandOff',
            'showHouseData',
            'smartTwinOwnsWoonplan',
            'woonplanState',
            'coachIsDone',
        ));
    }

    /**
     * "Gegevens woning bekijken": the dwelling as it is now, after SmartTwin's last import. Only once
     * the woonplan is SmartTwin's, and not while the resident waits for the coach: the woonplan, where
     * the button is, is not there then either (decision 2026-10-15-smarttwin-gegevens-woning-pagina,
     * S6g). Read-only, so someone observing sees it too.
     */
    public function houseData(Cooperation $cooperation, Scan $scan): View|RedirectResponse
    {
        /** @var Building $building */
        $building = HoomdossierSession::getBuilding(true);

        $woonplanState = app(WoonplanLock::class)
            ->stateFor($building, HoomdossierSession::getInputSource(true), HoomdossierSession::isUserObserving());

        if (! Hoomdossier::smartTwinOwnsWoonplanFor($building) || WoonplanState::AWAITING_COACH === $woonplanState) {
            return to_route('cooperation.frontend.tool.simple-scan.my-plan.index', compact('cooperation', 'scan'));
        }

        $houseData = HouseData::for($building);

        return view('cooperation.frontend.tool.simple-scan.my-plan.house-data', compact('scan', 'building', 'houseData'));
    }

    /**
     * The woonplan with nothing in it yet: either the resident has not done the check, or they have
     * and we are waiting for SmartTwin to send the results back.
     */
    private function smartTwinEmptyState(Scan $scan, Building $building): View
    {
        $eventType = EventType::tryFromInputSource(HoomdossierSession::getInputSource(true));

        if ($eventType instanceof EventType && $building->hasSmartTwinCallback($eventType)) {
            return view('cooperation.frontend.tool.simple-scan.my-plan.awaiting-advice', [
                'scan' => $scan,
                'building' => $building,
            ]);
        }

        return view('cooperation.frontend.tool.simple-scan.my-plan.start-check', [
            'scan' => $scan,
            'canHandOff' => SmartTwinHandOff::isOfferedFor($building),
        ]);
    }

    public function media(Request $request, Cooperation $cooperation, Scan $scan, ?Building $building = null): View
    {
        $currentBuilding = HoomdossierSession::getBuilding(true);

        Gate::authorize('viewAny', [Media::class, HoomdossierSession::getInputSource(true), $currentBuilding]);

        if (! $building instanceof Building) {
            $building = $currentBuilding;
        }

        return view('cooperation.frontend.tool.simple-scan.my-plan.media', compact('scan', 'building'));
    }
}
