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
        $users = [
            [
                'first_name' => 'Demo',
                'last_name' => 'Account',
                'email' => 'demo@example.org',
                'password' => bcrypt('secret'),
                'is_admin' => true,
            ],
        ];
        /** @var \stdClass $cooperation */
        $cooperation = DB::table('cooperations')->where('slug', 'hoom')->first();
        foreach ($users as $user) {
            $userId = DB::table('users')->insertGetId($user);
            if ($cooperation instanceof stdClass) {
                DB::table('cooperation_user')->insert([
                    'cooperation_id' => $cooperation->id,
                    'user_id'        => $userId,
                ]);
            }
        }
    }
}
