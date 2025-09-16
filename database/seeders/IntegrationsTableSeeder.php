<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class IntegrationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $datas = [
            [
                'name' => 'Econobis',
                'short' => 'econobis',
            ]
        ];

        foreach ($datas as $data) {
            $data = \DB::table('integrations')->updateOrInsert(['short' => $data['short']], $data);
        }
    }
}
