<?php

namespace App\Jobs;

use App\Exports\Cooperation\CsvExport;
use App\Helpers\ToolHelper;
use App\Helpers\QuestionValues\QuestionValue;
use App\Models\Cooperation;
use App\Models\ExampleBuilding;
use App\Models\FileStorage;
use App\Models\FileType;
use App\Models\ToolQuestion;
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
            ToolHelper::getContentStructure(ToolHelper::STRUCT_TOTAL)
        )->applicableForExampleBuildings();

        // Use array_values because apparently you cannot unpack (...) associative arrays (until PHP 8.1, anyways)
        $rows[] = ['Naam', 'Bouwjaar', ...array_values($contentStructure)];

        $exampleBuildings = ExampleBuilding::generic()->with('contents')->get();
        foreach ($exampleBuildings as $exampleBuilding) {
            foreach ($exampleBuilding->contents as $exampleBuildingContent) {
                $rows[$exampleBuildingContent->id] = [
                    $exampleBuilding->name,
                    $exampleBuildingContent->build_year,
                ];

                foreach ($contentStructure as $short => $translation) {
                    // The value is not always the correct translation. We'll have to dive down the rabbit hole
                    // to fetch the correct translation.
                    $exampleBuildingValue = $exampleBuildingContent->getValue($short);
                    $toolQuestion = ToolQuestion::findByShort($short);
                    $toolQuestionType = $toolQuestion->subSteps()->first()->pivot->toolQuestionType;

                    if (in_array($toolQuestionType->short, [
                        'radio-icon', 'radio-icon-small', 'radio', 'dropdown', 'checkbox-icon', 'multi-dropdown',
                    ])) {
                        $exampleBuildingValue = (array) $exampleBuildingValue;

                        $exampleBuildingValue = QuestionValue::init($this->cooperation, $toolQuestion)
                            ->answers(collect($exampleBuildingContent->content))
                            ->getQuestionValues()
                            ->whereIn('value', $exampleBuildingValue)
                            ->pluck('name')
                            ->implode(', ');
                    }

                    $rows[$exampleBuildingContent->id][$short] = $exampleBuildingValue;
                }
            }
        }

        Excel::store(new CsvExport($rows), $this->fileStorage->filename, 'downloads', \Maatwebsite\Excel\Excel::CSV);

        $this->fileStorage->isProcessed();
    }

    public function Failed(\Throwable $exception)
    {
        $this->fileStorage->delete();

        if (app()->bound('sentry')) {
            app('sentry')->captureException($exception);
        }
    }
}
