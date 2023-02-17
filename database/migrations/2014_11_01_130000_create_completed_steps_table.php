<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompletedStepsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if ( ! Schema::hasTable('completed_steps')) {
            Schema::create('completed_steps', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('input_source_id')->nullable()->default(null);
                $table->foreign('input_source_id')->references('id')->on('input_sources')->nullOnDelete();

                $table->integer('building_id')->unsigned();
                $table->foreign('building_id')->references('id')->on('buildings')->onDelete('restrict');

                $table->integer('step_id')->unsigned();
                $table->foreign('step_id')->references('id')->on('steps')->onDelete('restrict');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('completed_steps');
    }
}
