<?php

namespace App\Console\Commands\Upgrade\Subsidy;

use App\Console\Commands\Api\Verbeterjehuis\Mappings\SyncMeasures;
use App\Console\Commands\Api\Verbeterjehuis\Mappings\SyncTargetGroups;
use App\Helpers\Arr;
use App\Helpers\MappingHelper;
use App\Services\MappingService;
use Illuminate\Cache\Console\ClearCommand;
use Illuminate\Console\Command;
use Illuminate\Database\Console\Seeds\SeedCommand;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SeedBagMunicipalityMapping extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'upgrade:subsidy:seed-bag-municipality-mapping';

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
        DB::table('mappings')
            ->where('type', MappingHelper::TYPE_BAG_MUNICIPALITY)
            ->delete();

        $filename = Storage::path('plaatsnamen.csv');
        $header = null;
        $delimiter = ',';
        if (($handle = fopen($filename, 'r')) !== false) {
            while (($row = fgetcsv($handle, 1000, $delimiter)) !== false) {
                if (is_null($header)) {
                    $header = $row;
                } else {
                    if ($this->shouldSeedMunicipality($row)) {
                        MappingService::init()
                            ->from($row[2])
                            ->sync([], MappingHelper::TYPE_BAG_MUNICIPALITY);
                    }
                }
            }
            fclose($handle);
        }
    }

    private function shouldSeedMunicipality(array $row)
    {
        $gemeenteId = $row[3];

        if (empty($gemeenteId)) {
            return true;
        }
        return false;
    }
}