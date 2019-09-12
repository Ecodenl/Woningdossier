<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropContentTypeFromFileStoragesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('file_storages', function (Blueprint $table) {
            $table->dropColumn('content_type');
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
            $table->string('content_type')->after('filename');
        });

        $fileStorages = DB::table('file_storages')->get();

        foreach ($fileStorages as $fileStorage) {
            $fileType = DB::table('file_types')->where('id', $fileStorage->file_type_id)->first();
            DB::table('file_storages')->where('id', $fileStorage->id)->update(['content_type' => $fileType->content_type]);
        }



    }
}
