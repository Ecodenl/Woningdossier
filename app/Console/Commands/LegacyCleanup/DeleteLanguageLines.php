<?php

namespace App\Console\Commands\LegacyCleanup;

use App\Models\LanguageLine;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DeleteLanguageLines extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'language-lines:delete';

    /**
     * The console command description.
     *
     * @var string
     */
    // previously we had to create a migration each time language lines had to be removed from the database
    // these were just migrations with identical code, but different groups or keys
    protected $description = 'This command deletes the groups or/and keys given in the properties set, this command is here to be updated each time with new language lines to delete.';

    protected array $translationKeys = [
        'pdf/user-report.general-data.resume-energy-saving-measures.table.planned-year',
        'pdf/user-report.general-data.attachment.lead',
    ];

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
        $degenMode = false;
        if ($this->confirm("Do you want to remove all translations without confirmation each translation ?")) {
            $degenMode = true;
        }
        foreach ($this->translationKeys as $translationKey) {
            $groupAndKey = explode('.', $translationKey);
            $group = array_shift($groupAndKey);
            $key = implode('.', $groupAndKey);
            if ($degenMode === false && $this->confirm("Do you want to remove the following translation \r\n $translationKey \r\n" . __($translationKey) . "\r\n")) {
                LanguageLine::where(compact('group', 'key'))->delete();
                $this->info("Deleted");
            } else if ($degenMode === true) {
                LanguageLine::where(compact('group', 'key'))->delete();
                $this->info("Deleted");
            } else {
                $this->info("Skipped");
            }
        }

        $this->call('cache:clear');
    }
}
