<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFileStoragesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('file_storages', function (Blueprint $table) {

            $table->increments('id');

            $table->unsignedInteger('cooperation_id')->nullable()->default(null);
            $table->foreign('cooperation_id')->references('id')->on('cooperations')->onDelete('cascade');
            
            $table->unsignedInteger('user_id')->nullable()->default(null);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->unsignedInteger('file_type_id');
            $table->foreign('file_type_id')->references('id')->on('file_types')->onDelete('cascade');
            
            $table->string('filename');
            $table->string('content_type');
            
            $table->dateTime('available_until')->nullable()->default(null);

            $table->boolean('is_being_processed')->default('1');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('file_storages');
    }
}
