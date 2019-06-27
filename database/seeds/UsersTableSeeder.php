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
            $user['cooperation_id'] = null;
            if ($cooperation instanceof stdClass) {
                $user['cooperation_id'] = $cooperation->id;
            }
            DB::table('users')->insert($user);
        }
    }
}
