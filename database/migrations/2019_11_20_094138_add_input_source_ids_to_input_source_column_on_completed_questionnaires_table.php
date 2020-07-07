<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddInputSourceIdsToInputSourceColumnOnCompletedQuestionnairesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $residentInputSource = DB::table('input_sources')->where('short', \App\Models\InputSource::RESIDENT_SHORT)->first();

        if ($residentInputSource instanceof stdClass) {
            DB::table('completed_questionnaires')->update([
                'input_source_id' => $residentInputSource->id
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
        //
    }
}
