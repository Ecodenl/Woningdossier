<?php

namespace App\Http\Controllers\Cooperation\Frontend\Tool\SimpleScan;

use App\Helpers\HoomdossierSession;
use App\Http\Controllers\Controller;
use App\Models\Building;
use App\Models\Cooperation;
use App\Models\InputSource;
use App\Models\Scan;
use App\Models\UserActionPlanAdvice;
use App\Services\Models\NotificationService;
use App\Services\UserActionPlanAdviceService;
use App\Services\Verbeterjehuis\RegulationService;

class MyRegulationsController extends Controller
{
    public function index(Cooperation $cooperation, Scan $scan)
    {
        /** @var Building $building */
        $building = HoomdossierSession::getBuilding(true);
        $masterInputSource = InputSource::findByShort(InputSource::MASTER_SHORT);

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

        $payload = RegulationService::init()
            ->forBuilding($building)
            ->getSearch()
            ->getCategorized();

        $advices =
            $building->user->userActionPlanAdvices()
            ->forInputSource($masterInputSource)
            ->withoutDeletedCooperationMeasureApplications($masterInputSource)
            ->whereIn('category', [UserActionPlanAdviceService::CATEGORY_TO_DO, UserActionPlanAdviceService::CATEGORY_LATER])
            ->get();

        return view('cooperation.frontend.tool.simple-scan.my-regulations.index', compact('activeNotification', 'masterInputSource', 'payload', 'advices'));
    }
}
