<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
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
                    'nl' => 'PDF Raportage'
                ],
                'short' => 'pdf-report'
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

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
