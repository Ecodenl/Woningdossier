<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVisibleColumnToUserActionPlanAdvicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasColumn('user_action_plan_advices', 'visible')) {
            Schema::table('user_action_plan_advices', function (Blueprint $table) {
                $table->boolean('visible')->after('category')->default(false);
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
        if (Schema::hasColumn('user_action_plan_advices', 'visible')) {
            Schema::table('user_action_plan_advices', function (Blueprint $table) {
                $table->dropColumn('visible');
            });
        }
    }
}
