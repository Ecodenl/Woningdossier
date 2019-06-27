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


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Put column back
        if (!Schema::hasColumn('model_has_roles', 'cooperation_id')){
            Schema::table('model_has_roles', function (Blueprint $table) {
                $table->unsignedInteger('cooperation_id')->nullable()->after('model_id');
                $table->foreign('cooperation_id')->references('id')->on('cooperations')->onDelete('cascade');
            });
        }

        // Set rights back
        // todo

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
