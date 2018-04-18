<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserActionPlanAdvicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_action_plan_advices', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict');

            $table->integer('measure_application_id')->unsigned();
            $table->foreign('measure_application_id')->references('id')->on('measure_applications')->onDelete('restrict');

	        $table->decimal('costs')->nullable();
            $table->decimal('savings_gas')->nullable();
            $table->decimal('savings_electricity')->nullable();
            $table->decimal('savings_money')->nullable();

            $table->integer('year')->nullable();
            $table->boolean('planned')->default(true);
            $table->integer('planned_year')->nullable()->default(null);

            $table->integer('step_id')->unsigned();
            $table->foreign('step_id')->references('id')->on('steps')->onDelete('cascade');

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
        Schema::dropIfExists('user_action_plan_advices');
    }
}
