<?php

use App\Helpers\Str;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMeasureApplicationSplitZinc extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // cost unit creation
        $costUnit = DB::table('translations')
                      ->where('language', '=', 'nl')
                      ->where('translation', '=', 'per m')
                      ->first();

        if ( ! $costUnit) {
            $cuUuid = Str::uuid();
            \DB::table('translations')->insert([
                'key'         => $cuUuid,
                'language'    => 'nl',
                'translation' => 'per m',
            ]);
        } else {
            $cuUuid = $costUnit->key;
        }

        // Pitched
        // Measure name
        DB::table('translations')
          ->where('language', '=', 'nl')
          ->where('translation', '=', 'Zinkwerk')
          ->update(['translation' => 'Zinkwerk hellend dak']);

        $mTrans = DB::table('translations')
                    ->where('language', '=', 'nl')
                    ->where('translation', '=', 'Zinkwerk hellend dak')
                    ->first();

        $mnUuid = $mTrans->key;

        DB::table('measure_applications')
          ->where('short', '=', 'replace-zinc')
            ->update(
                [
                    'measure_name' => $mnUuid,
                    'short' => 'replace-zinc-pitched',
                    'costs' => 100,
                    'cost_unit' => $cuUuid,
                ]
            );

        // Flat
        // Measure name
        $flatMeasureName = DB::table('translations')
            ->where('language', '=', 'nl')
            ->where('translation', '=', 'Zinkwerk plat dak')
            ->first();

        if (!$flatMeasureName){
            $fmnUuid = Str::uuid();
            \DB::table('translations')->insert([
                'key'         => $fmnUuid,
                'language'    => 'nl',
                'translation' => 'Zinkwerk plat dak',
            ]);
        }
        else {
            $fmnUuid = $flatMeasureName->key;
        }

        $maintenanceUnit = DB::table('translations')
            ->where('language', '=', 'nl')
            ->where('translation', '=', 'jaar')
            ->first();

        $step = DB::table('steps')->where('slug', '=', 'roof-insulation')->first();

        DB::table('measure_applications')->updateOrInsert(
            [
                'short' => 'replace-zinc-flat',
            ],
            [
                'measure_type' => 'maintenance',
                'measure_name' => $fmnUuid,
                'application' => 'replace',
                'costs' => 25,
                'cost_unit' => $cuUuid,
                'minimal_costs' => 250,
                'maintenance_interval' => 25,
                'maintenance_unit' => $maintenanceUnit->key,
                'step_id' => $step->id,
            ]
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

    }
}
