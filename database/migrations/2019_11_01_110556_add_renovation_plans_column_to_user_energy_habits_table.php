<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRenovationPlansColumnToUserEnergyHabitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_energy_habits', function (Blueprint $table) {
            $table->smallInteger('renovation_plans')->after('amount_water')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_energy_habits', function (Blueprint $table) {
            $table->dropColumn('renovation_plans');
        });
    }
}
