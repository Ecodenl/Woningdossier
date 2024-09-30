<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('user_action_plan_advices', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict');

            $table->integer('input_source_id')->unsigned()->nullable()->default(1);
            $table->foreign('input_source_id')->references('id')->on('input_sources')->onDelete('set null');

            $table->string('user_action_plan_advisable_type');
            $table->unsignedBigInteger('user_action_plan_advisable_id');
            $table->index('user_action_plan_advisable_id', 'user_action_plan_advisable_id_index');
            $table->index('user_action_plan_advisable_type', 'user_action_plan_advisable_type_index');

            $table->string('category')->nullable();
            $table->boolean('visible')->default(false);

            $table->boolean('subsidy_available')->default(0);
            $table->boolean('loan_available')->default(0);

            $table->json('costs')->nullable();
            // Support up to 65 digits (including the 2 floating points)
            $table->decimal('savings_gas', 65)->nullable();
            $table->decimal('savings_electricity', 65)->nullable();
            $table->decimal('savings_money', 65)->nullable();

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
    public function down(): void
    {
        Schema::dropIfExists('user_action_plan_advices');
    }
};
