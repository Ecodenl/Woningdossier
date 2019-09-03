<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBasedOnInputSourceIdToFileStoragesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('file_storages', function (Blueprint $table) {
            $table->integer('based_on_input_source_id')->unsigned()->nullable()->default(1)->after('user_id');
            $table->foreign('based_on_input_source_id')->references('id')->on('input_sources')->onDelete('cascade');
        });

        $fileStorages = DB::table('file_storages')->get();

        $residentInputSourceId = DB::table('input_sources')->where('short', 'resident')->first()->id;
        foreach ($fileStorages as $fileStorage) {
            DB::table('file_storages')->where('id', $fileStorage->id)
                ->update([
                    'based_on_input_source_id' => $residentInputSourceId
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
        Schema::table('file_storages', function (Blueprint $table) {
            $table->dropForeign(['based_on_input_source_id']);
            $table->dropColumn('based_on_input_source_id');
        });
    }
}
