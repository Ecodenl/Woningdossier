<?php

namespace App\Http\Controllers\Cooperation\Pdf;

use App\Helpers\HoomdossierSession;
use App\Helpers\StepHelper;
use App\Http\Controllers\Controller;
use App\Models\Cooperation;
use App\Models\CooperationMeasureApplication;
use App\Models\CustomMeasureApplication;
use App\Models\InputSource;
use App\Models\MeasureApplication;
use App\Models\User;
use App\Models\UserActionPlanAdviceComments;
use App\Services\BuildingCoachStatusService;
use App\Services\DumpService;
use App\Services\UserActionPlanAdviceService;
use App\Services\UserService;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Support\Facades\DB;

class UserReportController extends Controller
{
    /**
     * TESTING only, turn on the routes to use it.
     */
    public function index(Cooperation $userCooperation)
    {
        $building = HoomdossierSession::getBuilding(true);
        $user = $building->user;
        // Always retrieve from master
        $inputSource = InputSource::findByShort(InputSource::MASTER_SHORT);

        $dumpService = DumpService::init()->user($user)->inputSource($inputSource)
            ->createHeaderStructure();

        $user = UserService::eagerLoadUserData($user, $inputSource);
        $buildingFeatures = $building->buildingFeatures;

        $GLOBALS['_cooperation'] = $userCooperation;
        $GLOBALS['_inputSource'] = $inputSource;

        $buildingInsulatedGlazings = $building->currentInsulatedGlazing->load('measureApplication', 'insulatedGlazing', 'buildingHeating');

        $userEnergyHabit = $user->energyHabit()->forInputSource($inputSource)->first();

        // unfortunately we can't load the whereHasMorph
        // so we have to do 2 separate queries and merge the collections together.
        $userActionPlanAdvicesForCustomMeasureApplications = $user
            ->actionPlanAdvices()
            ->forInputSource($inputSource)
            ->whereIn('category', [UserActionPlanAdviceService::CATEGORY_TO_DO, UserActionPlanAdviceService::CATEGORY_LATER])
            ->whereHasMorph(
                'userActionPlanAdvisable',
                [CustomMeasureApplication::class],
                function ($query) use ($inputSource) {
                    $query
                        ->forInputSource($inputSource);
                })->with(['userActionPlanAdvisable' => fn($query) => $query->forInputSource($inputSource)])->get();

        $remainingUserActionPlanAdvices = $user
            ->actionPlanAdvices()
            ->forInputSource($inputSource)
            ->whereIn('category', [UserActionPlanAdviceService::CATEGORY_TO_DO, UserActionPlanAdviceService::CATEGORY_LATER])
            ->whereHasMorph(
                'userActionPlanAdvisable',
                [MeasureApplication::class, CooperationMeasureApplication::class]
            )->get();

        $userActionPlanAdvices = $userActionPlanAdvicesForCustomMeasureApplications->merge($remainingUserActionPlanAdvices)->sortBy('order');

        // we don't want the actual advices, we have to show them in a different way
        $measures = UserActionPlanAdviceService::getCategorizedActionPlan($user, $inputSource, false);

        // full report for a user
        $reportForUser = $dumpService->generateDump();

        // the translations for the columns / tables in the user data
        $reportTranslations = $dumpService->headerStructure;

        //$calculations = $reportForUser['calculations'];
        //$reportData = [];
        //
        //foreach ($reportForUser['user-data'] as $key => $value) {
        //    // so we now its a step.
        //    if (is_string($key)) {
        //        $keys = explode('.', $key);
        //
        //        $tableData = array_splice($keys, 2);
        //
        //        // we dont want the calculations in the report data, we need them separate
        //        if (!in_array('calculation', $tableData)) {
        //            $reportData[$keys[0]][$keys[1]][implode('.', $tableData)] = $value;
        //        }
        //    }
        //}

        $connectedCoaches = BuildingCoachStatusService::getConnectedCoachesByBuildingId($building->id);
        $connectedCoachNames = [];
        foreach ($connectedCoaches->pluck('coach_id') as $coachId) {
            array_push($connectedCoachNames, User::find($coachId)->getFullName());
        }

        // retrieve all the comments by for each input source on a step
        $commentsByStep = StepHelper::getAllCommentsByStep($building);

        // the comments that have been made on the action plan
        $userActionPlanAdviceComments = UserActionPlanAdviceComments::forMe($user)
            ->with('inputSource')
            ->get()
            ->pluck('comment', 'inputSource.name')
            ->toArray();

        /** @var \Barryvdh\DomPDF\PDF $pdf */
        $pdf = PDF::loadView('cooperation.pdf.user-report.index', compact(
            'user', 'building', 'userCooperation', 'inputSource', 'userEnergyHabit', 'connectedCoachNames',
            'commentsByStep', 'reportTranslations', 'reportData', 'userActionPlanAdvices', 'reportForUser',
            'buildingFeatures', 'measures', 'userActionPlanAdviceComments', 'buildingInsulatedGlazings', 'calculations'
        ));

        return $pdf->stream();
    }
}
