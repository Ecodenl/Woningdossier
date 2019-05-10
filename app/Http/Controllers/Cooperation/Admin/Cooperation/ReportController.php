<?php

namespace App\Http\Controllers\Cooperation\Admin\Cooperation;

use App\Http\Controllers\Controller;
use App\Jobs\GenerateCustomQuestionnaireReport;
use App\Jobs\GenerateMeasureReport;
use App\Jobs\GenerateTotalReport;
use App\Models\Cooperation;
use App\Models\FileType;
use App\Models\FileTypeCategory;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index()
    {
        $reportFileTypeCategory = FileTypeCategory::short('report')->with('fileTypes.files')->first();

        return view('cooperation.admin.cooperation.reports.index', compact('reportFileTypeCategory'));
    }

    /**
     * Method that handles the right download by id.
     *
     * @param  Cooperation  $cooperation
     * @param $fileTypeId
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function generate(Cooperation $cooperation, $fileTypeId)
    {
        $fileType = FileType::findOrFail($fileTypeId);

        if($fileType->isBeingProcessed()) {
            return redirect()->back();
        }

        switch ($fileType->short) {
            case 'total-report':
                GenerateTotalReport::dispatch($cooperation, $fileType)->onQueue('high');
                break;
            case 'total-report-anonymized':
                GenerateTotalReport::dispatch($cooperation, $fileType, true)->onQueue('high');
                break;
            case 'measure-report':
                GenerateMeasureReport::dispatch($cooperation, $fileType)->onQueue('high');
                break;
            case 'measure-report-anonymized':
                GenerateMeasureReport::dispatch($cooperation, $fileType, true)->onQueue('high');
                break;
            case 'custom-questionnaires-report':
                GenerateCustomQuestionnaireReport::dispatch($cooperation, $fileType)->onQueue('high');
                break;
            case 'custom-questionnaires-report-anonymized':
                GenerateCustomQuestionnaireReport::dispatch($cooperation, $fileType, true)->onQueue('high');
                break;

        }

        return redirect(route('cooperation.admin.cooperation.reports.index'))
            ->with('success',  __('woningdossier.cooperation.admin.cooperation.reports.generate.success'))
            ->with('file_type_'.$fileTypeId, '');
    }

}
