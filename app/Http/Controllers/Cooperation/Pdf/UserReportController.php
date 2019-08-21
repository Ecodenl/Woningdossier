<?php

namespace App\Http\Controllers\Cooperation\Pdf;

use App\Helpers\Hoomdossier;
use App\Jobs\GenerateUserReport;
use App\Models\Cooperation;
use App\Http\Controllers\Controller;
use App\Services\CsvService;
use App\Services\PdfService;
use Barryvdh\DomPDF\Facade as PDF;
use function Couchbase\defaultDecoder;

class UserReportController extends Controller
{
    public function index(Cooperation $cooperation)
    {


        $user = Hoomdossier::user()->load('building');

        /** @var \Barryvdh\DomPDF\PDF $pdf */
        $pdf = PDF::loadView('cooperation.pdf.user-report.index', [
            'cooperation' => $cooperation,
            'user' => $user
        ]);



        $GLOBALS['_cooperation'] = $cooperation;

        $this->pdfData();

        return $pdf->stream();
        return view('cooperation.pdf.user-report.index', compact('user', 'cooperation'));
    }

    public function pdfData()
    {
        $user = Hoomdossier::user();

        $calculateData = CsvService::getCalculateData($user->building, $user);
        dd($calculateData);
        $userData = PdfService::totalReportForUser($user);
    }
}
