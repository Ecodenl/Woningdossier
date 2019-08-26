<?php

namespace App\Http\Controllers\Cooperation\MyAccount;

use App\Models\FileStorage;
use App\Models\FileTypeCategory;
use App\Scopes\AvailableScope;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ReportController extends Controller
{
    public function index()
    {
        $reportFileTypeCategory = FileTypeCategory::short('report')
            ->whereHas('fileTypes', function ($query) {
                $query->where('short', 'pdf-report');
            })->first();

        $anyFilesBeingProcessed = FileStorage::withOutGlobalScope(new AvailableScope())->where('is_being_processed', true)->count();

        return view('cooperation.my-account.reports.index', compact('reportFileTypeCategory', 'anyFilesBeingProcessed'));
    }

}
