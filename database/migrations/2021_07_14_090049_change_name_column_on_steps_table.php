<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeNameColumnOnStepsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {


        // first change it to a default string so we can set json content
        Schema::table('steps', function ($table) {
            $table->string('name')->change();
        });

        // even though we dont need em, we cant throw em away just yet.
        foreach (DB::table('steps')->get() as $step) {
            if (\Illuminate\Support\Str::isUuid($step->name)) {
                $trans = DB::table('translations')->where('key', $step->name)->where('language', 'nl')->first();
                DB::table('steps')->where('id', $step->id)->update(['name' => json_encode(['nl' => $trans->translation])]);
            }
        }

        // now actually pomp it to json
        Schema::table('steps', function ($table) {
            $table->json('name')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
