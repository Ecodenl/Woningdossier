<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FileTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
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
                    'nl' => 'Gegevens van de eenvoudige variant, met adresgegevens',
                ],
                'content_type' => 'text/csv',
                'short' => 'lite-scan-report',
            ],
            [
                'name' => [
                    'nl' => 'Gegevens van de eenvoudige variant, zonder adresgegevens',
                ],
                'content_type' => 'text/csv',
                'short' => 'lite-scan-report-anonymized',
            ],
            [
                'name' => [
                    'nl' => 'Gegevens van de kleine maatregelen, met adresgegevens',
                ],
                'content_type' => 'text/csv',
                'short' => 'small-measures-report',
            ],
            [
                'name' => [
                    'nl' => 'Gegevens van de kleine maatregelen, zonder adresgegevens',
                ],
                'content_type' => 'text/csv',
                'short' => 'small-measures-report-anonymized',
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
            [
                'name' => [
                    'nl' => 'Voorbeeld woning overzicht',
                ],
                'content_type' => 'text/csv',
                'short' => 'example-building-overview',
            ],
        ];

        foreach ($fileTypes as $fileType) {
            DB::table('file_types')->updateOrInsert(
                [
                    'short' => $fileType['short'],
                ],
                [
                    'name' => json_encode($fileType['name']),
                    'file_type_category_id' => $fileTypeCategory->id,
                    'content_type' => $fileType['content_type'],
                ]);
        }
    }
}
