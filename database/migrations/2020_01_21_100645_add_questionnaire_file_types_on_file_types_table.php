<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddQuestionnaireFileTypesOnFileTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (DB::table('cooperations')->find(1) instanceof stdClass) {
            $fileTypeCategory = DB::table('file_type_categories')->where('short', 'report')->first();

            $fileTypes = [
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

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        $fileTypes = [
            'custom-questionnaire-report',
            'custom-questionnaire-report-anonymized'
        ];

        foreach ($fileTypes as $fileTypeShort) {
            DB::table('file_types')->where('short', $fileTypeShort)->delete();
        }
    }
}
