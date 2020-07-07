<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveOldCommentsFromVariousTables extends Migration
{
    /**
     * Run the migrations.
     *
     * Since we added a new step_comments table and migrated to comments to that table
     * we can now safely remove the comments from the old columns
     *
     * @return void
     */
    public function up()
    {
        $tablesThatHaveComments = [
            'user_energy_habits' => 'living_situation_extra',
            'building_features' => 'additional_info',
            'building_elements' => 'extra',
            'building_services' => 'extra',
            'building_insulated_glazings' => 'extra',
            'building_roof_types' => 'extra',
            'building_pv_panels' => 'comment',
            'building_heaters' => 'comment'
        ];

        foreach ($tablesThatHaveComments as $table => $column) {
            // the comment is stored in the extra column inside json key comment
            // else its stored inside the given column, we will just drop that column.
            if ($column == 'extra') {
                foreach (DB::table($table)->get() as $tableData) {
                    $extraValue = json_decode($tableData->extra, true);
                    unset($extraValue['comment']);
                    DB::table($table)
                        ->where('id', $tableData->id)
                        ->update([
                            'extra' => json_encode($extraValue)
                        ]);
                }
            } else {
                Schema::table($table, function (Blueprint $table) use ($column) {
                   $table->dropColumn($column);
                });
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
        //
    }
}
