<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropMotivationExtraFromUserEnergyHabitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_energy_habits', function (Blueprint $table) {
           $table->dropColumn('motivation_extra');
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
            $table->longText('motivation_extra')->nullable()->default(null);
        });
    }
}
