<?php

use Illuminate\Database\Seeder;

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
            if ( ! DB::table('notification_intervals')->where('short',
                $notificationInterval['short']->first() instanceof stdClass)) {

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
    }
}
