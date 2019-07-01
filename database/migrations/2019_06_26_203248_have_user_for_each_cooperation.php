<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class HaveUserForEachCooperation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if ( ! Schema::hasColumn('users', 'cooperation_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->unsignedInteger('cooperation_id')->nullable()->after('account_id');
                $table->foreign('cooperation_id')->references('id')->on('cooperations')->onDelete('cascade');
            });
        }

        if (Schema::hasTable('cooperation_user')) {
            $combinations = DB::table('cooperation_user')->get();
            foreach ($combinations as $combination) {
                $user = DB::table('users')->where('id', '=',
                    $combination->user_id)->whereNull('cooperation_id')->first();
                if ($user instanceof stdClass) {
                    // update the user
                    DB::table('users')->where('id', '=',
                        $combination->user_id)->update(['cooperation_id' => $combination->cooperation_id]);
                } else {
                    // find the user (for the account ID)
                    $user = DB::table('users')->where('id', '=',
                        $combination->user_id)->first();
                    if ($user instanceof stdClass) {
                        DB::table('users')->insert([
                            'first_name'     => $user->first_name,
                            'last_name'      => $user->last_name,
                            'phone_number'   => $user->phone_number,
                            'mobile'         => $user->mobile,
                            'account_id'     => $user->account_id,
                            'cooperation_id' => $combination->cooperation_id,
                            'created_at'     => $user->created_at,
                            'updated_at'     => $user->updated_at
                        ]);
                    }
                }
                // delete the cooperation_user row
                DB::table('cooperation_user')
                  ->where('user_id', '=', $combination->user_id)
                  ->where('cooperation_id', '=', $combination->cooperation_id)
                  ->delete();
            }

            Schema::drop('cooperation_user');
        }

        // Correct rights

        $modelsWithRoles = DB::table('model_has_roles')->where('model_type',
            '=', 'App\Models\User')->get();
        foreach ($modelsWithRoles as $modelWithRoles) {
            // get related user
            $user = DB::table('users')->find($modelWithRoles->model_id);

            if ($user instanceof stdClass) {
                // get actual user by account_id and cooperation_id
                $sibling = DB::table('users')
                             ->where('account_id', '=', $user->account_id)
                             ->where('cooperation_id', '=',
                                 $modelWithRoles->cooperation_id)
                             ->first();

                if ($sibling instanceof stdClass && $sibling->id != $user->id) {
                    DB::table('model_has_roles')
                      ->where('model_type', '=', 'App\Models\User')
                      ->where('model_id', '=', $modelWithRoles->model_id)
                      ->where('cooperation_id', '=',
                          $modelWithRoles->cooperation_id)
                      ->where('role_id', '=', $modelWithRoles->role_id)
                      ->update(['model_id' => $sibling->id]);
                }
            }

        }

        // Now drop the cooperation column on the roles table
        if (Schema::hasColumn('model_has_roles', 'cooperation_id')){
            Schema::table('model_has_roles', function(Blueprint $table){
                $table->dropForeign(['cooperation_id']);
                $table->dropColumn('cooperation_id');
            });
        }

        // ---------------------------------------------------------------------------------------------------------------
        dump("--------------------------- siblings copy user_action_plan_advice_comments");
        $this->copyTableDataForSiblings('user_action_plan_advice_comments');

        // ---------------------------------------------------------------------------------------------------------------
        dump("--------------------------- siblings copy user_action_plan_advices");
        $this->copyTableDataForSiblings('user_action_plan_advices');

        // ---------------------------------------------------------------------------------------------------------------
        dump("--------------------------- siblings copy user_energy_habits");
        $this->copyTableDataForSiblings('user_energy_habits');

        // ---------------------------------------------------------------------------------------------------------------
        dump("--------------------------- siblings copy user_interests");
        $this->copyTableDataForSiblings('user_interests');

        // ---------------------------------------------------------------------------------------------------------------
        dump("--------------------------- siblings copy user_motivations");
        $this->copyTableDataForSiblings('user_motivations');


        // ---------------------------------------------------------------------------------------------------------------
        dump("Updating questionnaires..");
        $completedQuestionnaires = DB::table('completed_questionnaires')->get();
        foreach( $completedQuestionnaires as $completedQuestionnaire ) {
            $questionnaire = DB::table('questionnaires')->find($completedQuestionnaire->questionnaire_id);
            if ($questionnaire instanceof stdClass) {
                $user = DB::table('users')
                          ->where('id', '=', $completedQuestionnaire->user_id)
                          ->where('cooperation_id', '=', $questionnaire->cooperation_id)
                        ->first();
                if ($user instanceof stdClass) {
                    DB::table('completed_questionnaires')
                      ->where('id', '=', $completedQuestionnaire->id)
                      ->where('questionnaire_id', '=',
                          $completedQuestionnaire->questionnaire_id)
                      ->where('user_id', '=', $completedQuestionnaire->user_id)
                      ->update(['user_id' => $user->id]);
                }
            }
        }


        Schema::drop('building_user_usages');

    }

    /**
     * Creates separate copies a table row for all users of an account.
     *
     * @param string $table
     * @param string $userColumn
     * @param string $tablePrimaryKey
     */
    protected function copyTableDataForSiblings($table, $userColumn = 'user_id', $tablePrimaryKey = 'id')
    {
        $rows = DB::table($table)->get();
        /** @var stdClass $row */
        foreach($rows as $row){
            // get current attached user
            $current = DB::table('users')->find($row->$userColumn);
            if ($current instanceof stdClass){
                // get all siblings for the current attached user
                $siblings = DB::table('users')
                              ->where('account_id', '=', $current->account_id)
                              ->get();
                foreach ($siblings as $sibling) {
                    // only create a new row if the sibling is not the current user
                    if ($sibling->id != $current->id) {
                        $data = (array) $row;
                        $data[$userColumn] = $sibling->id;
                        // if primary key exists: remove it
                        if (array_key_exists($tablePrimaryKey, $data)){
                            unset($data[$tablePrimaryKey]);
                        }
                        dump("Copy " . $table . " " . $current->id . " -> " . $sibling->id);
                        DB::table($table)->insert($data);
                    }
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // put building_user_usages back
        if (!Schema::hasTable('building_user_usages')) {
            Schema::create('building_user_usages', function (Blueprint $table) {
                $table->increments('id');

                $table->integer('building_id')->unsigned()->nullable()->default(null);
                $table->foreign('building_id')->references('id')->on('buildings')->onDelete('restrict');

                $table->integer('input_source_id')->unsigned()->nullable()->default(1);
                $table->foreign('input_source_id')->references('id')->on('input_sources')->onDelete('cascade');

                $table->integer('user_id')->unsigned()->nullable()->default(null);
                $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict');

                $table->integer('usage_percentage')->nullable()->default(null);

                $table->date('start_date')->nullable();
                $table->date('end_date')->nullable();

                $table->timestamps();
            });
        }


        // todo put a lot more stuff back
        //
        //
        //


        // Put column back
        if (!Schema::hasColumn('model_has_roles', 'cooperation_id')){
            Schema::table('model_has_roles', function (Blueprint $table) {
                $table->unsignedInteger('cooperation_id')->nullable()->after('model_id');
                $table->foreign('cooperation_id')->references('id')->on('cooperations')->onDelete('cascade');
            });
        }

        // Set rights back
        // todo
        //
        //
        //

        // Link cooperation and users back
        if ( ! Schema::hasTable('cooperation_user')) {
            Schema::create('cooperation_user', function (Blueprint $table) {
                $table->integer('cooperation_id')->unsigned();
                $table->foreign('cooperation_id')->references('id')->on('cooperations')->onDelete('restrict');
                $table->integer('user_id')->unsigned();
                $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict');
            });
        }

        $users = DB::table('users')->get();
        $users = $users->sortBy('id');
        foreach ($users as $user) {
            // get all users with the same account
            $siblings = DB::table('users')->where('account_id', '=',
                $user->account_id)->get();
            // We know there's at least one, so below can be done safely
            $siblings = $siblings->sortBy('id');
            $first    = $siblings->first();
            foreach ($siblings as $sibling) {
                if ($sibling->id == $first->id) {
                    // Attach the first user to the cooperation
                    if ( ! is_null($first->cooperation_id)) {
                        $cooperationUser = DB::table('cooperation_user')
                                             ->where('user_id', '=', $first->id)
                                             ->where('cooperation_id', '=',
                                                 $first->cooperation_id)
                                             ->first();
                        // only insert if this combination does not exist yet
                        if (is_null($cooperationUser)) {
                            DB::table('cooperation_user')->insert(
                                [
                                    'user_id'        => $first->id,
                                    'cooperation_id' => $first->cooperation_id,
                                ]);
                        }
                    }
                } else {
                    DB::table('cooperation_user')->insert(
                        [
                            'user_id'        => $first->id,
                            'cooperation_id' => $sibling->cooperation_id,
                        ]);
                    DB::table('users')->where('id', '=',
                        $sibling->id)->delete();
                }
            }
        }

        if (Schema::hasColumn('users', 'cooperation_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropForeign(['cooperation_id']);
                $table->dropColumn('cooperation_id');
            });
        }
    }
}
