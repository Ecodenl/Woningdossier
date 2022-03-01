<?php

namespace App\Jobs;

use App\Exports\Cooperation\CsvExport;
use App\Models\Cooperation;
use App\Models\FileStorage;
use App\Models\FileType;
use App\Services\CsvService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Facades\Excel;

class GenerateExampleBuildingCsv implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $cooperation;
    public $fileType;
    public $fileStorage;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Cooperation $cooperation, FileType $fileType, FileStorage $fileStorage)
    {
        $this->cooperation = $cooperation;
        $this->fileType = $fileType;
        $this->fileStorage = $fileStorage;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

//        $rows = CsvService::totalReport($this->cooperation);

        // export the csv file
//        Excel::store(new CsvExport($rows), $this->fileStorage->filename, 'downloads', \Maatwebsite\Excel\Excel::CSV);

        $this->fileStorage->isProcessed();
    }

    public function failed(\Exception $exception)
    {
        $this->fileStorage->delete();
    }

}
