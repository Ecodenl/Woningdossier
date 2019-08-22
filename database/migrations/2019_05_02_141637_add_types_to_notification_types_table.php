<?php

use Illuminate\Database\Migrations\Migration;

class AddTypesToNotificationTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
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
            $uuid = \App\Helpers\Str::uuid();
            foreach ($notificationType['names'] as $locale => $name) {
                \DB::table('translations')->insert([
                    'key'         => $uuid,
                    'language'    => $locale,
                    'translation' => $name,
                ]);
            }

            DB::table('notification_types')->insert([
                'name' => $uuid,
                'short' => $notificationType['short'],
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
        DB::table('translations')
          ->where('key', DB::table('notification_types')->where('short', 'private-message')->first()->name)
          ->delete();

        DB::table('notification_types')->where('short', 'private-message')->delete();
    }
}
