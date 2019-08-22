<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Collection;

class AddCooperationIdsToCooperationIdColumnOnModelHasRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $users = DB::table('users')->get();

        foreach ($users as $user) {
            /** @var Collection $userCooperations */
            $userCooperations = DB::table('cooperation_user')->where('user_id', '=', $user->id)->get();

            // get the cooperation hoom
            $cooperationHoom = DB::table('cooperations')->where('slug', 'hoom')->first();

            // you know, just in case.
            if ($cooperationHoom instanceof stdClass) {
                // just get the first available cooperation id.
                $cooperationId = $userCooperations->first()->id;

                // if the user is associated with multiple cooperations
                // check if the user is associated with the cooperation hoom, if so get the first cooperation thats not the cooperation hoom
                // because the hoom cooperation is the test cooperation.
                if ($userCooperations->count() > 1 && $userCooperations->contains('id', $cooperationHoom->id)) {
                    $cooperationId = $userCooperations->where('id', '!=', $cooperationHoom->id)->first()->id;
                }

                // update the model_has_roles table with the desired cooperation id
                \DB::table('model_has_roles')
                   ->where('model_id', $user->id)
                   ->where('model_type', get_class($user))
                   ->update(['cooperation_id' => $cooperationId]);
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
        DB::table('model_has_roles')->update(['cooperation_id' => null]);
    }
}
