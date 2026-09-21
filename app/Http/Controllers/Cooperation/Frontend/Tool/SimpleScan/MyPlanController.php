<?php

namespace App\Http\Controllers\Cooperation\Frontend\Tool\SimpleScan;

use Illuminate\Support\Facades\Gate;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use App\Enums\SmartTwin\EventType;
use App\Models\Account;
use App\Helpers\Hoomdossier;
use App\Helpers\HoomdossierSession;
use App\Helpers\RoleHelper;
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
use App\Services\SmartTwin\Api\UserRole;
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
     */
    private function canHandOff(): bool
    {
        if (! Hoomdossier::hasEnabledSmartTwinCalls()) {
            return false;
        }

        // SmartTwin has a tool for a resident and one for a coach, and nothing for anyone else. An
        // account whose roles are only coordinator or cooperation-admin is skipped when SmartTwin
        // users are created (see SmartTwinEventSubscriber::dispatchForAccount), so it has no
        // SmartTwin user by design, and the hand-off would come back "unsupported role" anyway.
        $roleName = HoomdossierSession::getRole(true)?->name;

        if (! in_array($roleName, [RoleHelper::ROLE_RESIDENT, RoleHelper::ROLE_COACH], true)) {
            return false;
        }

        // An API client can be authenticated here too, and has no SmartTwin account to speak of.
        $account = Hoomdossier::account();

        if ($account instanceof Account && ! empty($account->smartTwinUserId())) {
            // An account gets one SmartTwin user, created for one role, and coach wins when an
            // account holds both (see SmartTwinEventSubscriber). So holding an id is not the same
            // as being able to use it in the role you are currently in: SmartTwin refuses a
            // quick-scan link for an Advisor account, and the other way round. Offering the button
            // in that case would be offering a round trip to an error message.
            $needed = $roleName === RoleHelper::ROLE_COACH ? UserRole::Advisor : UserRole::Resident;

            // Not reported: this is a known trade-off of one SmartTwin user per account, not a
            // fault in ours.
            return $account->smartTwinUserRole() === $needed;
        }

        // A resident or a coach is supposed to have one: it is handed out when the SmartTwin user is
        // created. Not having one means that never happened. Nothing the user can do about it and
        // nothing useful to show them, but it is not supposed to occur, so it is reported rather
        // than silently swallowed.
        report(new RuntimeException(sprintf(
            'Account %s (%s) has no SmartTwin user id, so the hand-off cannot be offered.',
            $account instanceof Account ? $account->id : 'unknown',
            $roleName,
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
