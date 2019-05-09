<?php

use Illuminate\Database\Seeder;

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
                'names' => [
                    'nl' => 'Rapportages',
                    'en' => 'Report'
                ],
                'content_type' => 'text/csv',
                'short' => 'report'
            ],

        ];

        foreach ($fileTypeCategories as $fileTypeCategory) {
            $uuid = \App\Helpers\Str::uuid();

            // create the translations
            foreach ($fileTypeCategory['names'] as $locale => $translation) {
                DB::table('translations')->insert([
                    'key' => $uuid,
                    'language' => $locale,
                    'translation' => $translation,
                ]);
            }
            // create the file type itself
            DB::table('file_type_categories')->insert([
                'name' => $uuid,
                'short' => $fileTypeCategory['short']
            ]);
        }
    }
}
