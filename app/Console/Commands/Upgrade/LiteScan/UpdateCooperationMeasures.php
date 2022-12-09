<?php

namespace App\Console\Commands\Upgrade\LiteScan;

use App\Helpers\Arr;
use Database\Seeders\CooperationMeasureApplicationsTableSeeder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdateCooperationMeasures extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'upgrade:lite-scan:update-cooperation-measures';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update cooperation measures.';

    public function handle()
    {
        $measures = CooperationMeasureApplicationsTableSeeder::MEASURES;

        $newMeasures = Arr::where($measures, function ($value) {
            return $value['is_extensive_measure'] == true;
        });

        // Make insertable
        foreach ($newMeasures as $index => $newMeasure) {
            $newMeasures[$index]['name'] = json_encode($newMeasure['name']);
            $newMeasures[$index]['info'] = json_encode($newMeasure['info']);
            $newMeasures[$index]['costs'] = json_encode($newMeasure['costs']);
            $newMeasures[$index]['extra'] = json_encode($newMeasure['extra']);
        }

        // Set all non-extensive measures to be deletable
        DB::table('cooperation_measure_applications')->where('is_extensive_measure', false)
            ->update([
                'is_deletable' => true,
            ]);

        // Give each cooperation the new measures
        DB::table('cooperations')->orderBy('id')
            ->chunk(100, function ($cooperations) use ($newMeasures) {
                foreach ($cooperations as $cooperation) {
                    // Always atomic :)

                    $hasNewMeasures = DB::table('cooperation_measure_applications')
                        ->where('cooperation_id', $cooperation->id)
                        ->where('is_extensive_measure', true)
                        ->exists();

                    if (! $hasNewMeasures) {
                        // Add cooperation ID and insert
                        foreach ($newMeasures as $newMeasure) {
                            $newMeasure['cooperation_id'] = $cooperation->id;
                            DB::table('cooperation_measure_applications')
                                ->insert($newMeasure);
                        }

                    }
                }
            });
    }
}