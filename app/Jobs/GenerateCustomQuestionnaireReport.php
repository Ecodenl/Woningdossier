<?php

namespace App\Jobs;

use App\Exports\Cooperation\CsvExport;
use App\Helpers\HoomdossierSession;
use App\Helpers\Str;
use App\Models\Cooperation;
use App\Models\FileStorage;
use App\Models\FileType;
use App\Models\InputSource;
use App\Services\CsvService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Maatwebsite\Excel\Facades\Excel;

class GenerateCustomQuestionnaireReport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $cooperation;
    protected $anonymizeData;
    protected $fileType;

    /**
     * Generate the custom questionnaire report.
     *
     * @param  Cooperation  $cooperation
     * @param FileType $fileType
     * @param  bool  $anonymizeData
     */
    public function __construct(Cooperation $cooperation, FileType $fileType, bool $anonymizeData = false)
    {
        $this->fileType = $fileType;
        $this->cooperation = $cooperation;
        $this->anonymizeData = $anonymizeData;
    }
    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $fileName = substr(Str::uuid(), 0, 7).$this->fileType->name.'.csv';

        $fileStorage = FileStorage::create([
            'cooperation_id' => $this->cooperation->id,
            'file_type_id' => $this->fileType->id,
            'content_type' => 'text/csv',
            'filename' => $fileName,
        ]);

        // temporary session to get the right data for the dumb.
        $residentInputSource = InputSource::findByShort('resident');
        HoomdossierSession::setInputSource($residentInputSource);
        HoomdossierSession::setInputSourceValue($residentInputSource);

        // get the rows for the csv
        $rows = CsvService::questionnaireResults($this->cooperation, $this->anonymizeData);

        // forget the session since we dont need it.
        \Session::forget('hoomdossier_session');

        // export the csv file
        Excel::store(new CsvExport($rows), $fileName, 'downloads', \Maatwebsite\Excel\Excel::CSV);


        $availableUntil = $fileStorage->created_at->addDays($this->fileType->duration ?? 5);
        $fileStorage->available_until = $availableUntil;
        $fileStorage->is_being_processed = false;
        $fileStorage->save();
    }
}
