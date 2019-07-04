<?php

namespace App\Http\Controllers\Cooperation\Pdf;

use App\Models\Cooperation;
use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Support\Facades\App;

class UserReportController extends Controller
{
    public function index(Cooperation $cooperation)
    {
        /** @var \Barryvdh\DomPDF\PDF $pdf */
        $pdf = PDF::loadView('cooperation.pdf.user-report.index');
        return $pdf->stream();
        return view('cooperation.pdf.user-report.index');
    }
}
