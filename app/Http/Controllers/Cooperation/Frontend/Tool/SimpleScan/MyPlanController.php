<?php

namespace App\Http\Controllers\Cooperation\Frontend\Tool\SimpleScan;

use Illuminate\Support\Facades\Gate;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
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

        // If the user can't access his woonplan, redirect him back to the first incomplete step + substep.
        if (! $woonplanService->canAccessWoonplan()) {
            $firstIncompleteStep = $building->getFirstIncompleteStep($scan, $masterInputSource);

            // There are incomplete steps left, set the sub step
            if ($firstIncompleteStep instanceof Step) {
                $firstIncompleteSubStep = $building->getFirstIncompleteSubStep($firstIncompleteStep, $masterInputSource);

                if ($firstIncompleteSubStep instanceof SubStep) {
                    return redirect()->route('cooperation.frontend.tool.simple-scan.index', [
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

        return view('cooperation.frontend.tool.simple-scan.my-plan.index', compact('scan', 'building', 'inputSource', 'activeNotification'));
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
