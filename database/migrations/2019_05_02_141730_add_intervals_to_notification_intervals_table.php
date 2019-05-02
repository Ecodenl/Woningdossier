<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIntervalsToNotificationIntervalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $notificationIntervals = [
            [
                'names' => [
                    'nl' => 'Elke dag',
                    'en' => 'Every day',
                ],
                'short' => 'daily',
            ],
            [
                'names' => [
                    'nl' => 'Wekelijks',
                    'en' => 'Weekly',
                ],
                'short' => 'weekly',
            ],
            [
                'names' => [
                    'nl' => 'Geen interesse',
                    'en' => 'No interest',
                ],
                'short' => 'no-interest',
            ],
        ];

        foreach ($notificationIntervals as $notificationInterval) {
            $uuid = \App\Helpers\Str::uuid();
            foreach ($notificationInterval['names'] as $locale => $name) {
                \DB::table('translations')->insert([
                    'key'         => $uuid,
                    'language'    => $locale,
                    'translation' => $name,
                ]);
            }

            DB::table('notification_intervals')->insert([
                'name'  => $uuid,
                'short' => $notificationInterval['short']
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
          ->where('key', DB::table('notification_intervals')->where('short', 'weekly')->first()->name)
          ->delete();

        DB::table('notification_intervals')->where('short', 'weekly')->delete();

        DB::table('translations')
          ->where('key', DB::table('notification_intervals')->where('short', 'daily')->first()->name)
          ->delete();

        DB::table('notification_intervals')->where('short', 'daily')->delete();

        DB::table('translations')
          ->where('key', DB::table('notification_intervals')->where('short', 'no-interest')->first()->name)
          ->delete();

        DB::table('notification_intervals')->where('short', 'no-interest')->delete();

    }
}
