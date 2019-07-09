<?php

namespace App\Http\Controllers\Cooperation\Pdf;

use App\Jobs\GenerateUserReport;
use App\Models\Cooperation;
use App\Http\Controllers\Controller;
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

        $pdfOptions = $pdf->getDomPDF()->getOptions();
        $pdfOptions->setIsPhpEnabled(true);

        $GLOBALS['_cooperation'] = $cooperation;


        return $pdf->stream();
        return view('cooperation.pdf.user-report.index', compact('user', 'cooperation'));
    }
}
