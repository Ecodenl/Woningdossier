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

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
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
