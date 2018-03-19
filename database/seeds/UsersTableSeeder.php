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
	        ],
        ];
        foreach($users as $user){
        	DB::table('users')->insert($user);
        }
    }
}
