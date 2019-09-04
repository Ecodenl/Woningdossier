<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddInputSourceIdToFileStoragesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('file_storages', function (Blueprint $table) {
            $table->integer('input_source_id')->unsigned()->nullable()->default(1)->after('user_id');
            $table->foreign('input_source_id')->references('id')->on('input_sources')->onDelete('cascade');
        });

        $fileStorages = DB::table('file_storages')->get();

        $cooperationInputSource = DB::table('input_sources')->where('short', 'cooperation')->first();

        if ($cooperationInputSource instanceof stdClass) {

            $cooperationInputSourceId = $cooperationInputSource->id;
            foreach ($fileStorages as $fileStorage) {
                DB::table('file_storages')->where('id', $fileStorage->id)
                    ->update([
                        'input_source_id' => $cooperationInputSourceId
                    ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('file_storages', function (Blueprint $table) {
            $table->dropForeign(['input_source_id']);
            $table->dropColumn('input_source_id');
        });
    }
}
