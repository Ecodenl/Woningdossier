<?php

namespace App\Console\Commands\Api\Verbeterjehuis\Mappings;

use App\Services\Verbeterjehuis\Client;
use App\Services\Verbeterjehuis\Verbeterjehuis;
use Illuminate\Console\Command;

class SyncTargetGroups extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api:verbeterjehuis:mapping:sync-target-groups';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will map our ToolQuestionCustomValues to the correct target groups.';

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
        $map = [
            ''
        ];
        $client = Client::init();
        $targetGroups = Verbeterjehuis::init($client)->regulation()->getFilters()['TargetGroups'];

        return 0;
    }
}
