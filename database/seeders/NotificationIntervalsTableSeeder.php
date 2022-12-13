<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NotificationIntervalsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $notificationIntervals = [
            [
                'name' => [
                    'nl' => 'Elke dag',
                    'en' => 'Every day',
                ],
                'short' => 'daily',
            ],
            [
                'name' => [
                    'nl' => 'Wekelijks',
                    'en' => 'Weekly',
                ],
                'short' => 'weekly',
            ],
            [
                'name' => [
                    'nl' => 'Geen interesse',
                    'en' => 'No interest',
                ],
                'short' => 'no-interest',
            ],
        ];

        foreach ($notificationIntervals as $notificationInterval) {
            // only create it when there is no interval.
            if (! DB::table('notification_intervals')->where('short', $notificationInterval['short'])->first() instanceof \stdClass) {
                DB::table('notification_intervals')->insert([
                    'name'  => json_encode($notificationInterval['name']),
                    'short' => $notificationInterval['short'],
                ]);
            }
        }
    }
}
