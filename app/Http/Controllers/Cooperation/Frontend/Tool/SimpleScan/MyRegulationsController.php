<?php

namespace App\Http\Controllers\Cooperation\Frontend\Tool\SimpleScan;

use App\Helpers\HoomdossierSession;
use App\Helpers\MyRegulationHelper;
use App\Http\Controllers\Controller;
use App\Models\Building;
use App\Models\Cooperation;
use App\Models\InputSource;
use App\Models\Scan;
use App\Services\Models\NotificationService;

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
        $relevantRegulations = MyRegulationHelper::getRelevantRegulations($building, $masterInputSource);

        return view(
            'cooperation.frontend.tool.simple-scan.my-regulations.index',
            compact('scan', 'activeNotification', 'masterInputSource', 'relevantRegulations')
        );
    }
}
