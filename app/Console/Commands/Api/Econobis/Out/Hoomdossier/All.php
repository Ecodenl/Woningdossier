<?php

namespace App\Console\Commands\Api\Econobis\Out\Hoomdossier;

use App\Models\Building;
use App\Services\Econobis\EconobisService;
use App\Services\Econobis\Api\EconobisApi;
use App\Services\Econobis\Payloads\AppointmentDatePayload;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class All extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api:econobis:out:hoomdossier:all {building : The id of the building you would like to process.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run all out commands.';

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
    public function handle(): int
    {
        $commands = [
            AppointmentDate::class,
            BuildingStatus::class,
            Delete::class,
            Gebruik::class,
            PdfReport::class,
            ScanStatus::class
        ];
        foreach ($commands as $command) {
            $this->call($command, ['building' => $this->argument('building')]);
            $this->info("Processed {$command}");
        }
    }
}
