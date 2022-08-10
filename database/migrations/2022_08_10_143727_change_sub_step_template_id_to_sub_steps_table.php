<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeSubStepTemplateIdToSubStepsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sub_steps', function (Blueprint $table) {
            $table->unsignedBigInteger('sub_step_template_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sub_steps', function (Blueprint $table) {
            $table->unsignedBigInteger('sub_step_template_id')->nullable(false)->change();
        });
    }
}
