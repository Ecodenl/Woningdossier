<?php

use Illuminate\Database\Seeder;

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
                'names' => [
                    'nl' => 'Berichten',
                    'en' => 'Messages',
                ],
                'short' => 'private-message',
            ],
        ];

        foreach ($notificationTypes as $notificationType) {
            if ( ! DB::table('notification_types')->where('short',
                    $notificationType['short'])->first() instanceof stdClass) {


                $uuid = \App\Helpers\Str::uuid();
                foreach ($notificationType['names'] as $locale => $name) {
                    \DB::table('translations')->insert([
                        'key'         => $uuid,
                        'language'    => $locale,
                        'translation' => $name,
                    ]);
                }

                DB::table('notification_types')->insert([
                    'name'  => $uuid,
                    'short' => $notificationType['short']
                ]);
            }
        }
    }
}
