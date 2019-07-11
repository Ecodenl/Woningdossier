<?php

use Illuminate\Database\Seeder;

class StatusesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $statuses = [
            [
                'names' => [
                    'nl' => 'Actief',
                    'en' => 'Active',
                ],
                'short' => 'active'
            ],
            [
                'names' => [
                    'nl' => 'Inactief',
                    'en' => 'Inactive',
                ],
                'short' => 'inactive'
            ],
            [
                'names' => [
                    'nl' => 'In afwachting',
                    'en' => 'Pending',
                ],
                'short' => 'pending'
            ],
            [
                'names' => [
                    'nl' => 'In uitvoering',
                    'en' => 'In progress',
                ],
                'short' => 'in_progress'
            ],
            [
                'names' => [
                    'nl' => 'Uitgevoerd',
                    'en' => 'Executed',
                ],
                'short' => 'executed'
            ],
            [
                'names' => [
                    'nl' => 'Geen uitvoering',
                    'en' => 'No execution',
                ],
                'short' => 'no_execution'
            ],
        ];


        foreach ($statuses as $order => $status) {
            $uuid = \App\Helpers\Str::uuid();
            foreach ($status['names'] as $locale => $name) {
                \DB::table('translations')->insert([
                    'key'         => $uuid,
                    'language'    => $locale,
                    'translation' => $name,
                ]);
            }

            \DB::table('statuses')->insert([
                'order' => $order,
                'name' => $uuid,
                'short' => $status['short'],
            ]);
        }

        $this->command->info('StatusesTableSeeder: done');
    }
}
