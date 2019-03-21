<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddElementValueVeryGoodInsulationToElementValuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        $floorInsulationElement = DB::table('elements')->where('short', 'floor-insulation')->first();

        $uuid = \App\Helpers\Str::uuid();

        $translation = ['key' => $uuid, 'translation' => 'Zeer goede isolatie (meer dan 20 cm isolatie)', 'language' => 'nl'];

        $elementValue = [
            'element_id' => $floorInsulationElement->id,
            'value' => $uuid,
            'order' => 4,
            'calculate_value' => 5
        ];

        \DB::table('translations')->insert($translation);
        \DB::table('element_values')->insert($elementValue);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $floorInsulationElement = DB::table('elements')->where('short', 'floor-insulation')->first();

        $elementValueToDelete = DB::table('element_values')->where('element_id', $floorInsulationElement->id)->first();

        // first delete the translations and then the element value itself
        DB::table('translations')->where('key', $elementValueToDelete->value)->delete();
        // and delete the element_values
        DB::table('element_values')->where('element_id', $floorInsulationElement->id)->delete();

    }
}
