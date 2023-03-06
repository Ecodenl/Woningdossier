<?php

namespace App\Console\Commands\Api\Econobis\Out\Building;

use App\Models\Building;
use App\Models\InputSource;
use App\Models\Scan;
use App\Services\Econobis\Client;
use App\Services\Econobis\Econobis;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ScanStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api:econobis:out:building:scan-status {building : The id of the building you would like to process.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send the current status of the building.';

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
        $building = Building::findOrFail($this->argument('building'));
        $cooperation = $building->user->cooperation;
        $inputSource = InputSource::findByShort(InputSource::MASTER_SHORT);
        $data = [];

        $scans = $cooperation->load(['scans' => fn($q) => $q->where('short', '!=', Scan::EXPERT)])->scans;
        $data['scans'] = [];
        foreach($scans as $scan) {
            $data['scans'][$scan->short] = [
                'id' => $scan->id,
                'name' => $scan->name,
                'short' => $scan->short,
                'is_completed' => $building->hasCompletedScan($scan, $inputSource),
            ];
        }
        $data = array_merge($data, [
            'account_related' => [
                'building_id' => $building->id,
                'user_id' => $building->user->id,
                'account_id' => $building->user->account_id,
                'contact_id' => $building->user->extra['contact_id'] ?? null,
            ],
        ]);

        $logger = \Illuminate\Support\Facades\Log::getLogger();
        $client = Client::init($logger);
        $econobis = Econobis::init($client);

        $response = $econobis->hoomdossier()->scanStatus($data);

        Log::debug('Response', $response);

        return 0;
    }
}
