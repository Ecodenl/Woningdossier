<?php

namespace App\Console\Commands\Macros;

use App\Exports\ToolQuestionsExport;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Excel;

class ExportToolQuestionTranslations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'macros:export-tool-question-translations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export the tool questions with its translations';

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
     */
    public function handle(Excel $excel): int
    {
        $excel->store(new ToolQuestionsExport, 'tool-questions.csv', 'local', Excel::CSV);

        return 0;
    }
}
