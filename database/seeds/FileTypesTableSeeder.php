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
                'content_type' => 'text/csv',
                'short' => 'total-report'
            ],
            [
                'names' => [
                    'nl' => 'Alle ingevulde gegevens, zonder adresgegevens'
                ],
                'content_type' => 'text/csv',
                'short' => 'total-report-anonymized'
            ],
            [
                'names' => [
                    'nl' => 'Actieplan per maatregel, met adresgegevens'
                ],
                'content_type' => 'text/csv',
                'short' => 'measure-report'
            ],
            [
                'names' => [
                    'nl' => 'Actieplan per maatregel, zonder adresgegevens'
                ],
                'content_type' => 'text/csv',
                'short' => 'measure-report-anonymized'
            ],
            [
                'names' => [
                    'nl' => 'PDF Rapportage'
                ],
                'content_type' => 'application/pdf',
                'short' => 'pdf-report'
            ],
            [
                'names' => [
                    'nl' => 'Antwoorden van de bewoners op de custom vragenlijsten, met alle adresgegevens'
                ],
                'content_type' => 'text/csv',
                'short' => 'custom-questionnaire-report'
            ],
            [
                'names' => [
                    'nl' => 'Antwoorden van de bewoners op de custom vragenlijsten, zonder adresgegevens'
                ],
                'content_type' => 'text/csv',
                'short' => 'custom-questionnaire-report-anonymized'
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
                'short' => $fileType['short'],
                'content_type' => $fileType['content_type']
            ]);
        }
    }
}
