<?php

namespace App\Http\Controllers\Cooperation\Admin\Cooperation;

use Illuminate\View\View;
use App\Http\Controllers\Controller;
use App\Models\Cooperation;
use App\Models\FileStorage;
use App\Models\FileTypeCategory;
use App\Services\SmartTwin\SmartTwinFileTypes;

class ReportController extends Controller
{
    public function index(Cooperation $cooperation): View
    {
        $reportFileTypeCategory = FileTypeCategory::short('report')
            ->with(['fileTypes' => function ($query) {
                // The SmartTwin artifacts share the report category, but are per building instead of
                // cooperation wide. They're not reports a cooperation generates, so they don't belong
                // on this page at all; the super admin has a dedicated page for them.
                $query->whereNotIn('short', array_merge(
                    ['pdf-report', 'example-building-overview'],
                    SmartTwinFileTypes::all(),
                ))
                    ->with(['files' => function ($query) {
                        $query->leaveOutPersonalFiles();
                    }]);
            }])->first();

        $questionnaires = $cooperation->questionnaires;

        // Is there any file being processed for my cooperation
        $filesBeingProcessed = FileStorage::leaveOutPersonalFiles()->withExpired()->beingProcessed()->count();

        return view('cooperation.admin.cooperation.reports.index', compact('questionnaires', 'reportFileTypeCategory', 'filesBeingProcessed'));
    }
}
