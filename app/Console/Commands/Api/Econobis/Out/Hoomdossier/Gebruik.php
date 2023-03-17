<?php

namespace App\Console\Commands\Api\Econobis\Out\Hoomdossier;

use App\Models\Building;
use App\Models\InputSource;
use App\Models\ToolQuestion;
use App\Services\Econobis\Api\Client;
use App\Services\Econobis\Api\Econobis;
use App\Services\Econobis\EconobisService;
use App\Services\Econobis\Payloads\GebruikPayload;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

class Gebruik extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api:econobis:out:hoomdossier:gebruik {building : The id of the building you would like to process.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send all tool question with its answers to Econobis.';

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
    public function handle(EconobisService $econobisService, Econobis $econobis)
    {
        $building = Building::findOrFail($this->argument('building'));

        $econobis->hoomdossier()->gebruik($econobisService->getPayload($building, GebruikPayload::class));

        return 0;
    }
}
