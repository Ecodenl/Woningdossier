<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateTranslationOnTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $step = DB::table('steps')->where('short', 'ventilation')->first();

        DB::table('translations')
            ->where('key', $step->name)
            ->where('language', 'nl')
            ->update([
                'translation' => 'Ventilatie'
            ]);

        DB::table('translations')
            ->where('key', $step->name)
            ->where('language', 'en')
            ->update([
                'translation' => 'Ventilation'
            ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $step = DB::table('steps')->where('short', 'ventilation')->first();

        DB::table('translations')
            ->where('key', $step->name)
            ->where('language', 'nl')
            ->update([
                'translation' => 'Ventilatie informatie'
            ]);

        DB::table('translations')
            ->where('key', $step->name)
            ->where('language', 'en')
            ->update([
                'translation' => 'Ventilation information'
            ]);
    }
}
