<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateToolCalculationResultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tool_calculation_results', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->json('name');
            $table->json('help_text')->nullable()->default(null);
            $table->string('short');
            $table->string('unit_of_measure');
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
        Schema::dropIfExists('tool_calculation_results');
    }
}
