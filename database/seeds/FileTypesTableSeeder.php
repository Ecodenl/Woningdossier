<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

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
                'name' => [
                    'nl' => 'Alle ingevulde gegevens, met adresgegevens',
                ],
                'content_type' => 'text/csv',
                'short' => 'total-report',
            ],
            [
                'name' => [
                    'nl' => 'Alle ingevulde gegevens, zonder adresgegevens',
                ],
                'content_type' => 'text/csv',
                'short' => 'total-report-anonymized',
            ],
            [
                'name' => [
                    'nl' => 'Actieplan per maatregel, met adresgegevens',
                ],
                'content_type' => 'text/csv',
                'short' => 'measure-report',
            ],
            [
                'name' => [
                    'nl' => 'Actieplan per maatregel, zonder adresgegevens',
                ],
                'content_type' => 'text/csv',
                'short' => 'measure-report-anonymized',
            ],
            [
                'name' => [
                    'nl' => 'PDF Rapportage',
                ],
                'content_type' => 'application/pdf',
                'short' => 'pdf-report',
            ],
            [
                'name' => [
                    'nl' => 'Antwoorden van de bewoners op de custom vragenlijsten, met alle adresgegevens',
                ],
                'content_type' => 'text/csv',
                'short' => 'custom-questionnaire-report',
            ],
            [
                'name' => [
                    'nl' => 'Antwoorden van de bewoners op de custom vragenlijsten, zonder adresgegevens',
                ],
                'content_type' => 'text/csv',
                'short' => 'custom-questionnaire-report-anonymized',
            ],
        ];

        foreach ($fileTypes as $fileType) {
            DB::table('file_types')->insert([
                'name' => json_encode($fileType['name']),
                'file_type_category_id' => $fileTypeCategory->id,
                'short' => $fileType['short'],
                'content_type' => $fileType['content_type'],
            ]);
        }
    }
}
