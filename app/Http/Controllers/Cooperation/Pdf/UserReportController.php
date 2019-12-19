<?php

namespace App\Http\Controllers\Cooperation\Pdf;

use App\Helpers\Arr;
use App\Helpers\HoomdossierSession;
use App\Helpers\StepHelper;
use App\Http\Controllers\Controller;
use App\Models\BuildingInsulatedGlazing;
use App\Models\Cooperation;
use App\Models\Interest;
use App\Models\UserActionPlanAdviceComments;
use App\Scopes\GetValueScope;
use App\Services\DumpService;
use App\Services\UserActionPlanAdviceService;
use Barryvdh\DomPDF\Facade as PDF;

class UserReportController extends Controller
{
    /**
     * TESTING only, turn on the routes to use it.
     *
     * @param Cooperation $userCooperation
     */
    public function index(Cooperation $userCooperation)
    {
        $building = HoomdossierSession::getBuilding(true);
        $user = $building->user;
        $inputSource = HoomdossierSession::getInputSource(true);

        $buildingFeatures = $building->buildingFeatures()->forInputSource($inputSource)->first();

        $GLOBALS['_cooperation'] = $userCooperation;
        $GLOBALS['_inputSource'] = $inputSource;

        $buildingInsulatedGlazings = BuildingInsulatedGlazing::where('building_id', $building->id)
            ->forInputSource($inputSource)
            ->with('measureApplication', 'insulatedGlazing', 'buildingHeating')
            ->get();


        $steps = $userCooperation->getActiveOrderedSteps();

        $userActionPlanAdvices = UserActionPlanAdviceService::getPersonalPlan($user, $inputSource);

        // we don't want the advices, we need to show them in a different way.
        $measures = UserActionPlanAdviceService::getCategorizedActionPlan($user, $inputSource, false);

        // full report for a user
        $reportForUser = DumpService::totalDump($user, $inputSource, false, true, true);

        // the translations for the columns / tables in the user data
        $reportTranslations = $reportForUser['translations-for-columns'];

        $calculations = $reportForUser['calculations'];
        $reportData = [];

        foreach ($reportForUser['user-data'] as $key => $value) {
            // so we now its a step.
            if (is_string($key)) {
                $keys = explode('.', $key);

                $tableData = array_splice($keys, 2);

                // we dont want the calculations in the report data, we need them separate
                if (!in_array('calculation', $tableData)) {
                    $reportData[$keys[0]][$keys[1]][implode('.', $tableData)] = $value;
                }
            }
        }

        // intersect the data, we dont need the data we wont show anyway
        $activeOrderedStepShorts = $steps->pluck('short')->flip()->toArray();
        $reportData = array_intersect_key($reportData, $activeOrderedStepShorts);



        // steps that are considered to be measures.
        $stepShorts = \DB::table('steps')
            ->select('short', 'id')
            ->get()
            ->pluck('short', 'id')
            ->flip()
            ->toArray();

        // retrieve all the comments by for each input source on a step
        $commentsByStep = StepHelper::getAllCommentsByStep($building);


        // the comments that have been made on the action plan
        $userActionPlanAdviceComments = UserActionPlanAdviceComments::forMe($user)
            ->with('inputSource')
            ->get()
            ->pluck('comment', 'inputSource.name')
            ->toArray();

        $noInterest = Interest::where('calculate_value', 4)->first();

        /** @var \Barryvdh\DomPDF\PDF $pdf */
        $pdf = PDF::loadView('cooperation.pdf.user-report.index', compact(
            'user', 'building', 'userCooperation', 'stepShorts', 'inputSource', 'measuresToCheckForCorrespondingPlannedYear',
            'commentsByStep', 'reportTranslations', 'reportData', 'userActionPlanAdvices', 'reportForUser', 'noInterest',
            'buildingFeatures', 'measures', 'steps', 'userActionPlanAdviceComments', 'buildingInsulatedGlazings', 'calculations'
        ));

        return $pdf->stream();

        return view('cooperation.pdf.user-report.index', compact(
            'user', 'building', 'userCooperation', 'stepSlugs', 'inputSource',
            'commentsByStep', 'reportTranslations', 'reportData', 'userActionPlanAdvices',
            'buildingFeatures', 'advices', 'steps', 'userActionPlanAdviceComments', 'buildingInsulatedGlazings'
        ));
    }
}
