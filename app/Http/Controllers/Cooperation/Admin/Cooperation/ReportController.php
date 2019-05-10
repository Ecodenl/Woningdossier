<?php

namespace App\Http\Controllers\Cooperation\Admin\Cooperation;

use App\Helpers\Str;
use App\Http\Controllers\Controller;
use App\Jobs\GenerateCustomQuestionnaireReport;
use App\Jobs\GenerateMeasureReport;
use App\Jobs\GenerateTotalReport;
use App\Models\Cooperation;
use App\Models\FileStorage;
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
     * @param FileType $fileType
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function generate(Cooperation $cooperation, FileType $fileType)
    {
        if($fileType->isBeingProcessed()) {
            return redirect()->back();
        }

        // we will create the file storage here, if we would do it in the job itself it would bring confusion to the user.
        // Because if there are multiple jobs in the queue, only the job thats being processed would show up as "generating"
        $fileName = substr(Str::uuid(), 0, 7).$this->fileType->name.'.csv';

        $fileStorage = FileStorage::create([
            'cooperation_id' => $this->cooperation->id,
            'file_type_id' => $this->fileType->id,
            'content_type' => 'text/csv',
            'filename' => $fileName,
        ]);


        switch ($fileType->short) {
            case 'total-report':
                GenerateTotalReport::dispatch($cooperation, $fileType, $fileStorage)->onQueue('high');
                break;
            case 'total-report-anonymized':
                GenerateTotalReport::dispatch($cooperation, $fileType, $fileStorage, true)->onQueue('high');
                break;
            case 'measure-report':
                GenerateMeasureReport::dispatch($cooperation, $fileType, $fileStorage)->onQueue('high');
                break;
            case 'measure-report-anonymized':
                GenerateMeasureReport::dispatch($cooperation, $fileType, $fileStorage, true)->onQueue('high');
                break;
            case 'custom-questionnaires-report':
                GenerateCustomQuestionnaireReport::dispatch($cooperation, $fileType, $fileStorage)->onQueue('high');
                break;
            case 'custom-questionnaires-report-anonymized':
                GenerateCustomQuestionnaireReport::dispatch($cooperation, $fileType, $fileStorage, true)->onQueue('high');
                break;

        }

        return redirect(route('cooperation.admin.cooperation.reports.index'))
            ->with('success',  __('woningdossier.cooperation.admin.cooperation.reports.generate.success'))
            ->with('file_type_'.$fileTypeId, '');
    }

}
