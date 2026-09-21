<?php

namespace App\Http\Controllers\Cooperation\Frontend\Tool\SimpleScan;

use Illuminate\Support\Facades\Gate;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use App\Enums\SmartTwin\EventType;
use App\Models\Account;
use App\Helpers\Hoomdossier;
use App\Helpers\HoomdossierSession;
use RuntimeException;
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

        if (! $woonplanService->canAccessWoonplan()) {
            // In SmartTwin mode there is no incomplete step to send the resident back to: the
            // technical questions are asked in SmartTwin. What there is instead is the check itself,
            // or the wait for its results.
            if (Hoomdossier::hasEnabledSmartTwinCalls()) {
                return $this->smartTwinEmptyState($scan, $building);
            }

            // Otherwise, redirect him back to the first incomplete step + substep.
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
        $canHandOff = $this->canHandOff();

        return view('cooperation.frontend.tool.simple-scan.my-plan.index', compact(
            'scan',
            'building',
            'inputSource',
            'activeNotification',
            'canHandOff',
        ));
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
            'canHandOff' => $this->canHandOff(),
        ]);
    }

    /**
     * Whether we can send this account into SmartTwin at all.
     *
     * The id is handed out when the account is created, so not having one means that never happened.
     * There is nothing the resident can do about it and nothing useful to show them, but it is not
     * supposed to occur, so it is reported rather than silently swallowed.
     */
    private function canHandOff(): bool
    {
        if (! Hoomdossier::hasEnabledSmartTwinCalls()) {
            return false;
        }

        // An API client can be authenticated here too, and has no SmartTwin account to speak of.
        $account = Hoomdossier::account();

        if ($account instanceof Account && ! empty($account->smartTwinUserId())) {
            return true;
        }

        report(new RuntimeException(sprintf(
            'Account %s has no SmartTwin user id, so the hand-off cannot be offered.',
            $account instanceof Account ? $account->id : 'unknown',
        )));

        return false;
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
