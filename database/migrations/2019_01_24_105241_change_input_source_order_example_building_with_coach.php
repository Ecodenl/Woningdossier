<?php

use Illuminate\Database\Migrations\Migration;

class ChangeInputSourceOrderExampleBuildingWithCoach extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $coach = DB::table('input_sources')->where('short', 'coach')->first();
        $exampleBuilding = DB::table('input_sources')->where('short', 'example-building')->first();

        // if the coach order is higher then the example building, switch them.
        if ($coach->order > $exampleBuilding->order) {
            DB::table('input_sources')
                ->where('short', 'coach')
                ->update([
                    'order' => $exampleBuilding->order,
                ]);

            DB::table('input_sources')
                ->where('short', 'example-building')
                ->update([
                    'order' => $coach->order,
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
        $coach = DB::table('input_sources')->where('short', 'coach')->first();
        $exampleBuilding = DB::table('input_sources')->where('short', 'example-building')->first();

        // if the coach order is higher then the example building, switch them.
        if ($coach->order < $exampleBuilding->order) {
            DB::table('input_sources')
                ->where('short', 'coach')
                ->update([
                    'order' => $exampleBuilding->order,
                ]);

            DB::table('input_sources')
                ->where('short', 'example-building')
                ->update([
                    'order' => $coach->order,
                ]);
        }
    }
}
