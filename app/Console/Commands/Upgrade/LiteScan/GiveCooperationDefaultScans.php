<?php

namespace App\Console\Commands\Upgrade\LiteScan;

use App\Models\Cooperation;
use App\Models\CooperationScan;
use App\Models\Scan;
use Illuminate\Console\Command;

class GiveCooperationDefaultScans extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'upgrade:lite-scan:default-scans-cooperations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Give the all the cooperations a quick-scan';

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
        $cooperations = Cooperation::doesntHave('scans')->get();
        $scans = Scan::whereIn('short', ['expert-scan', 'quick-scan'])->get();
        foreach ($cooperations as $cooperation) {
            $cooperation->scans()->sync($scans);
        }
        return 0;
    }
}
