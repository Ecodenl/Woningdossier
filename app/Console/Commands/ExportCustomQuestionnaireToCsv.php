<?php

namespace App\Console\Commands;

use App\Exports\Cooperation\CsvExport;
use App\Models\Questionnaire;
use App\Services\CsvService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class ExportCustomQuestionnaireToCsv extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'export:custom-questionnaire {questionnaireId} {anonymize}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export a specific questionnaire to csv file.';

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
     * @return mixed
     */
    public function handle()
    {
        $questionnaire = Questionnaire::find(
            $this->argument('questionnaireId')
        );

        $rows = CsvService::dumpForQuestionnaire($questionnaire, $this->argument('anonymize'));

        $date = Carbon::now()->format('y-m-d');

        $questionnaireName = Str::slug($questionnaire->name);

        $isAnonymized = $this->argument('anonymize') ? 'zonder-adresgegevens' : 'met-adresgegevens';
        $filename = "{$date}-{$questionnaireName}-{$isAnonymized}.csv";

        Excel::store(new CsvExport($rows), $filename, 'exports', \Maatwebsite\Excel\Excel::CSV);
    }
}
