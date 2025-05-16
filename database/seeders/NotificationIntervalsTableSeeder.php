<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NotificationIntervalsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
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
                    'nl' => 'Per week',
                    'en' => 'Weekly',
                ],
                'short' => 'weekly',
            ],
            [
                'name' => [
                    'nl' => 'Geen mails ontvangen',
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
