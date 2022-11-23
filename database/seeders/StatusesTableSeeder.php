<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

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
                'name' => [
                    'nl' => 'Actief',
                    'en' => 'Active',
                ],
                'short' => 'active',
            ],
            [
                'name' => [
                    'nl' => 'Inactief',
                    'en' => 'Inactive',
                ],
                'short' => 'inactive',
            ],
            [
                'name' => [
                    'nl' => 'In afwachting',
                    'en' => 'Pending',
                ],
                'short' => 'pending',
            ],
            [
                'name' => [
                    'nl' => 'In uitvoering',
                    'en' => 'In progress',
                ],
                'short' => 'in_progress',
            ],
            [
                'name' => [
                    'nl' => 'Uitgevoerd',
                    'en' => 'Executed',
                ],
                'short' => 'executed',
            ],
            [
                'name' => [
                    'nl' => 'Geen uitvoering',
                    'en' => 'No execution',
                ],
                'short' => 'no_execution',
            ],
        ];

        foreach ($statuses as $order => $status) {
            DB::table('statuses')->updateOrInsert(
                [
                    'short' => $status['short'],
                ],
                [
                    'order' => $order,
                    'name' => json_encode($status['name']),
                    'short' => $status['short'],
               ]
            );
        }
    }
}
