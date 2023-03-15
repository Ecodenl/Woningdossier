<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVariousColumnsToUserActionPlanAdvicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_action_plan_advices', function (Blueprint $table) {
            if (!Schema::hasColumn('user_action_plan_advices', 'subsidy_available')) {
                $table->boolean('subsidy_available')->default(0)->after('visible');
            }
            if (!Schema::hasColumn('user_action_plan_advices', 'loan_available')) {
                $table->boolean('loan_available')->default(0)->after('subsidy_available');
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
        Schema::table('user_action_plan_advices', function (Blueprint $table) {
            if (Schema::hasColumn('user_action_plan_advices', 'subsidy_available')) {
                $table->dropColumn('subsidy_available');
            }
            if (Schema::hasColumn('user_action_plan_advices', 'loan_available')) {
                $table->dropColumn('loan_available');
            }
        });
    }
}
