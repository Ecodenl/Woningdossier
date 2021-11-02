<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveMasterInputSourceRowsFromFileStorages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // TODO: Delete this when ran on production
        $master = \App\Models\InputSource::findByShort(\App\Models\InputSource::MASTER_SHORT);

        if ($master instanceof \App\Models\InputSource) {
            \App\Models\FileStorage::withoutGlobalScopes()->forInputSource($master)->delete();
        }
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
