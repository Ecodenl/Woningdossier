<?php

namespace App\Http\Controllers\Cooperation\Frontend\Tool\QuickScan;

use App\Helpers\HoomdossierSession;
use App\Jobs\RecalculateStepForUser;
use App\Models\Building;
use App\Models\Cooperation;
use App\Models\InputSource;
use App\Models\Media;
use App\Models\Notification;
use App\Models\Scan;
use App\Models\Step;
use App\Models\SubStep;
use App\Http\Controllers\Controller;
use App\Services\Models\NotificationService;
use Illuminate\Http\Request;

class MyPlanController extends Controller
{
    public function index(Cooperation $cooperation, Scan $scan)
    {

        /** @var Building $building */
        $building = HoomdossierSession::getBuilding(true);

        $masterInputSource = InputSource::findByShort(InputSource::MASTER_SHORT);

        // Apparently the plan should be visible for observing users
        if (! HoomdossierSession::isUserObserving()) {
            $firstIncompleteStep = $building->getFirstIncompleteStep([], $masterInputSource);

            // There are incomplete steps left, set the sub step
            if ($firstIncompleteStep instanceof Step) {
                $firstIncompleteSubStep = $building->getFirstIncompleteSubStep($firstIncompleteStep, [], $masterInputSource);

                if ($firstIncompleteSubStep instanceof SubStep) {
                    return redirect()->route('cooperation.frontend.tool.quick-scan.index', [
                        'step' => $firstIncompleteStep,
                        'subStep' => $firstIncompleteSubStep,
                    ]);
                }
            }
        }

        $types = [\App\Jobs\RecalculateStepForUser::class];

        $service = NotificationService::init()
            ->forInputSource($masterInputSource)
            ->forBuilding($building);

        $activeNotification = false;

        foreach ($types as $type) {
            if ($service->setType($type)->isActive()) {
                $activeNotification = true;
                break;
            }
        }

        return view('cooperation.frontend.tool.simple-scan.my-plan.index', compact('scan', 'building', 'activeNotification'));
    }

    public function media(Request $request, Cooperation $cooperation, Scan $scan, ?Building $building = null)
    {
        $this->authorize('viewAny', [Media::class, HoomdossierSession::getInputSource(true), HoomdossierSession::getBuilding(true)]);

        if (! $building instanceof Building) {
            $building = HoomdossierSession::getBuilding(true);
        }

        return view('cooperation.frontend.tool.simple-scan.my-plan.media', compact('scan', 'building'));
    }
}
