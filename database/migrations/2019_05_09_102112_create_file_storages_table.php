<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
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

            $table->unsignedInteger('building_id')->nullable();
            $table->foreign('building_id')->references('id')->on('buildings')->onDelete('cascade');

            $table->unsignedInteger('questionnaire_id')->nullable();
            $table->foreign('questionnaire_id')->references('id')->on('questionnaires')->onDelete('cascade');

            $table->unsignedInteger('input_source_id')->nullable()->default(1);
            $table->foreign('input_source_id')->references('id')->on('input_sources')->onDelete('cascade');

            $table->unsignedInteger('file_type_id');
            $table->foreign('file_type_id')->references('id')->on('file_types')->onDelete('cascade');
            
            $table->string('filename');

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
};
