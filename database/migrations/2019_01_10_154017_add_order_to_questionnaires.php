<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOrderToQuestionnaires extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('questionnaires', function (Blueprint $table) {
            if (!Schema::hasColumn('questionnaires', 'order')) {
                $table->integer('order')->after('cooperation_id')->default(0);
            }

            $steps = DB::table('steps')->get();

            foreach ($steps as $step) {
                $stepQuestionnaires = DB::table('questionnaires')->where('step_id', $step->id)->get();
                foreach ($stepQuestionnaires as $order => $questionnaire) {
                    DB::table('questionnaires')->where('id', $questionnaire->id)->update(['order' => $order]);
                }
            }

        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('questionnaires', function (Blueprint $table) {
            $table->dropColumn('order');
        });
    }
}
