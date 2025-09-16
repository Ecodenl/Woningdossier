<?php

namespace App\Console\Commands\Api\Econobis\Out\Hoomdossier;

use App\Jobs\Econobis\Out\SendUserDeletedToEconobis;
use App\Models\Building;
use App\Models\Cooperation;
use App\Services\Econobis\EconobisService;
use Illuminate\Console\Command;

class Delete extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api:econobis:out:hoomdossier:delete 
                            {building : The id of the building you would like to process.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send the building that should be deleted';

    /**
     * Execute the console command.
     */
    public function handle(EconobisService $econobisService): int
    {
        $building = Building::find($this->argument('building'));

        if ($building instanceof Building && $building->user?->cooperation instanceof Cooperation) {
            SendUserDeletedToEconobis::dispatch(
                $building->user->cooperation,
                $econobisService->forBuilding($building)->resolveAccountRelated()
            );
        }

        return self::SUCCESS;
    }
}
