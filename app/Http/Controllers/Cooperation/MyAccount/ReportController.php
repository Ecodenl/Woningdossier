<?php

namespace App\Http\Controllers\Cooperation\MyAccount;

use App\Helpers\Hoomdossier;
use App\Helpers\HoomdossierSession;
use App\Jobs\PdfReport;
use App\Models\Cooperation;
use App\Models\FileStorage;
use App\Models\FileType;
use App\Models\FileTypeCategory;
use App\Scopes\AvailableScope;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;

class ReportController extends Controller
{
    public function index()
    {
        $reportFileTypeCategory = FileTypeCategory::short('report')
            ->with(['fileTypes' => function ($query) {
                $query->where('short', 'pdf-report');
            }])->first();


        $anyFilesBeingProcessed = FileStorage::withOutGlobalScope(new AvailableScope())->where('is_being_processed', true)->count();

        return view('cooperation.my-account.reports.index', compact('reportFileTypeCategory', 'anyFilesBeingProcessed'));
    }


}
