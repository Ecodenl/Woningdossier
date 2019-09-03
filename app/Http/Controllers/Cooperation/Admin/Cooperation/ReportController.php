<?php

namespace App\Http\Controllers\Cooperation\Admin\Cooperation;

use App\Http\Controllers\Controller;
use App\Models\FileStorage;
use App\Models\FileTypeCategory;
use App\Scopes\AvailableScope;

class ReportController extends Controller
{
    public function index()
    {
        $reportFileTypeCategory = FileTypeCategory::short('report')
            ->with(['fileTypes' => function ($query) {
                $query->where('short', '!=', 'pdf-report')
                    ->with('files');
            }])
            ->first();

        $anyFilesBeingProcessed = FileStorage::withOutGlobalScope(new AvailableScope)->where('is_being_processed', true)->count();

        return view('cooperation.admin.cooperation.reports.index', compact('reportFileTypeCategory', 'anyFilesBeingProcessed'));
    }
}
