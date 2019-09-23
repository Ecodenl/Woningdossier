<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddContentTypeToFileTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('file_types', function (Blueprint $table) {
            $table->string('content_type')->after('short');
        });

        $fileTypes = [
            [
                'short' => 'total-report',
                'content_type' => 'text/csv'
            ],
            [
                'short' => 'total-report-anonymized',
                'content_type' => 'text/csv'
            ],
            [
                'short' => 'measure-report',
                'content_type' => 'text/csv'
            ],
            [
                'short' => 'measure-report-anonymized',
                'content_type' => 'text/csv'
            ],
            [
                'short' => 'pdf-report',
                'content_type' => 'application/pdf'
            ]
        ];

        foreach ($fileTypes as $fileType) {
            DB::table('file_types')->where('short', $fileType['short'])->update(['content_type' => $fileType['content_type']]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('file_types', function (Blueprint $table) {
            $table->dropColumn('content_type');
        });
    }
}
