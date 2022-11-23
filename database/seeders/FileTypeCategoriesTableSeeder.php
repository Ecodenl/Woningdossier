<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FileTypeCategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $fileTypeCategories = [
            [
                'name' => [
                    'nl' => 'Rapportages',
                    'en' => 'Report',
                ],
                'content_type' => 'text/csv',
                'short' => 'report',
            ],
        ];

        foreach ($fileTypeCategories as $fileTypeCategory) {
            DB::table('file_type_categories')->insert([
                'name' => json_encode($fileTypeCategory['name']),
                'short' => $fileTypeCategory['short'],
            ]);
        }
    }
}
