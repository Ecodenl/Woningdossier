<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NotificationTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $notificationTypes = [
            [
                'name' => [
                    'nl' => 'Berichten',
                    'en' => 'Messages',
                ],
                'short' => 'private-message',
            ],
        ];

        foreach ($notificationTypes as $notificationType) {
            if (! DB::table('notification_types')->where('short',
                    $notificationType['short'])->first() instanceof stdClass) {

                DB::table('notification_types')->insert([
                    'name'  => json_encode($notificationType['name']),
                    'short' => $notificationType['short'],
                ]);
            }
        }
    }
}
