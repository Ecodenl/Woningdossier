<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOrderColumnOnVariousTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasColumn('energy_labels', 'order')) {
            Schema::table('energy_labels', function (Blueprint $table) {
                $table->integer('order')->after('calculate_value');
            });
        }
        if (! Schema::hasColumn('user_action_plan_advices', 'order')) {
            Schema::table('user_action_plan_advices', function (Blueprint $table) {
                $table->integer('order')->after('user_action_plan_advisable_id')->default(0);
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('energy_labels', 'order')) {
            Schema::table('energy_labels', function (Blueprint $table) {
                $table->dropColumn('order');
            });
        }
        if (Schema::hasColumn('user_action_plan_advices', 'order')) {
            Schema::table('user_action_plan_advices', function (Blueprint $table) {
                $table->dropColumn('order');
            });
        }
    }
}
