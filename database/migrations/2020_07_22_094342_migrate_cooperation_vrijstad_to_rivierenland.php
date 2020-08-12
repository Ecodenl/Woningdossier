<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MigrateCooperationVrijstadToRivierenland extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $rivierenLand = DB::table('cooperations')->where('slug', '=', 'hnwr')->first();
        // cooperation to remove.
        $vrijstadEnergie = DB::table('cooperations')->where('slug', '=', 'vrijstadenergie')->first();

        // only migrate the pdf file file types.
        // cvs dont matter at this point
        DB::table('file_types')
            ->select('file_storages.*')
            ->where('content_type', '=', 'application/pdf')
            ->where('cooperation_id', '=', $vrijstadEnergie->id)
            ->leftJoin('file_storages', 'file_types.id', '=', 'file_storages.file_type_id')
            ->update(['cooperation_id' => $rivierenLand->id]);

        // after the update we can delete the remaining file storages
        //DB::table('file_storages')->where('cooperation_id', $vrijstadEnergie->id)->delete();

        // remove the cooperation steps for the old cooperation
        //DB::table('cooperation_steps')->where('cooperation_id', $vrijstadEnergie->id)->delete();

        // update the users to the new cooperation
        DB::table('users')
            ->where('cooperation_id', '=', $vrijstadEnergie->id)
            ->update(['cooperation_id' => $rivierenLand->id]);


        // and finally delete the cooperation
        //DB::table('cooperations')->where('slug', '=', 'vrijstadenergie')->delete();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
