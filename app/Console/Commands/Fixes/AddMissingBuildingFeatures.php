<?php

namespace App\Console\Commands\Fixes;

use App\Models\InputSource;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AddMissingBuildingFeatures extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fixes:add-missing-building-features';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $buildingsWithoutFeatures = DB::select("SELECT * 
            FROM buildings b WHERE NOT EXISTS (
                SELECT * FROM building_features f WHERE b.id = f.building_id
            ) AND b.user_id IS NOT NULL");

        $inputSources = DB::table('input_sources')
            ->whereIn('short', [InputSource::MASTER_SHORT, InputSource::RESIDENT_SHORT])
            ->get();

        foreach ($buildingsWithoutFeatures as $buildingWithoutFeatures) {
            foreach ($inputSources as $inputSource) {
                DB::table('building_features')->insert([
                    'building_id' => $buildingWithoutFeatures->id,
                    'input_source_id' => $inputSource->id,
                    'created_at' => now(),
                ]);
            }
        }

        return 0;
    }
}
