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

    /**
     * @param Questionnaire $questionnaire
     * @param FileStorage $fileStorage
     * @param FileType    $fileType
     * @param bool        $anonymizeData
     */
    public function __construct(Questionnaire $questionnaire, FileType $fileType, FileStorage $fileStorage, bool $anonymizeData = false)
    {
        $this->fileType = $fileType;
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
        if (\App::runningInConsole()) {
            \Log::debug(__CLASS__.' Is running in the console with a maximum execution time of: '.ini_get('max_execution_time'));
        }
        $rows = CsvService::dumpForQuestionnaire($this->questionnaire, $this->anonymizeData);

        $date = Carbon::now()->format('y-m-d');

        $questionnaireName = Str::slug($this->questionnaire->name);

        $filename = "{$date}-{$questionnaireName}-{$this->anonymizeData}.csv";

        Excel::store(new CsvExport($rows), $filename, 'exports', \Maatwebsite\Excel\Excel::CSV);

        $this->fileStorage->isProcessed();
    }

    public function failed(\Exception $exception)
    {
        $this->fileStorage->delete();
    }
}
