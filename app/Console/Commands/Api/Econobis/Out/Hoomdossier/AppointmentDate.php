<?php

namespace App\Console\Commands\Api\Econobis\Out\Hoomdossier;

use App\Jobs\Econobis\Out\SendAppointmentDateToEconobis;
use App\Models\Building;
use App\Services\Econobis\EconobisService;
use App\Services\Econobis\Api\EconobisApi;
use App\Services\Econobis\Payloads\AppointmentDatePayload;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class AppointmentDate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api:econobis:out:hoomdossier:appointment-date {building : The id of the building you would like to process.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send the current appointment date of the building.';

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
    public function handle(): int
    {
        SendAppointmentDateToEconobis::dispatch(
            Building::findOrFail($this->argument('building'))
        );

        return 0;
    }
}
