<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCategoryColumnOnUserActionPlanAdvicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasColumn('user_action_plan_advices', 'category')) {
            Schema::table('user_action_plan_advices', function (Blueprint $table) {
                $table->string('category')->after('user_action_plan_advisable_id')->nullable();
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
        if (Schema::hasColumn('user_action_plan_advices', 'category')) {
            Schema::table('user_action_plan_advices', function (Blueprint $table) {
               $table->dropColumn('category');
            });
        }
    }
}
