<?php

namespace App\Http\Controllers\Cooperation\Admin\Cooperation;

use App\Helpers\Arr;
use App\Helpers\HoomdossierSession;
use App\Http\Controllers\Controller;
use App\Models\Building;
use App\Models\Cooperation;
use App\Models\InputSource;
use App\Models\MeasureApplication;
use App\Models\Question;
use App\Models\Questionnaire;
use App\Models\QuestionOption;
use App\Scopes\GetValueScope;
use App\Services\CsvExportService;
use App\Services\CsvReportService;
use App\Services\CsvService;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index()
    {
        return view('cooperation.admin.cooperation.reports.index');
    }

    public function downloadQuestionnaireResults()
    {
        return CsvService::questionnaireResults();
    }

    public function downloadByYear()
    {
        return CsvService::byYear();
    }

    public function downloadByMeasure()
    {

        return CsvService::byMeasure();
    }

    public function downloadByMeasureAnonymized()
    {
        return CsvService::byMeasureAnonymized();
    }
}
