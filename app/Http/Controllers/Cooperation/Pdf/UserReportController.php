<?php

namespace App\Http\Controllers\Cooperation\Pdf;

use App\Jobs\GenerateUserReport;
use App\Models\Cooperation;
use App\Http\Controllers\Controller;
use App\Services\CsvService;
use App\Services\PdfService;
use Barryvdh\DomPDF\Facade as PDF;

class UserReportController extends Controller
{
    public function index(Cooperation $cooperation)
    {


        $user = \Auth::user();
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
        $user = \Auth::user();

        $calculateData = CsvService::getCalculateData($user->buildings()->first(), $user);
        $userData = PdfService::totalReportForUser($user);
    }
}
