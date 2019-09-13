<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBuildingIdToFileStoragesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('file_storages', function (Blueprint $table) {
            $table->integer('building_id')->unsigned()->nullable()->default(null)->after('cooperation_id');
            $table->foreign('building_id')->references('id')->on('buildings')->onDelete('cascade');
        });

        $fileStorages = DB::table('file_storages')->get();

        foreach ($fileStorages as $fileStorage) {
            if ($fileStorage instanceof stdClass && !empty($fileStorage->user_id)) {
                $buildingId = DB::table('buildings')->where('user_id', $fileStorage->user_id)->first()->id;
                DB::table('file_storages')->where('user_id', $fileStorage->user_id)->update([
                    'building_id' => $buildingId
                ]);
            }
        }

        Schema::table('file_storages', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('file_storages', function (Blueprint $table) {
            $table->integer('user_id')->unsigned()->nullable()->default(null)->after('cooperation_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        $fileStorages = DB::table('file_storages')->get();

        foreach ($fileStorages as $fileStorage) {
            if ($fileStorage instanceof stdClass && !empty($fileStorage->building_id)) {
                $userId = DB::table('buildings')->where('id', $fileStorage->building_id)->first()->user_id;
                DB::table('file_storages')->where('building_id', $fileStorage->building_id)->update([
                    'user_id' => $userId
                ]);
            }
        }

        Schema::table('file_storages', function (Blueprint $table) {
            $table->dropForeign(['building_id']);
            $table->dropColumn('building_id');
        });
    }
}
