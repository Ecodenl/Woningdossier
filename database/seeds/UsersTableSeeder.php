<?php

use Illuminate\Database\Seeder;

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
                'email' => 'demo@example.org',
                'password' => bcrypt('secret'),
                'is_admin' => true,

                'users' => [
                    [
                        'first_name' => 'Demo',
                        'last_name' => 'Account',
                    ],
                ],
            ],
        ];

        /** @var \stdClass $cooperation */
        $cooperation = DB::table('cooperations')->where('slug', 'hoom')->first();

        foreach ($accounts as $account) {
            $users = $account['users'];
            unset($account['users']);

            $accountId = DB::table('accounts')->insertGetId($account);

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
