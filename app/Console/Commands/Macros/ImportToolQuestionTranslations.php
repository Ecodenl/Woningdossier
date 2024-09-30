<?php

namespace App\Console\Commands\Macros;

use App\Helpers\FileFormats\CsvHelper;
use App\Imports\ToolQuestionsImport;
use App\Models\ToolQuestion;
use Illuminate\Console\Command;
use Illuminate\Filesystem\FilesystemManager;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Excel;

class ImportToolQuestionTranslations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'macros:import-tool-question-translation';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will update the tool question translations, based on the csv.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(Excel $excel): int
    {
        if (! Storage::disk('local')->exists('tool-questions.csv')) {
            $this->error('"tool-questions.csv" not found at /storage/app!');
            exit;
        }

        $excel->import(
            new ToolQuestionsImport(),
            Storage::disk('local')->path('tool-questions.csv'),
            null,
            Excel::CSV
        );

        return 0;
    }
}
