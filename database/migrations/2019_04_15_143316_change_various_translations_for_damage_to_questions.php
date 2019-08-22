<?php

use Illuminate\Database\Migrations\Migration;

class ChangeVariousTranslationsForDamageToQuestions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $woodRotALittle = DB::table('wood_rot_statuses')->where('calculate_value', 3)->first();
        if ($woodRotALittle instanceof stdClass) {
            DB::table('translations')->where('key', $woodRotALittle->name)->update([
                'translation' => 'Een beetje',
            ]);
        }

        $woodRotYes = DB::table('wood_rot_statuses')->where('calculate_value', 1)->first();
        if ($woodRotYes instanceof stdClass) {
            DB::table('translations')->where('key', $woodRotYes->name)->update([
                'translation' => 'Ja',
            ]);
        }

        $paintWorkALittle = DB::table('paintwork_statuses')->where('calculate_value', 3)->first();
        if ($paintWorkALittle instanceof stdClass) {
            DB::table('translations')->where('key', $paintWorkALittle->name)->update([
                'translation' => 'Een beetje',
            ]);
        }

        $paintWorkYes = DB::table('paintwork_statuses')->where('calculate_value', 1)->first();
        if ($paintWorkYes instanceof stdClass) {
            DB::table('translations')->where('key', $paintWorkYes->name)->update([
                'translation' => 'Ja',
            ]);
        }

        $facadeDamagedPaintWorksALittle = DB::table('facade_damaged_paintworks')->where('calculate_value', 3)->first();
        if ($facadeDamagedPaintWorksALittle instanceof stdClass) {
            DB::table('translations')->where('key', $facadeDamagedPaintWorksALittle->name)->update([
                'translation' => 'Een beetje',
            ]);
        }

        $facadeDamagedPaintWorksYes = DB::table('facade_damaged_paintworks')->where('calculate_value', 5)->first();
        if ($facadeDamagedPaintWorksYes instanceof stdClass) {
            DB::table('translations')->where('key', $facadeDamagedPaintWorksYes->name)->update([
                'translation' => 'Ja',
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
        $woodRotALittle = DB::table('wood_rot_statuses')->where('calculate_value', 3)->first();
        if ($woodRotALittle instanceof stdClass) {
            DB::table('translations')->where('key', $woodRotALittle->name)->update([
                'translation' => 'Ja, een beetje',
            ]);
        }

        $woodRotYes = DB::table('wood_rot_statuses')->where('calculate_value', 1)->first();
        if ($woodRotYes instanceof stdClass) {
            DB::table('translations')->where('key', $woodRotYes->name)->update([
                'translation' => 'Ja, heel erg',
            ]);
        }

        $paintWorkALittle = DB::table('paintwork_statuses')->where('calculate_value', 3)->first();
        if ($paintWorkALittle instanceof stdClass) {
            DB::table('translations')->where('key', $paintWorkALittle->name)->update([
                'translation' => 'Ja, een beetje',
            ]);
        }

        $paintWorkYes = DB::table('paintwork_statuses')->where('calculate_value', 1)->first();
        if ($paintWorkYes instanceof stdClass) {
            DB::table('translations')->where('key', $paintWorkYes->name)->update([
                'translation' => 'Ja, heel erg',
            ]);
        }

        $facadeDamagedPaintWorksALittle = DB::table('facade_damaged_paintworks')->where('calculate_value', 3)->first();
        if ($facadeDamagedPaintWorksALittle instanceof stdClass) {
            DB::table('translations')->where('key', $facadeDamagedPaintWorksALittle->name)->update([
                'translation' => 'Ja, een beetje',
            ]);
        }

        $facadeDamagedPaintWorksYes = DB::table('facade_damaged_paintworks')->where('calculate_value', 5)->first();
        if ($facadeDamagedPaintWorksYes instanceof stdClass) {
            DB::table('translations')->where('key', $facadeDamagedPaintWorksYes->name)->update([
                'translation' => 'Ja, heel erg',
            ]);
        }
    }
}
