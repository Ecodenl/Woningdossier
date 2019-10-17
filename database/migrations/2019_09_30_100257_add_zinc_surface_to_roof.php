<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddZincSurfaceToRoof extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Add zinc column to building_roof_types table
        if (!Schema::hasColumn('building_roof_types', 'zinc_surface')){
            Schema::table('building_roof_types', function (Blueprint $table) {
                $table->integer('zinc_surface')->after('insulation_roof_surface')->unsigned()->nullable()->default(null);
            });
        }

        // Add zinc surface translations for pitched and flat roofs. (title & help)
        $group = 'roof-insulation';

        $step = DB::table('steps')->where('slug', '=', $group)->first();
        if ($step instanceof \stdClass){
            $step_id = $step->id;
            // proceed
            $keys = [
                // pitched
                'current-situation.insulation-pitched-zinc-surface.help' => [
                    'nl' => '',
                ],
                'current-situation.insulation-pitched-zinc-surface.title' => [
                    'nl' => 'Zink oppervlak hellend dak',
                ],
                // flat
                'current-situation.insulation-flat-zinc-surface.help' => [
                    'nl' => '',
                ],
                'current-situation.insulation-flat-zinc-surface.title' => [
                    'nl' => 'Zink oppervlak plat dak',
                ],
            ];

            foreach($keys as $key => $text) {
                $text = json_encode($text);
                if (!DB::table('language_lines')->where('group', $group)->where('key', $key)->exists()) {
                    if (stristr($key, '.title') === false) {
                        $help_language_line_id = null;
                    }
                    $help_language_line_id = DB::table('language_lines')->insertGetId(compact('group',
                        'key', 'text', 'step_id', 'help_language_line_id'));
                }
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
        // We don't want this as we rely on it during calculations
        //Schema::table('building_roof_types',function (Blueprint $table){
        //    $table->dropColumn('zinc_surface');
        //});
    }
}
