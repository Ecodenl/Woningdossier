<?php

namespace App\Console\Commands\Api\Verbeterjehuis\Mappings;

use App\Helpers\Arr;
use App\Helpers\MappingHelper;
use App\Helpers\Wrapper;
use App\Models\Mapping;
use App\Services\Verbeterjehuis\RegulationService;
use Illuminate\Console\Command;

class ResyncMunicipalityMappings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api:verbeterjehuis:mapping:resync-municipality';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Walk through all mappings to update the ID based on name';

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
        $vbjehuisMunicipalities = Wrapper::wrapCall(fn () => RegulationService::init()->getFilters()['Cities']) ?? [];

        if (! empty($vbjehuisMunicipalities)) {
            foreach (Mapping::forType(MappingHelper::TYPE_MUNICIPALITY_VBJEHUIS)->get() as $mapping) {
                // Get the current data from Verbeterjehuis.
                $current = Arr::first(Arr::where($vbjehuisMunicipalities, fn ($data) => $data['Name'] === data_get($mapping->target_data, 'Name')));

                if (! is_array($current)) {
                    $this->info("No mapping available for mapping {$mapping->id} (({$mapping->resolvable->name})");
                } else {
                    if ($current['Id'] !== $mapping->target_data['Id']) {
                        $this->info("Changing {$mapping->id} from {$mapping->target_data['Id']} to {$current['Id']} for {$current['Name']} ({$mapping->resolvable->name})");

                        if ($mapping->resolvable->name !== $current['Name']) {
                            $this->warn("Municipality name {$mapping->resolvable->name} does not match Verbeterjehuis {$current['Name']}!");
                        }

                        $mapping->update(['target_data' => $current]);
                    }
                }
            }
        }

        return self::SUCCESS;
    }
}
