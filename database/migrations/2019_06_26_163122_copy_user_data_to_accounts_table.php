<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CopyUserDataToAccountsTable extends Migration
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
            $accountId = DB::table('accounts')->insertGetId([
                'email' => $user->email,
                'password' => $user->password,
                'remember_token' => $user->remember_token,
                'confirm_token' => $user->confirm_token,
                'old_email_token' => $user->old_email_token,
                'old_email' => $user->old_email,
                'is_admin' => $user->is_admin
            ]);

            DB::table('users')
              ->where('id', $user->id)
              ->update([
                  'account_id' => $accountId
              ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $accounts = DB::table('accounts')->get();

        foreach ($accounts as $account) {
            DB::table('users')->where('account_id', $account->id)->update([
                'email' => $account->email,
                'password' => $account->password,
                'remember_token' => $account->remember_token,
                'confirm_token' => $account->confirm_token,
                'old_email_token' => $account->old_email_token,
                'old_email' => $account->old_email,
                'is_admin' => $account->is_admin
            ]);
        }
    }
}
