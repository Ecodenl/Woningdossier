<?php

namespace App\Jobs;

use App\Exports\Cooperation\CsvExport;
use App\Helpers\HoomdossierSession;
use App\Models\Cooperation;
use App\Models\FileStorage;
use App\Models\FileType;
use App\Models\InputSource;
use App\Models\Questionnaire;
use App\Services\CsvService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class GenerateCustomQuestionnaireReport implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected $questionnaire;
    protected $anonymizeData;
    protected $fileType;
    protected $fileStorage;
    protected $filename;

    /**
     * @param Questionnaire $questionnaire
     * @param FileStorage $fileStorage
     * @param FileType    $fileType
     * @param bool        $anonymizeData
     */
    public function __construct(Questionnaire $questionnaire, $filename, FileType $fileType, FileStorage $fileStorage, bool $anonymizeData = false)
    {
        $this->fileType = $fileType;
        $this->filename = $filename;
        $this->fileStorage = $fileStorage;
        $this->questionnaire = $questionnaire;
        $this->anonymizeData = $anonymizeData;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $rows = CsvService::dumpForQuestionnaire($this->questionnaire, $this->anonymizeData);

        Excel::store(new CsvExport($rows), $this->filename, 'downloads', \Maatwebsite\Excel\Excel::CSV);

        $this->fileStorage->isProcessed();
    }

    public function failed(\Exception $exception)
    {
        $this->fileStorage->delete();
    }
}
