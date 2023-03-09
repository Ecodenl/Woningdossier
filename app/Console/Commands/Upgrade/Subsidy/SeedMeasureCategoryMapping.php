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

class SeedMeasureCategoryMapping extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'upgrade:subsidy:seed-measure-category-mapping';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Upgrade the application with all changes for the lite scan';

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
        $targetGroups = collect(
            RegulationService::init()->getFilters()['Measures']
        )->keyBy('Value');

        foreach ($targetGroups as $targetGroup) {
            $measureCategory = MeasureCategory::updateOrCreate(
                ['short' => Str::slug($targetGroup['Label'])],
                ['name' => ['nl' => $targetGroup['Label']]]
            );

            MappingService::init()
                ->from($measureCategory)
                ->sync([$targetGroup], MappingHelper::TYPE_MEASURE_CATEGORY_VBJEHUIS);
        }
    }
}
