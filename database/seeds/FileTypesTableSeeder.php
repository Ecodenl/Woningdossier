<?php

use Illuminate\Database\Seeder;

class FileTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $fileTypeCategory = DB::table('file_type_categories')->where('short', 'report')->first();

        $fileTypes = [
            [
                'names' => [
                    'nl' => 'Alle ingevulde gegevens, met adresgegevens'
                ],
                'short' => 'total-report'
            ],
            [
                'names' => [
                    'nl' => 'Alle ingevulde gegevens, zonder adresgegevens'
                ],
                'short' => 'total-report-anonymized'
            ],
            [
                'names' => [
                    'nl' => 'Actieplan per maatregel, met adresgegevens'
                ],
                'short' => 'measure-report'
            ],
            [
                'names' => [
                    'nl' => 'Actieplan per maatregel, zonder adresgegevens'
                ],
                'short' => 'measure-report-anonymized'
            ],
            [
                'names' => [
                    'nl' => 'Antwoorden van de bewoners op de custom vragenlijsten, met alle adresgegevens'
                ],
                'short' => 'custom-questionnaires-report'
            ],
            [
                'names' => [
                    'nl' => 'Antwoorden van de bewoners op de custom vragenlijsten, zonder adresgegevens'
                ],
                'short' => 'custom-questionnaires-report-anonymized'
            ],

        ];

        foreach ($fileTypes as $fileType) {
            $uuid = \App\Helpers\Str::uuid();

            // create the translations
            foreach ($fileType['names'] as $locale => $translation) {
                DB::table('translations')->insert([
                    'key' => $uuid,
                    'language' => $locale,
                    'translation' => $translation,
                ]);
            }
            // create the file type itself
            DB::table('file_types')->insert([
                'name' => $uuid,
                'file_type_category_id' => $fileTypeCategory->id,
                'short' => $fileType['short']
            ]);
        }
    }
}
