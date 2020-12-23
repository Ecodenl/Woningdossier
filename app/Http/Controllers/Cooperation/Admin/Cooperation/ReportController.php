<?php

namespace App\Http\Controllers\Cooperation\Admin\Cooperation;

use App\Http\Controllers\Controller;
use App\Models\Cooperation;
use App\Models\FileStorage;
use App\Models\FileTypeCategory;
use App\Models\InputSource;
use App\Models\User;
use App\Services\DumpService;
use App\Services\UserService;
use Illuminate\Support\Facades\Cache;

class ReportController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Cooperation $cooperation)
    {
        $reportFileTypeCategory = FileTypeCategory::short('report')
            ->with(['fileTypes' => function ($query) {
                $query->where('short', '!=', 'pdf-report')
                    ->with(['files' => function ($query) {
                        $query->leaveOutPersonalFiles();
                    }]);
            }])->first();

        $questionnaires = $cooperation->questionnaires;

        // Is there any file being processed for my cooperation
        $anyFilesBeingProcessed = FileStorage::leaveOutPersonalFiles()->withExpired()->beingProcessed()->count();

        return view('cooperation.admin.cooperation.reports.index', compact('questionnaires', 'reportFileTypeCategory', 'anyFilesBeingProcessed'));
    }
}
