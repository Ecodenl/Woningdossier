<?php

namespace App\Http\Controllers\Cooperation\Pdf;

use App\Models\Cooperation;
use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade as PDF;

class UserReportController extends Controller
{
    public function index(Cooperation $cooperation)
    {
        /** @var \Barryvdh\DomPDF\PDF $pdf */
        $pdf = PDF::loadView('cooperation.pdf.user-report.index', compact('cooperation'));

        $pdfOptions = $pdf->getDomPDF()->getOptions();

        $pdfOptions->setIsPhpEnabled(true);

        return $pdf->stream();
        return view('cooperation.pdf.user-report.index');
    }
}
