<?php

namespace App\Http\Controllers\Cooperation\Pdf;

use App\Helpers\Hoomdossier;
use App\Helpers\StepHelper;
use App\Jobs\GenerateUserReport;
use App\Models\Cooperation;
use App\Http\Controllers\Controller;
use App\Models\Step;
use App\Models\UserActionPlanAdvice;
use App\Services\CsvService;
use App\Services\PdfService;
use Barryvdh\DomPDF\Facade as PDF;

class UserReportController extends Controller
{
    public function index(Cooperation $cooperation)
    {


        $user = Hoomdossier::user()->load('building');

        $GLOBALS['_cooperation'] = $cooperation;
        $userActionPlanAdvices = $user->actionPlanAdvices()->with('measureApplication')->get();


        set_time_limit(0);

        $pdfData = \Cache::get('test3');
        /** @var \Barryvdh\DomPDF\PDF $pdf */
        $stepSlugs = \DB::table('steps')->select('slug')->get()->pluck('slug')->flip()->toArray();
        $commentsByStep = StepHelper::getAllCommentsByStep();

        dd($commentsByStep);
        $pdf = PDF::loadView('cooperation.pdf.user-report.index', compact(
            'user', 'cooperation', 'pdfData', 'stepSlugs', 'userActionPlanAdvices',
            'commentsByStep'
        ));



        return $pdf->stream();
        return view('cooperation.pdf.user-report.index',  compact('user', 'cooperation', 'pdfData', 'stepSlugs', 'userActionPlanAdvices',
        'commentsByStep'
        ));
    }

    public function pdfData()
    {
        $user = Hoomdossier::user();

        $calculateData = CsvService::getCalculateData($user->building, $user);
        $userData = PdfService::totalReportForUser($user);

        return ['user-data' => $userData, 'calculate-data' => $calculateData];
    }
}
