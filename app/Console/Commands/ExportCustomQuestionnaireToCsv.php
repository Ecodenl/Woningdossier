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

        if ($questionnaire instanceof Questionnaire) {
            $debugTxt = "with address info";
            $isAnonymized = 'met-adresgegevens';
            if ($this->argument('anonymize') == true) {
                $debugTxt = "without address info";
                $isAnonymized = 'zonder-adresgegevens';
            }
            $this->alert("Starting export {$questionnaire->name} {$debugTxt}");

            $rows = CsvService::dumpForQuestionnaire($questionnaire, $this->argument('anonymize'));

            $date = Carbon::now()->format('y-m-d');

            $questionnaireName = Str::slug($questionnaire->name);

            $filename = "{$date}-{$questionnaireName}-{$isAnonymized}.csv";

            $this->info('Export completed! stored under storage/app/exports');
            Excel::store(new CsvExport($rows), $filename, 'exports', \Maatwebsite\Excel\Excel::CSV);
        } else {
            $this->alert("No questionnaire with ID: {$this->argument('questionnaireId')} found");
        }
    }
}
