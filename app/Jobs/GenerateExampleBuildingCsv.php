<?php

namespace App\Jobs;

use App\Exports\Cooperation\CsvExport;
use App\Helpers\ToolHelper;
use App\Models\Cooperation;
use App\Models\ExampleBuilding;
use App\Models\FileStorage;
use App\Models\FileType;
use App\Services\ContentStructureService;
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
        $contentStructure = ContentStructureService::init(
            ToolHelper::getContentStructure()
        )->applicableForExampleBuildings();

        $rows[] = ['Naam', 'Bouwjaar', ...collect($contentStructure)->pluck('*.*.label')->flatten()->filter()->toArray()];

        $exampleBuildings = ExampleBuilding::generic()->with('contents')->get();
        foreach ($exampleBuildings as $exampleBuilding) {

            foreach ($exampleBuilding->contents as $exampleBuildingContent) {
                $rows[$exampleBuildingContent->id] = [
                    $exampleBuilding->name,
                    $exampleBuildingContent->build_year
                ];


                foreach ($contentStructure as $step => $dataForSubSteps) {
                    foreach ($dataForSubSteps as $subStep => $subStepData) {
                        foreach ($subStepData as $formFieldName => $rowData) {
                            if ($formFieldName != 'calculations') {
                                $contentKey = "{$step}.{$subStep}.{$formFieldName}";

                                // what the admins filled in as value for the example building
                                $exampleBuildingValue = $exampleBuildingContent->getValue($contentKey);

                                if (array_key_exists('options', $rowData)) {
                                    if (is_array($exampleBuildingValue)) {
                                        $exampleBuildingValues = array_map(fn($value) => $rowData['options'][$value], $exampleBuildingValue);
                                        $exampleBuildingValue = implode(',', $exampleBuildingValues);
                                    } else if (!is_null($exampleBuildingValue)) {
                                        $exampleBuildingValue = $rowData['options'][$exampleBuildingValue];
                                    }
                                }


                                $rows[$exampleBuildingContent->id][$contentKey] = $exampleBuildingValue;
                            }
                        }
                    }
                }
            }
        }

        Excel::store(new CsvExport($rows), $this->fileStorage->filename, 'downloads', \Maatwebsite\Excel\Excel::CSV);

        $this->fileStorage->isProcessed();
    }

    public function failed(\Exception $exception)
    {
        $this->fileStorage->delete();
    }

}
