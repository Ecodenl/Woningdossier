<?php

use Illuminate\Database\Seeder;

class CrawlspaceAccessesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $items = [
            [
                'names' => [
                    'nl' => 'Ja',
                ],
                'calculate_value' => 0,
                'order' => 0,
            ],
            [
                'names' => [
                    'nl' => 'Nee',
                ],
                'calculate_value' => 2,
                'order' => 1,
            ],
            [
                'names' => [
                    'nl' => 'Gedeeltelijk',
                ],
                'calculate_value' => 1,
                'order' => 2,
            ],
        ];

        foreach ($items as $item) {
            $uuid = \App\Helpers\Str::uuid();
            foreach ($item['names'] as $locale => $name) {
                \DB::table('translations')->insert([
                    'key'         => $uuid,
                    'language'    => $locale,
                    'translation' => $name,
                ]);
            }

            \DB::table('crawlspace_accesses')->insert([
                'name' => $uuid,
                'calculate_value' => $item['calculate_value'],
                'order' => $item['order'],
            ]);
        }
    }
}
