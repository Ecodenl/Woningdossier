<?php

use Illuminate\Database\Migrations\Migration;

class AddFileTypeToFileTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $fileTypeCategory = DB::table('file_type_categories')->where('short', 'report')->first();

        $fileTypes = [
            [
                'names' => [
                    'nl' => 'PDF Rapportage',
                ],
                'short' => 'pdf-report',
            ],
        ];

        if ($fileTypeCategory instanceof stdClass) {
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
    }
}
