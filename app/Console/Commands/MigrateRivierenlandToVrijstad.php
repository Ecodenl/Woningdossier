<?php

namespace App\Console\Commands;

use App\Models\Cooperation;
use Illuminate\Console\Command;

class MigrateRivierenlandToVrijstad extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:rivierenland-vrijstad {residentEmails : emails of the resident, comma separated}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrates cooperation rivierenland to vrijstad';

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
    public function handle()
    {
        $residentEmails = explode(',', $this->argument('residentEmails'));
        $fromCooperation = Cooperation::where('slug', 'energieloketrivierenland')->first();
        $toCooperation   = Cooperation::where('slug', 'vrijstadenergie')->first();


        // just to visualise / confirm what is about to happen
        $this->table(['email'], [$residentEmails]);
        $this->warn("irreversible action head!");
        if ($this->confirm("Are you sure you want to migrate the given users from {$fromCooperation->name} [{$fromCooperation->id}] to {$toCooperation->name} [{$toCooperation->id}]")) {
            foreach ($residentEmails as $residentEmail) {
                $this->call(MigrateUser::class, [
                    'email' => $residentEmail,
                    'from' => $fromCooperation->id,
                    'to' => $toCooperation->id
                ]);
            }
        }


        return 0;
    }
}
