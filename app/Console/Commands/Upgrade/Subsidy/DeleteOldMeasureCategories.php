<?php

namespace App\Console\Commands\Upgrade\Subsidy;

use App\Console\Commands\Api\Verbeterjehuis\Mappings\SyncMeasures;
use App\Console\Commands\Api\Verbeterjehuis\Mappings\SyncTargetGroups;
use App\Helpers\Arr;
use App\Helpers\MappingHelper;
use App\Models\MeasureCategory;
use App\Models\Municipality;
use App\Services\MappingService;
use App\Services\Verbeterjehuis\RegulationService;
use Illuminate\Cache\Console\ClearCommand;
use Illuminate\Console\Command;
use Illuminate\Database\Console\Seeds\SeedCommand;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DeleteOldMeasureCategories extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'upgrade:subsidy:delete-old-measure-categories';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete the old non relevant measure categories.';

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
        DB::table('measure_categories')->where('id', '<=', 15)->delete();
    }
}