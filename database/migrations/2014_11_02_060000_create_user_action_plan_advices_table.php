<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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

            $table->string('category')->nullable();
            $table->boolean('visible')->default(false);
            $table->json('costs')->nullable();
            $table->decimal('savings_gas')->nullable();
            $table->decimal('savings_electricity')->nullable();
            $table->decimal('savings_money')->nullable();

            $table->integer('year')->nullable();
            $table->boolean('planned')->default(true);
            $table->integer('planned_year')->nullable()->default(null);

            $table->unsignedInteger('step_id')->nullable()->default(null);
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
