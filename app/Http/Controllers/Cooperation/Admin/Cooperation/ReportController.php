<?php

namespace App\Http\Controllers\Cooperation\Admin\Cooperation;

use App\Http\Controllers\Controller;
use App\Jobs\GenerateCustomQuestionnaireReport;
use App\Jobs\GenerateMeasureReport;
use App\Jobs\GenerateTotalReport;
use App\Models\Cooperation;
use App\Models\FileStorage;
use App\Models\FileType;
use App\Models\FileTypeCategory;
use App\Services\CsvService;

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
    public function download(Cooperation $cooperation, $fileTypeId)
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
            ->with('success', __('woningdossier.cooperation.admin.cooperation.reports.download.success'))
            ->with('file_type_'.$fileTypeId, __('woningdossier.cooperation.admin.cooperation.reports.download.success'));
    }

    public function downloadByYear()
    {
        return CsvService::byYear();
    }

    /**
     * Download the measure action plan
     *
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function downloadByMeasure()
    {

        return CsvService::byMeasure();
    }

    /**
     * Download the measure action plan, anonymized version
     *
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function downloadByMeasureAnonymized()
    {
        return CsvService::byMeasureAnonymized();
    }

    /**
     * Download the questionnaire results
     *
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function downloadQuestionnaireResults()
    {
        return CsvService::questionnaireResults();
    }

    /**
     * Download the questionnaire results anonymized
     *
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function downloadQuestionnaireResultsAnonymized()
    {
        return CsvService::questionnaireResultsAnonymized();
    }

    public function downloadTotalDump()
    {
        return CsvService::totalDump();
    }
}
