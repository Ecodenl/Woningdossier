<?php

namespace App\Http\Controllers\Cooperation\Frontend\Tool\SimpleScan;

use App\Helpers\HoomdossierSession;
use App\Http\Controllers\Controller;
use App\Models\Building;
use App\Models\Cooperation;
use App\Models\InputSource;
use App\Models\Mapping;
use App\Models\MeasureApplication;
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
            ->getSearch();

        // here we will heavy modify the "payload" (regulations)
        // this is all bussines logic
        // we will filter out all the regulations that are not relevant for the user, they are not relevant when theere are no matching advices
        // we will also add the appropriate data while at it, so we dont have to do it again in the view.
//        $advisableMaps = Mapping::where('from_model_type', MeasureApplication::class)->get();
        $advices = $building
            ->user
            ->userActionPlanAdvices()
            ->forInputSource($masterInputSource)
            ->withoutDeletedCooperationMeasureApplications($masterInputSource)
            ->whereIn('category', [UserActionPlanAdviceService::CATEGORY_TO_DO, UserActionPlanAdviceService::CATEGORY_LATER])
            ->get();

        $regulations = [];
        foreach ($payload->transformedPayload as $regulation) {
            $where = [];
            if ($regulation['Type'] == RegulationService::LOAN) {
                $where['loan_available'] = true;
            }
            if ($regulation['Type'] == RegulationService::SUBSIDY) {
                $where['subsidy_available'] = true;
            }

            if ($regulation['Type'] == RegulationService::OTHER) {
                dd('bier');
            }
            dd($regulation['Tags']);
            $building
                ->user
                ->userActionPlanAdvices()
                ->forInputSource($masterInputSource)
                ->where($where)
                ->whereIn('category', [UserActionPlanAdviceService::CATEGORY_TO_DO, UserActionPlanAdviceService::CATEGORY_LATER]);

        }
//        foreach ($payload-> as )

        return view('cooperation.frontend.tool.simple-scan.my-regulations.index',
            compact('activeNotification', 'masterInputSource', 'payload', 'advices'));
    }
}
