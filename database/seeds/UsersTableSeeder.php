<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $accounts = [
            [
                'email'    => 'demo@example.org',
                'password' => Hash::make('secret'),
                'is_admin' => true,

                'users' => [
                    [
                        'first_name' => 'Demo',
                        'last_name'  => 'Account',
                    ],
                ],
            ],
        ];

        /** @var \stdClass $cooperation */
        $cooperation = DB::table('cooperations')->where('slug',
            'hoom')->first();

        foreach ($accounts as $account) {
            $users = $account['users'];
            unset($account['users']);

            DB::table('accounts')->updateOrInsert(['email' => $account['email']], $account);
            $row = DB::table('accounts')->where('email', '=', $account['email'])->first();

            $accountId = $row->id;

            foreach ($users as $user) {
                $user['cooperation_id'] = null;
                if ($cooperation instanceof stdClass) {
                    $user['cooperation_id'] = $cooperation->id;
                }
                $user['account_id'] = $accountId;
                DB::table('users')->insert($user);
            }
        }
    }
}
