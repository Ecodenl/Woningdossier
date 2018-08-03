<?php

use Illuminate\Database\Seeder;

class PrivateMessagesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        // well create a new "coach"
        \App\Models\User::create([
            'first_name' => 'Coach',
            'last_name' => 'Demo',
            'email' => 'democoach@example.org',
            'password' => bcrypt('secret'),
        ]);

        $privateMessages = [
            // the request from a user to cooperation for a coach to help
            [
                'title' => 'Coachgesprek aanvraag',
                'message' => 'Hallo, ik wil graag een coachgesprek',
                'from_user_id' => 1,
                'to_cooperation_id' => 1,
                // normally this would be set after a cooperation decided which coach to connect to the user
                // but for testing purposes we do this right now
                'status' => 'gekoppeld aan coach',
            ],
            // the connection between a coach and user
            [
                'title' => 'Coachgesprek aanvraag',
                'message' => 'Goedenmiddag, waarom wilde u een coachgesprek',
                'from_user_id' => 2,
                'to_user_id' => 1,
            ],
            // message back from user to coach
//            [
//                'title' => 'Coachgesprek aanvraag',
//                'message' => 'Ik kom ergens niet bepaald uit, dit heeft te maken met mijn dak thuis.',
//                'from_user_id' => 1,
//                'to_user_id' => 2,
//            ]
        ];

        foreach ($privateMessages as $privateMessage) {
            \App\Models\PrivateMessage::create($privateMessage);
        }
    }
}
