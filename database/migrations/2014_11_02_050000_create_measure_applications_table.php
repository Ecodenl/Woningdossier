<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMeasureApplicationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('measure_applications', function (Blueprint $table) {
            $table->increments('id');
            $table->enum('measure_type', ['energy_saving', 'maintenance']);
            $table->uuid('measure_name');
            $table->enum('application', ['place', 'replace', 'remove', 'repair']);
            $table->double('costs', 8, 2);
            $table->uuid('cost_unit');
	        $table->double('minimal_costs', 8, 2);
	        $table->integer('maintenance_interval');
	        $table->uuid('maintenance_unit');
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
        Schema::dropIfExists('measure_applications');
    }
}
