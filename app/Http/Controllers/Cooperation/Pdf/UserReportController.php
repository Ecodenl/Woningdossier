<?php

namespace App\Http\Controllers\Cooperation\Pdf;

use App\Helpers\Hoomdossier;
use App\Helpers\StepHelper;
use App\Models\Cooperation;
use App\Http\Controllers\Controller;
use App\Services\CsvService;
use App\Services\DumpService;
use App\Services\PdfService;
use Barryvdh\DomPDF\Facade as PDF;

class UserReportController extends Controller
{
    public function index(Cooperation $cooperation)
    {

        $user = Hoomdossier::user();
        $building = $user->building;

        $GLOBALS['_cooperation'] = $cooperation;

        $userActionPlanAdvicesQuery = $user->actionPlanAdvices();

        $userActionPlanAdvices = $userActionPlanAdvicesQuery->get();

        $userActionPlanAdvicesWithMaintenance = $userActionPlanAdvicesQuery
            ->whereHas('measureApplication', function ($query) {
                $query->where('measure_type', 'maintenance');
            })->get();

        $userActionPlanAdvicesWithEnergySaving = $userActionPlanAdvicesQuery
            ->whereHas('measureApplication', function ($query) {
                $query->where('measure_type', 'energy_saving');
            })->get();

        $reportForUser = DumpService::totalDump($user, false);

        $reportTranslations = $reportForUser['translations-for-columns'];
        // undot it so we can handle the data in view later on
        $reportData = \App\Helpers\Arr::arrayUndot($reportForUser['user-data']);


        $stepSlugs = \DB::table('steps')->select('slug', 'id')->get()->pluck('slug', 'id')->flip()->toArray();
        // retrieve all the comments by for each input source on a step
        $commentsByStep = StepHelper::getAllCommentsByStep();

        /** @var \Barryvdh\DomPDF\PDF $pdf */
        $pdf = PDF::loadView('cooperation.pdf.user-report.index', compact(
            'user', 'building', 'cooperation', 'pdfData', 'stepSlugs', 'userActionPlanAdvicesWithMaintenance',
            'userActionPlanAdvicesWithEnergySaving', 'commentsByStep', 'reportTranslations', 'reportData', 'userActionPlanAdvices'
        ));

        return $pdf->stream();
    }

    public function pdfData()
    {
        $user = Hoomdossier::user();

        $calculateData = CsvService::getCalculateData($user->building, $user);
        $userData = PdfService::totalReportForUser($user);

        return ['user-data' => $userData, 'calculate-data' => $calculateData];
    }
}
