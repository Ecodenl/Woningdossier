<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCooperationsToAllSteps extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $allCooperations = \App\Models\Cooperation::all();

        $steps = \App\Models\Step::all();

        foreach ($allCooperations as $cooperation) {
            $cooperation->steps()->attach($steps);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $allCooperations = \App\Models\Cooperation::all();

        $steps = \App\Models\Step::all();

        foreach ($allCooperations as $cooperation) {
            $cooperation->steps()->sync($steps);
        }
    }
}
