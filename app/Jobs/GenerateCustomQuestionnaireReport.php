<?php

namespace App\Jobs;

use App\Exports\Cooperation\CsvExport;
use App\Models\FileStorage;
use App\Models\FileType;
use App\Models\Questionnaire;
use App\Services\CsvService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
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

    public function Failed(\Throwable $exception)
    {
        $this->fileStorage->delete();
    }
}
