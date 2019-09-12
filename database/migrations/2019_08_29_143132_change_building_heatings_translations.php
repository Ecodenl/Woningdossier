<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeBuildingHeatingsTranslations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        $heated = DB::table('building_heatings')->where('calculate_value', 2)->first();
        if ($heated instanceof stdClass) {
            \DB::table('translations')->where('language', 'nl')->where('key', $heated->name)->update([
                'translation' => 'Verwarmd',
            ]);
        }

        $mediumHeated = DB::table('building_heatings')->where('calculate_value', 3)->first();
        if ($mediumHeated instanceof stdClass) {
            \DB::table('translations')->where('language', 'nl')->where('key', $mediumHeated->name)->update([
                'translation' => 'Matig verwarmd',
            ]);
        }

        $notHeated = DB::table('building_heatings')->where('calculate_value', 4)->first();
        if ($notHeated instanceof stdClass) {
            \DB::table('translations')->where('language', 'nl')->where('key', $notHeated->name)->update([
                'translation' => 'Onverwarmd',
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        $heated = DB::table('building_heatings')->where('calculate_value', 2)->first();
        if ($heated instanceof stdClass) {

            \DB::table('translations')->where('language', 'nl')->where('key', $heated->name)->update([
                'translation' => 'Verwarmd, de meeste radiatoren staan aan',
            ]);
        }

        $mediumHeated = DB::table('building_heatings')->where('calculate_value', 3)->first();
        if ($mediumHeated instanceof stdClass) {

            \DB::table('translations')->where('language', 'nl')->where('key', $mediumHeated->name)->update([
                'translation' => 'Matig verwarmd, de meeste radiatoren staan hoger dan * of een aantal radiatoren staan hoog',
            ]);

        }

        $notHeated = DB::table('building_heatings')->where('calculate_value', 4)->first();
        if ($notHeated instanceof stdClass) {

            \DB::table('translations')->where('language', 'nl')->where('key', $notHeated->name)->update([
                'translation' => 'Onverwarmd, de radiatoren staan op * of uit',
            ]);
        }
    }
}
