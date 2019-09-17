<?php

namespace App\Http\Controllers\Cooperation\Pdf;

use App\Helpers\Hoomdossier;
use App\Helpers\HoomdossierSession;
use App\Helpers\StepHelper;
use App\Models\BuildingElement;
use App\Models\BuildingInsulatedGlazing;
use App\Models\BuildingService;
use App\Models\Cooperation;
use App\Http\Controllers\Controller;
use App\Models\Element;
use App\Models\InputSource;
use App\Models\Interest;
use App\Models\Service;
use App\Models\UserActionPlanAdvice;
use App\Models\UserActionPlanAdviceComments;
use App\Scopes\GetValueScope;
use App\Services\DumpService;
use Barryvdh\DomPDF\Facade as PDF;

class UserReportController extends Controller
{
    /**
     *
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


        // the comments that have been made on the action plan
        $userActionPlanAdviceComments = UserActionPlanAdviceComments::withoutGlobalScope(GetValueScope::class)
            ->where('user_id', $user->id)
            ->with('inputSource')
            ->get();

        $steps = $userCooperation->getActiveOrderedSteps();

        $userActionPlanAdvices = UserActionPlanAdvice::getPersonalPlan($user, $inputSource);

        // we don't want the advices, we need to show them in a different way.
        $advices = UserActionPlanAdvice::getCategorizedActionPlan($user, $inputSource, false);

        // full report for a user
        $reportForUser = DumpService::totalDump($user, $inputSource, false);

        // the translations for the columns / tables in the user data
        $reportTranslations = $reportForUser['translations-for-columns'];

        // undot it so we can handle the data in view later on
        $reportData = \App\Helpers\Arr::arrayUndot($reportForUser['user-data']);

        // steps that are considered to be measures.
        $stepSlugs = \DB::table('steps')
            ->where('slug', '!=', 'building-detail')
            ->where('slug', '!=', 'general-data')
            ->select('slug', 'id')
            ->get()
            ->pluck('slug', 'id')
            ->flip()
            ->toArray();


        // retrieve all the comments by for each input source on a step
        $commentsByStep = StepHelper::getAllCommentsByStep($user);

        $noInterest = Interest::where('calculate_value', 4)->first();

        /** @var \Barryvdh\DomPDF\PDF $pdf */
        $pdf = PDF::loadView('cooperation.pdf.user-report.index', compact(
            'user', 'building', 'userCooperation', 'stepSlugs', 'inputSource',
            'commentsByStep', 'reportTranslations', 'reportData', 'userActionPlanAdvices', 'reportForUser', 'noInterest',
            'buildingFeatures', 'advices', 'steps', 'userActionPlanAdviceComments', 'buildingInsulatedGlazings'
        ));

        return $pdf->stream();
        return view('cooperation.pdf.user-report.index', compact(
            'user', 'building', 'userCooperation', 'stepSlugs', 'inputSource',
            'commentsByStep', 'reportTranslations', 'reportData', 'userActionPlanAdvices',
            'buildingFeatures', 'advices', 'steps', 'userActionPlanAdviceComments', 'buildingInsulatedGlazings'
        ));


    }
}
