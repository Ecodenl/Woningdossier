<?php

namespace App\Http\Controllers\Cooperation\Admin\Cooperation\CooperationAdmin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Cooperation\Admin\Cooperation\Coordinator\ReportController as CooperationReportController;

class ReportController extends Controller
{
    public function index()
    {
        return (new CooperationReportController())->index();
    }

    public function downloadByYear()
    {
        return (new CooperationReportController())->downloadByYear();
    }

    public function downloadByMeasure()
    {
        return (new CooperationReportController())->downloadByMeasure();
    }
}
