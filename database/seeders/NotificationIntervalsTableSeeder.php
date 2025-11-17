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
                'order' => 1,
            ],
            [
                'name' => [
                    'nl' => 'Per week',
                    'en' => 'Weekly',
                ],
                'short' => 'weekly',
                'order' => 2,
            ],
            [
                'name' => [
                    'nl' => 'Geen mails ontvangen',
                    'en' => 'No interest',
                ],
                'short' => 'no-interest',
                'order' => 3,
            ],
            [
                'name' => [
                    'nl' => 'Direct',
                    'en' => 'Directly',
                ],
                'short' => 'direct',
                'order' => 0,
            ],
        ];

        foreach ($notificationIntervals as $i => $notificationInterval) {
            // only create it when there is no interval.
            DB::table('notification_intervals')->updateOrInsert([
                'short' => $notificationInterval['short'],
            ], [
                'name' => json_encode($notificationInterval['name']),
                'order' => $notificationInterval['order'] ?? $i,
            ]);
        }
    }
}
