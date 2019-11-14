<?php

namespace App\Http\Controllers\Cooperation\Admin\Cooperation;

use App\Helpers\Hoomdossier;
use App\Helpers\HoomdossierSession;
use App\Http\Controllers\Controller;
use App\Models\FileStorage;
use App\Models\FileTypeCategory;
use App\Models\InputSource;
use App\Services\CsvService;
use App\Services\DumpService;

class ReportController extends Controller
{
    public function index()
    {


        $reportFileTypeCategory = FileTypeCategory::short('report')
            ->with(['fileTypes' => function ($query) {
                $query->where('short', '!=', 'pdf-report')
                    ->with(['files' => function ($query) {
                        $query->leaveOutPersonalFiles();
                    }]);
            }])->first();

        // Is there any file being processed for my cooperation
        $anyFilesBeingProcessed = FileStorage::leaveOutPersonalFiles()->withExpired()->beingProcessed()->count();

        return view('cooperation.admin.cooperation.reports.index', compact('reportFileTypeCategory', 'anyFilesBeingProcessed'));
    }
}
