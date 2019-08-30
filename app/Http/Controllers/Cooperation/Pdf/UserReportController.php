<?php

namespace App\Http\Controllers\Cooperation\Pdf;

use App\Helpers\Hoomdossier;
use App\Helpers\HoomdossierSession;
use App\Helpers\StepHelper;
use App\Models\Cooperation;
use App\Http\Controllers\Controller;
use App\Models\InputSource;
use App\Models\UserActionPlanAdvice;
use App\Services\CsvService;
use App\Services\DumpService;
use App\Services\PdfService;
use Barryvdh\DomPDF\Facade as PDF;

class UserReportController extends Controller
{
    /**
     * @deprecated
     *
     * TESTING only, turn on the routes to use it.
     *
     * @param Cooperation $cooperation
     */
    public function index(Cooperation $cooperation)
    {

        $user = Hoomdossier::user()->load('motivations');

        $building = $user->building;
        $buildingFeatures = $building->buildingFeatures;

        $inputSource = InputSource::findByShort('resident');

        $GLOBALS['_cooperation'] = $cooperation;
        $GLOBALS['_inputSource'] = $inputSource;


        $userActionPlanAdvices = UserActionPlanAdvice::getPersonalPlan($user, $inputSource);

        $advices = UserActionPlanAdvice::getCategorizedActionPlan($user, $inputSource);

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


        /** @var \Barryvdh\DomPDF\PDF $pdf */
        $pdf = PDF::loadView('cooperation.pdf.user-report.index', compact(
            'user', 'building', 'cooperation', 'stepSlugs', 'inputSource',
            'commentsByStep', 'reportTranslations', 'reportData', 'userActionPlanAdvices',
            'buildingFeatures', 'advices'
        ));


        return $pdf->stream();
    }
}
