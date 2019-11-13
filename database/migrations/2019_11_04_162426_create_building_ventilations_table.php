<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBuildingVentilationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('building_ventilations', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('input_source_id')->unsigned()->nullable()->default(1);
            $table->foreign('input_source_id')->references('id')->on('input_sources')->onDelete('cascade');

            $table->integer('building_id')->unsigned();
            $table->foreign('building_id')->references('id')->on('buildings')->onDelete('cascade');

            $table->json('how')->nullable();
            $table->json('living_situation')->nullable();
            $table->json('usage')->nullable();

            $table->timestamps();
        });

        // Update step (if needed)
        $step = DB::table('steps')->where('slug', '=',
            'ventilation-information')->first();
        if ($step instanceof \stdClass) {
            DB::table('steps')->where('id', '=',
                $step->id)->update(['slug' => 'ventilation']);
            // and remove the progresses as we need users to fill this step
            DB::table('completed_steps')->where('step_id', '=',
                $step->id)->delete();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('building_ventilations');
        $step = DB::table('steps')->where('slug', '=', 'ventilation')->first();
        if ($step instanceof \stdClass) {
            DB::table('steps')->where('id', '=',
                $step->id)->update(['slug' => 'ventilation-information']);
            // There's no revert option needed for the down method
        }
    }
}
