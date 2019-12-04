<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStepCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('step_comments', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('building_id');
            $table->foreign('building_id')->references('id')->on('buildings')->onDelete(null);

            $table->unsignedInteger('input_source_id');
            $table->foreign('input_source_id')->references('id')->on('input_sources')->onDelete(null);

            $table->unsignedInteger('step_id');
            $table->foreign('step_id')->references('id')->on('steps')->onDelete(null);

            $table->longText('comment');

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
        Schema::dropIfExists('step_comments');
    }
}
